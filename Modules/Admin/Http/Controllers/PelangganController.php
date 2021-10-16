<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SysGroup;
use App\Models\BbkkpSis\SysUser;
use App\Models\BbkkpSis\SysUserGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class PelangganController extends Controller
{
    public $module = self::class;
    private $url = 'admin/data/pelanggan';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Administrator'),
            new BreadcrumbsStruct('Data Pelanggan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view('admin::data_pelanggan.index')->with($parser);
    }

    public function create()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Administrator'),
            new BreadcrumbsStruct('Data Pelanggan', url($this->url)),
            new BreadcrumbsStruct('Tambah'),
        ];

        $groups = SysGroup::where('group_id', 3)->get();
        $parse  = ['url' => $this->url, 'groups' => $groups, 'module' => $this->module, 'breadcrumbs' => $breadcrumbs];
        return view('admin::data_pelanggan.create')->with($parse);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string',
            'email'    => 'required|email|min:4|unique:App\Models\BbkkpSis\SysUser,user_email',
            'password' => 'required|min:4|confirmed',
            'foto'     => 'sometimes|max:500|mimes:jpeg,jpg,png'
        ]);

        $uploadedPath = [];

        try {
            DB::beginTransaction();

            $dataInsert = [
                'user_fullname'  => $request->fullname,
                'user_email'     => $request->email,
                'user_password'  => bcrypt($request->password),
                'user_token'     => NULL,
                'user_is_active' => 'yes',
            ];

            if ($request->hasFile("foto")) {
                $image = $request->file('foto');
                $img   = Image::make($request->file('foto')->getRealPath());
                $img->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $imageName = "/images/profiles/" . str_replace(" ", "_", $request->fullname) . '_' . Str::random(5) . '.' . $image->getClientOriginalExtension();
                $img->save(public_path($imageName), 80);
                $dataInsert['user_picture'] = $imageName;

                array_push($uploadedPath, public_path($imageName));
            }

            $newUser = SysUser::create($dataInsert);
            // Delete User Group
            SysUserGroup::where("ug_user_id", $newUser->user_id)->delete();
            // Reinsert User Group
            SysUserGroup::create([
                'ug_user_id'    => $newUser->user_id,
                'ug_group_id'   => 3,
                'ug_is_default' => 'yes'
            ]);

            // Insert to Sis Pelanggan
            $sisPelanggan             = new SisPelanggan();
            $sisPelanggan->user_id    = $newUser->user_id;
            $sisPelanggan->cust_email = $newUser->user_email;
            $sisPelanggan->save();

            DB::commit();

            return redirect()->back()->with('message', "Data berhasil ditambahkan");
        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($uploadedPath as $path) {
                @unlink($path);
            }
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Administrator'),
            new BreadcrumbsStruct('Data Pelanggan', url($this->url)),
            new BreadcrumbsStruct('Edit'),
        ];

        $data          = SysUser::findOrFail($id);
        $groups        = SysGroup::where('group_id', 3)->get();
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
        return view('admin::data_pelanggan.edit')->with($parse);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string|min:4',
            'email'    => 'required|email|min:4',
            'password' => 'sometimes|confirmed',
            'foto'     => 'sometimes|max:500|mimes:jpeg,jpg,png',
        ]);

        $uploadedPath = [];
        try {
            DB::beginTransaction();

            // TODO: check apakah ada email yg kembar
            $currentUser = SysUser::findOrFail($id);
            $newEmail    = SysUser::where("user_email", $request->email)->where('user_email', '<>', $currentUser->user_email)->first();
            if (!empty($newEmail)) return redirect()->back()->withInput($request->all())->withErrors("Email telah digunakan");

            $currentUser->user_fullname = $request->fullname;
            $currentUser->user_email    = $request->email;
            if (!empty($request->password)) $currentUser->user_password = bcrypt($request->password);

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

                array_push($uploadedPath, public_path($imageName));
            }

            $currentUser->save();


            DB::commit();
            return redirect()->back()->with("message", "Edit data berhasil");
        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($uploadedPath as $path) {
                @unlink($path);
            }
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $data = SysUser::findOrFail($id);
        $data->delete();

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
        $data = SysUser::whereIn('ug_group_id', [3])
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
