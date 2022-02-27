<?php

namespace Modules\Home\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\MasterBadanHukum;
use App\Models\BbkkpSis\MasterJenisPerusahaan;
use App\Models\BbkkpSis\MasterKabupaten;
use App\Models\BbkkpSis\MasterKecamatan;
use App\Models\BbkkpSis\MasterNegara;
use App\Models\BbkkpSis\MasterProvinsi;
use App\Models\BbkkpSis\SysUser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class AccountController extends Controller
{
    private $url = 'account';

    public function index()
    {
        return view('home::account.profile');
    }

    public function editPassword()
    {
        return view("home::account.change_password");
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'combo-provinsi'  => $this->ajax_combo_provinsi($request),
            'combo-kabupaten' => $this->ajax_combo_kabupaten($request),
            'combo-kecamatan' => $this->ajax_combo_kecamatan($request),
            default           => null,
        };
    }

    private function ajax_combo_provinsi(Request $request)
    {
        $data = MasterProvinsi::select("*");
        // Filter
        if (!$request->q) $data->where('prov_nama', 'LIKE', '%' . $request->q . '%');

        // Sorter
        $data->orderBy('prov_id', 'ASC');

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['prov_id']   = $d->prov_id;
            $x['prov_nama'] = $d->prov_nama;
            array_push($result, $x);
        }

        return response()->json($result);
    }

    private function ajax_combo_kabupaten(Request $request)
    {
        $id = $request->get('id');

        if ($id) {
            $data = MasterKabupaten::select("*")->where('prov_id', $id);
            // Filter
            if (!$request->q) $data->where('kab_nama', 'LIKE', '%' . $request->q . '%');

            // Sorter
            $data->orderBy('kab_nama', 'ASC');

            // Result
            $result = [];
            foreach ($data->get() as $d) {
                $x['kab_id']   = $d->kab_id;
                $x['kab_nama'] = $d->kab_nama;
                array_push($result, $x);
            }

            return response()->json($result);
        }
    }

    private function ajax_combo_kecamatan(Request $request)
    {
        $id = $request->get('id');

        if ($id) {
            $data = MasterKecamatan::select("*")->where('kab_id', $id);
            // Filter
            if (!$request->q) $data->where('kec_nama', 'LIKE', '%' . $request->q . '%');

            // Sorter
            $data->orderBy('kec_nama', 'ASC');

            // Result
            $result = [];
            foreach ($data->get() as $d) {
                $x['kec_id']   = $d->kec_id;
                $x['kec_nama'] = $d->kec_nama;
                array_push($result, $x);
            }

            return response()->json($result);
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|min:4',
            'new_password'     => 'required|min:4|confirmed',
        ]);

        if (Hash::check($request['current_password'], Auth::user()->user_password)) {
            Auth::user()->user_password = bcrypt($request['new_password']);
            Auth::user()->save();
            return redirect()->back()->with("message", "Kata sandi berhasil diperbarui");
        } else {
            return redirect()->back()->withErrors(['message' => "Kata sandi sekarang tidak sesuai"]);
        }
    }

    public function editProfile()
    {
        if ((int) session('group_selected') == 3)
        {
            // pelanggan
            $master_badan_hukum      = MasterBadanHukum::all();
            $master_jenis_perusahaan = MasterJenisPerusahaan::all();
            $master_negara           = MasterNegara::orderBy('negara_nama', 'asc')->get();
            $master_provinsi         = MasterProvinsi::orderBy('prov_nama', 'asc')->get();

            $breadcrumbs = [
                new BreadcrumbsStruct('Akun'),
                new BreadcrumbsStruct('Profile', url('/account/profile')),
                new BreadcrumbsStruct('Update Profile'),
            ];

            $data = [
                'url'                     => $this->url,
                'user_data'               => auth()->user(),
                'master_badan_hukum'      => $master_badan_hukum,
                'master_jenis_perusahaan' => $master_jenis_perusahaan,
                'master_negara'           => $master_negara,
                'master_provinsi'         => $master_provinsi,
                'breadcrumbs'             => $breadcrumbs
            ];

            return view('home::account.update_profile')->with($data);
        } else {
            // not pelanggan

            $breadcrumbs = [
                new BreadcrumbsStruct('Akun'),
                new BreadcrumbsStruct('Profile', url('/account/profile')),
                new BreadcrumbsStruct('Update Profile'),
            ];

            $data = [
                'breadcrumbs' => $breadcrumbs
            ];

            return view('home::account.update_profile_user')->with($data);
        }
    }

    public function updateProfile(Request $request)
    {
        // dd($request->all());
        // dd($request->hasFile("avatar"));
        $request->validate([
            'avatar'                => 'sometimes|max:500|mimes:jpeg,jpg,png',
            'fullname'              => 'required',
            'company_name'          => 'required',
            'company_owner_name'    => 'required',
            'company_pimpinan_name' => 'required',
            'company_wakil_name'    => 'required',
            'company_address'       => 'required',
            'company_country'       => 'required',
            'company_province'      => 'required_if:company_country,3',
            'company_kabupaten'     => 'required_if:company_country,3',
            'company_kecamatan'     => 'required_if:company_country,3',
            'company_no_akta'       => 'required',
            'company_badan_hukum'   => 'required',
            'company_jenis'         => 'required',
            'company_telp'          => 'required',
            'company_fax'           => 'required',
            'company_cp'            => 'required',
        ]);

        $user                = Auth::user();
        $user->user_fullname = $request['fullname'];

        if ($request->hasFile("avatar")) {
            $image = $request->file('avatar');
            $img   = Image::make($request->file('avatar')->getRealPath());
            $img->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $imageName = "/images/profiles/" . str_replace(" ", "_", $request->fullname) . '_' . Str::random(5) . '.' . $image->getClientOriginalExtension();
            $img->save(public_path($imageName), 80);
            $user->user_picture = $imageName;
        }

        $user->save();

        $user->sis_pelanggan->cust_nomor_telp           = $request['company_telp'];
        $user->sis_pelanggan->cust_nomor_fax            = $request['company_fax'];
        $user->sis_pelanggan->cust_nomor_hp             = $request['company_cp'];
        $user->sis_pelanggan->cust_nama                 = $request['company_name'];
        $user->sis_pelanggan->jenis_perusahaan_id       = $request['company_jenis'];
        $user->sis_pelanggan->badan_hukum_id            = $request['company_badan_hukum'];
        $user->sis_pelanggan->negara_id                 = $request['company_country'];
        $user->sis_pelanggan->kec_id                    = $request['company_country'] == 3 ? $request['company_kecamatan'] : null;
        $user->sis_pelanggan->kab_id                    = $request['company_country'] == 3 ? $request['company_kabupaten'] : null;
        $user->sis_pelanggan->prov_id                   = $request['company_country'] == 3 ? $request['company_province'] : null;
        $user->sis_pelanggan->cust_alamat               = $request['company_address'];
        $user->sis_pelanggan->cust_nomor_akta_pendirian = $request['company_no_akta'];
        $user->sis_pelanggan->cust_nama_pemilik         = $request['company_owner_name'];
        $user->sis_pelanggan->cust_nama_pimpinan        = $request['company_pimpinan_name'];
        $user->sis_pelanggan->cust_nama_wakil_manajemen = $request['company_wakil_name'];
        $user->sis_pelanggan->updated_at                = date('Y-m-d H:i:s');
        $user->sis_pelanggan->save();

        return redirect()->back()->with('message', "Profil berhasil diperbarui");
    }

    public function updateUserProfile(Request $request)
    {
        $request->validate([
            'fullname'  => 'required|string|min:4',
            'email'     => 'required|email|min:4',
            'foto'      => 'sometimes|max:500|mimes:jpeg,jpg,png',
        ]);

        // TODO: check apakah ada email yg kembar
        $currentUser = SysUser::findOrFail(auth()->user()->user_id);
        $newEmail    = SysUser::where("user_email", $request->email)->where('user_email', '<>', $currentUser->user_email)->first();
        if (!empty($newEmail)) return redirect()->back()->withInput($request->all())->withErrors("Email telah digunakan");

        $currentUser->user_fullname = $request->fullname;
        $currentUser->user_email    = $request->email;

        if ($request->hasFile("foto")) {
            $image = $request->file('foto');
            $img   = Image::make($request->file('foto')->getRealPath());
            $img->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $imageName = "/images/profiles/" . str_replace(" ", "_", $request->fullname) . '_' . Str::random(5) . '.' . $image->getClientOriginalExtension();
            $img->save(public_path($imageName), 80);
            $currentUser->user_picture = $imageName;
        }

        if ($currentUser->save()) {
            return redirect()->back()->with("message", "Profil berhasil diperbarui");
        } else {
            return redirect()->back()->withErrors("Gagal memperbarui profil");
        }
    }
}
