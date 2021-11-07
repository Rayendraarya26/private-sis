<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\MasterPegawai;
use App\Models\BbkkpSis\SysGroup;
use App\Models\BbkkpSis\SysUser;
use App\Models\BbkkpSis\SysUserGroup;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class PegawaiController extends Controller
{
    public $module = self::class;
    private $url = 'admin/data/pegawai';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Administrator'),
            new BreadcrumbsStruct('Data Pegawai'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view('admin::data_pegawai.index')->with($parser);
    }

    public function create()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Administrator'),
            new BreadcrumbsStruct('Data Pelanggan', url($this->url)),
            new BreadcrumbsStruct('Tambah'),
        ];

        $groups = SysGroup::whereNotIn('group_id', [1, 3])->get();
        $parse  = ['url' => $this->url, 'groups' => $groups, 'module' => $this->module, 'breadcrumbs' => $breadcrumbs];
        return view('admin::data_pegawai.create')->with($parse);
    }

    public function store(Request $request)
    {
        $uploadedPath = [];
        try {
            $request->validate([
                'nip'           => 'required|string',
                'fullname'      => 'required|string',
                'email'         => 'required|email|min:4|unique:App\Models\BbkkpSis\SysUser,user_email',
                'password'      => 'required|min:4|confirmed',
                'foto'          => 'sometimes|max:500|mimes:jpeg,jpg,png',
                'group'         => 'required',
                'group_default' => 'required',
            ]);
            DB::beginTransaction();
            $newUser                = new SysUser();
            $newUser->user_email    = $request['email'];
            $newUser->user_password = bcrypt($request['password']);
            $newUser->user_fullname = $request['fullname'];

            if ($request->hasFile("foto")) {
                $image = $request->file('foto');
                $img   = Image::make($request->file('foto')->getRealPath());
                $img->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $imageName = "/images/profiles/" . str_replace(" ", "_", $request->fullname) . '_' . Str::random(5) . '.' . $image->getClientOriginalExtension();
                $img->save(public_path($imageName), 80);

                $newUser->user_picture = $imageName;
                array_push($uploadedPath, public_path($imageName));
            }

            $newUser->save();

            // Delete User Group
            SysUserGroup::where("ug_user_id", $newUser->user_id)->delete();
            // Reinsert User Group
            foreach ($request->group as $group) {
                SysUserGroup::create([
                    'ug_user_id'    => $newUser->user_id,
                    'ug_group_id'   => $group,
                    'ug_is_default' => $request->group_default == $group ? 'yes' : 'no'
                ]);
            }

            // Insert to master pegawai
            $pegawai                 = new MasterPegawai();
            $pegawai->user_id        = $newUser->user_id;
            $pegawai->peg_nama       = $newUser->user_fullname;
            $pegawai->peg_alamat     = $request['alamat'];
            $pegawai->peg_telp       = $request['no_telp'];
            $pegawai->peg_nip        = $request['nip'];
            $pegawai->peg_ttd_base64 = $request['signature_base64'];
            $pegawai->peg_status     = "aktif";
            $pegawai->save();

            if ($request->hasFile('signature_file')) {
                $signature = $request->file('signature_file');
                $img       = Image::make($request->file('signature_file')->getRealPath());
                $img->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $filePath = sprintf(config("app.path_file_pegawai"), $pegawai->peg_id);
                if (!File::exists($filePath)) {
                    File::makeDirectory($filePath, 0777, true, true);
                }
                $signatureName = $filePath . '/signature-' . str_replace(" ", "_", $pegawai->peg_nama) . '_' . Carbon::now()->unix() . '.' . $signature->getClientOriginalExtension();
                $img->save(public_path($signatureName), 80);

                $pegawai->peg_ttd_file = $signatureName;
                $pegawai->save();

                array_push($uploadedPath, public_path($signatureName));
            }

            DB::commit();
            return responseJSON(200, [], "Data sukses ditambahkan");
        } catch (Exception $e) {
            DB::rollBack();
            foreach ($uploadedPath as $path) {
                @unlink($path);
            }
            return responseJSON(500, [], $e->getMessage());
        }
    }

    public function edit($id)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Administrator'),
            new BreadcrumbsStruct('Data Pelanggan', url($this->url)),
            new BreadcrumbsStruct('Edit'),
        ];

        $data          = SysUser::with('master_pegawai')->findOrFail($id);
        $groups        = SysGroup::whereNotIn('group_id', [1, 3])->get();
        $defaultGroup  = $data->user_group()->where("ug_is_default", "yes")->first()?->ug_group_id;
        $selectedGroup = $data->user_group->toArray();
        $parse         = [
            'breadcrumbs'    => $breadcrumbs,
            'url'            => $this->url,
            'module'         => $this->module,
            'id'             => $id,
            'data'           => $data,
            'groups'         => $groups,
            'default_group'  => $defaultGroup,
            'selected_group' => $selectedGroup,
        ];
        return view('admin::data_pegawai.edit')->with($parse);
    }

    public function update(Request $request, $id)
    {
        $uploadedPath = [];
        $oldPath      = [];
        $request->validate([
            'nip'           => 'required|string',
            'fullname'      => 'required|string',
            'email'         => 'required|email|min:4',
            'password'      => 'sometimes|min:4|confirmed',
            'foto'          => 'sometimes|max:500|mimes:jpeg,jpg,png',
            'group'         => 'required',
            'group_default' => 'required',
        ]);
        try {
            DB::beginTransaction();

            $currentUser                = SysUser::with('master_pegawai')->findOrFail($id);
            $currentUser->user_email    = $request['email'];
            $currentUser->user_fullname = $request['fullname'];
            if (!empty($request['password'])) $currentUser->user_password = bcrypt($request['password']);

            if ($request->hasFile("foto")) {
                if ($currentUser->user_picture != '/images/profiles/default.png') array_push($oldPath, public_path($currentUser->user_picture));

                $image = $request->file('foto');
                $img   = Image::make($request->file('foto')->getRealPath());
                $img->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $imageName = "/images/profiles/" . str_replace(" ", "_", $request->fullname) . '_' . Str::random(5) . '.' . $image->getClientOriginalExtension();
                $img->save(public_path($imageName), 80);

                $currentUser->user_picture = $imageName;
                array_push($uploadedPath, public_path($imageName));
            }

            $currentUser->save();


            // Delete User Group
            SysUserGroup::where("ug_user_id", $currentUser->user_id)->delete();
            // Reinsert User Group
            foreach ($request->group as $group) {
                SysUserGroup::create([
                    'ug_user_id'    => $currentUser->user_id,
                    'ug_group_id'   => $group,
                    'ug_is_default' => $request->group_default == $group ? 'yes' : 'no'
                ]);
            }

            // Update Master Pegarai
            $pegawai             = $currentUser->master_pegawai;
            $pegawai->peg_nama   = $currentUser->user_fullname;
            $pegawai->peg_alamat = $request['alamat'];
            $pegawai->peg_telp   = $request['no_telp'];
            $pegawai->peg_nip    = $request['nip'];
            $pegawai->peg_status = "aktif";
            if (!empty($request['signature_base64'])) $pegawai->peg_ttd_base64 = $request['signature_base64'];
            $pegawai->save();

            if ($request->hasFile('signature_file')) {
                array_push($oldPath, public_path($pegawai->peg_ttd_file));
                $signature = $request->file('signature_file');
                $img       = Image::make($request->file('signature_file')->getRealPath());
                $img->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $filePath = sprintf(config("app.path_file_pegawai"), $pegawai->peg_id);
                if (!File::exists($filePath)) {
                    File::makeDirectory($filePath, 0777, true, true);
                }
                $signatureName = $filePath . '/signature-' . str_replace(" ", "_", $pegawai->peg_nama) . '_' . Carbon::now()->unix() . '.' . $signature->getClientOriginalExtension();
                $img->save(public_path($signatureName), 80);

                $pegawai->peg_ttd_file = $signatureName;
                $pegawai->save();

                array_push($uploadedPath, public_path($signatureName));
            }

            DB::commit();
            foreach ($oldPath as $path) {
                @unlink($path);
            }
            return responseJSON(200, [], "Data sukses diperbarui");
        } catch (Exception $e) {
            DB::rollBack();
            foreach ($uploadedPath as $path) {
                @unlink($path);
            }
            return responseJSON(500, [], $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $data                = SysUser::findOrFail($id);
        $deletedID           = $data->master_pegawai->peg_id;
        $deletedProfileImage = $data->user_picture;
        $data->delete();

        $deletedPath = sprintf(config("app.path_file_pegawai"), $deletedID);
        File::deleteDirectory($deletedPath);
        if ($deletedProfileImage != '/images/profiles/default.png') {
            @unlink($deletedProfileImage);
        }

        return responseJSON(200, [], "Sukses");
    }

    public function banned(Request $request)
    {
        $request->validate([
            'ids'    => 'required',
            'status' => ['required', Rule::in(['yes', 'no']),],
        ]);

        foreach ($request->ids as $id) {
            $data                 = SysUser::findOrFail($id);
            $data->user_is_banned = $request->status;
            $data->user_banned_at = $request->status == 'yes' ? date("Y-m-d H:i:s") : NULL;
            $data->save();
        }
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid' => $this->ajax_datagrid($request),
            default    => responseJSON(404, null, "Invalid url"),
        };
    }

    private function ajax_datagrid(Request $request)
    {
        $data = SysUser::whereNotIn('ug_group_id', [1, 3])
            ->leftJoin('sys_user_group', 'ug_user_id', '=', 'user_id');
        // Filter
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                $data->orderBy($sort[$i], $order[$i]);
            }
        }

        $data = $data->groupBy('user_id');

        // Total
        $total = $data->get()->count();
        // Pagination
        $data->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['user_id']         = $d->user_id;
            $x['user_fullname']   = $d->user_fullname;
            $x['user_email']      = $d->user_email;
            $x['user_is_active']  = ucwords($d->user_is_active);
            $x['user_picture']    = url(config("app.url_profile_image") . $d->user_picture);
            $x['user_last_login'] = !empty($d->user_last_login) ? Carbon::createFromFormat('Y-m-d H:i:s', $d->user_last_login)->format("d M Y, h:i:s") : $d->user_last_login;
            $x['user_created_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $d->user_created_at)->format("d M Y, h:i:s");
            $x['user_is_banned']  = ucwords($d->user_is_banned);
            if ($d->user_is_banned == "yes") {
                $x['user_banned_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $d->user_banned_at)->format("d M Y, h:i:s");
            }
            array_push($result, $x);
        }
        return response()->json(["total" => $total, "rows" => $result]);
    }
}
