<?php

namespace Modules\Auth\Http\Traits;

use App\Models\BbkkpSis\MasterBadanHukum;
use App\Models\BbkkpSis\MasterJenisDokPerusahaan;
use App\Models\BbkkpSis\MasterJenisPerusahaan;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SysUser;
use App\Models\BbkkpSis\SysUserGroup;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

trait SsoTrait
{
    use AuthTraits;

    /*
     * loginSsoSuccess: Set session if an account exists or creates new if not registered
     *
     * @param $accessToken
     * @return mixed
     */
    public function loginSsoSuccess($accessToken)
    {
        $user = Http::withToken($accessToken)->get(sprintf('%s/api/user', config('app.sso.server')));
        $data = $user->json('results');

        if (empty($data)) {
            session()->flush();
            Auth::logout();
            // remove cookie access_token
            return redirect('/auth/sso/redirect')->withoutCookie('access_token');
        }

        session()->put('user_sso', $data);

        $user = SysUser::where('user_email', $data['email'])->first();
        DB::beginTransaction();
        if (empty($user)) {
            $user                  = new SysUser();
            $user->user_email      = strtolower($data['email']);
            $user->user_fullname   = ucwords($data['name']);
            $user->user_password   = null;
            $user->user_token      = null;
            $user->user_is_active  = "yes";
            $user->user_is_banned  = "no";
            $user->user_picture    = '/images/profiles/default.png';
            $user->user_created_at = Carbon::now();
            $user->save();

            $ug                = new SysUserGroup();
            $ug->ug_user_id    = $user->user_id;
            $ug->ug_group_id   = 3;
            $ug->ug_is_default = "yes";
            $ug->ug_created_at = Carbon::now();
            $ug->save();

        }

        $sisPelanggan                            = SisPelanggan::query()->firstOrNew(['user_id' => $user->user_id]);
        $sisPelanggan->user_id                   = $user->user_id;
        $sisPelanggan->cust_nama                 = Arr::get($data, 'detail.nama');
        $sisPelanggan->cust_email                = Arr::get($data, 'detail.surel');
        $sisPelanggan->cust_nomor_telp           = Arr::get($data, 'detail.telepon');
        $sisPelanggan->cust_nomor_fax            = Arr::get($data, 'detail.fax');
        $sisPelanggan->cust_nomor_hp             = Arr::get($data, 'detail.whatsapp');
        $sisPelanggan->cust_alamat               = Arr::get($data, 'detail.alamat');
        $sisPelanggan->cust_nomor_akta_pendirian = Arr::get($data, 'detail.no_akta_pendirian');
        $sisPelanggan->cust_npwp                 = Arr::get($data, 'detail.npwp');
        $sisPelanggan->cust_nama_pemilik         = Arr::get($data, 'detail.pemilik');
        $sisPelanggan->cust_nama_pimpinan        = Arr::get($data, 'detail.pimpinan');
        $sisPelanggan->cust_nama_wakil_manajemen = Arr::get($data, 'detail.pj_nama');
        if (Arr::get($data, 'detail.badan_hukum')) {
            $masterBadanHukum = MasterBadanHukum::query()->where('badan_hukum_nama', Arr::get($data, 'detail.badan_hukum'))->first();
            if ($masterBadanHukum) {
                $sisPelanggan->badan_hukum_id = $masterBadanHukum->badan_hukum_id;
            }
        }

        if (Arr::get($data, 'detail.jenis')) {
            $masterJenisPerusahaan = MasterJenisPerusahaan::query()->where('jenis_perusahaan_nama', Arr::get($data, 'detail.jenis'))->first();
            if ($masterJenisPerusahaan) {
                $sisPelanggan->jenis_perusahaan_id = $masterJenisPerusahaan->jenis_perusahaan_id;
            }
        }

        $documents = [
            'dok_npwp'           => 5, // 5 = NPWP
            'dok_nib'            => 79, // 79 = Nomor Induk Berusaha (NIB)
            'dok_akta_pendirian' => 1, // 1 = Akte Perusahaan
        ];

        foreach ($documents as $docType => $docId) {
            if ($url = Arr::get($data, "detail.$docType")) {
                $masterJenisDokPerusahaan = MasterJenisDokPerusahaan::query()->find($docId);

                // download file
                try {
                    $download = file_get_contents($url);

                    $filePath = sprintf(config("app.path_file_customer"), $sisPelanggan->cust_id);
                    if (!File::exists($filePath)) File::makeDirectory($filePath, 0777, true, true);

                    $fileName = Str::slug($masterJenisDokPerusahaan->jenis_dok_perusahaan_text) . '-' . time() . '.pdf';
                    $path     = $filePath . '/' . $fileName;

                    File::put($path, $download);

                    // get old file path, if exists then delete
                    $oldPath = $sisPelanggan->sis_pelanggan_dokumens()->where('jenis_dok_perusahaan_id', $docId)->value('cust_dok_filepath');
                    if ($oldPath && File::exists($oldPath)) File::delete($oldPath);

                    $sisPelanggan->sis_pelanggan_dokumens()->updateOrCreate([
                        'jenis_dok_perusahaan_id' => $docId,
                    ], [
                        'cust_dok_filepath' => $path,
                    ]);
                } catch (Exception $e) {
                    log_error($e, $sisPelanggan->toArray());
                }

            }
        }

        $sisPelanggan->save();
        DB::commit();

        Auth::loginUsingId($user->user_id);
        Auth::user()->user_last_login = date("Y-m-d H:i:s");
        Auth::user()->save();

        $group_selected      = Auth::user()->user_group->where("ug_is_default", "yes")->first()->ug_group_id;
        $group_selected_name = Auth::user()->user_group->where("ug_is_default", "yes")->first()->group->group_name;
        $this->setAccess($group_selected, $group_selected_name);

        $oneYear = 60 * 24 * 365;
        return redirect()->intended(route('dashboard'))->withCookie('access_token', $accessToken, $oneYear);
    }
}
