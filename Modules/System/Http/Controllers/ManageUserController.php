<?php

namespace Modules\System\Http\Controllers;

use App\Http\Traits\GeneralTraits;
use App\Models\BbkkpSis\SysGroup;
use App\Models\BbkkpSis\SysUser;
use App\Models\BbkkpSis\SysUserGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ManageUserController extends Controller
{
    use GeneralTraits;

    private $module = 'system/user';

    public function index()
    {
        $parse = ['module' => $this->module];
        return view('system::user.index')->with($parse);
    }

    public function create()
    {
        $groups = SysGroup::all();
        $parse = ['module' => $this->module, 'groups' => $groups];
        return view('system::user.create')->with($parse);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string',
            'email' => 'required|email|min:4|unique:App\Models\BbkkpSis\SysUser,user_email',
            'password' => 'required|min:4|confirmed',
            'foto' => 'sometimes|max:500|mimes:jpeg,jpg,png'
        ]);

        $dataInsert = [
            'user_fullname' => $request->fullname,
            'user_email' => $request->email,
            'user_password' => bcrypt($request->password),
            'user_token' => NULL,
            'user_is_active' => 'yes',
        ];

        if ($request->hasFile("foto")) {
            $image = $request->file('foto');
            $img = (new Image)->make($request->file('foto')->getRealPath());
            $img->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $imageName = "/images/profiles/" . str_replace(" ", "_", $request->username) . '_' . Str::random(5) . '.' . $image->getClientOriginalExtension();
            $img->save(public_path($imageName), 80);

            $dataInsert['user_picture'] = $imageName;
        }

        DB::transaction(function () use ($request, $dataInsert) {
            $insert = SysUser::create($dataInsert);
            // Delete User Group
            SysUserGroup::where("ug_user_id", $insert->user_id)->delete();
            // Reinsert User Group
            foreach ($request->group as $group) {
                SysUserGroup::create([
                    'ug_user_id' => $insert->user_id,
                    'ug_group_id' => $group,
                    'ug_is_default' => $request->group_default == $group ? 'yes' : 'no'
                ]);
            }
        });


        return redirect()->back()->with('message', "Data berhasil ditambahkan");
    }

    public function show($id)
    {
        $defaultGroup = auth()->user()->user_group()->where("ug_is_default", "yes")->first();
        return redirect($this->module . "/$id/edit")
            ->with([
                'default_group' => $defaultGroup
            ]);
    }

    public function edit($id)
    {
        $data = SysUser::findOrFail($id);
        $groups = SysGroup::all();
        $defaultGroup = $data->user_group()->where("ug_is_default", "yes")->first()?->ug_group_id;
        $selectedGroup = $data->user_group->toArray();
        $parse = [
            'module' => $this->module,
            'id' => $id,
            'data' => $data,
            'groups' => $groups,
            'default_group' => $defaultGroup,
            'selected_group' => $selectedGroup
        ];
        return view('system::user.edit')->with($parse);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|string|min:4',
            'email' => 'required|email|min:4',
            'password' => 'sometimes|confirmed',
            'foto' => 'sometimes|max:500|mimes:jpeg,jpg,png',
            'group' => 'required',
            'group_default' => 'required',
        ]);

        // TODO: check apakah ada email/username yg kembar
        $currentUser = SysUser::findOrFail($id);
        $newUsername = SysUser::where("user_fullname", $request->username)->where('user_fullname', '<>', $currentUser->user_fullname)->first();
        if (!empty($newUsername)) return redirect()->back()->withInput($request->all())->withErrors("Username telah digunakan");
        $newEmail = SysUser::where("user_email", $request->email)->where('user_email', '<>', $currentUser->user_email)->first();
        if (!empty($newEmail)) return redirect()->back()->withInput($request->all())->withErrors("Email telah digunakan");

        $currentUser->user_fullname = $request->username;
        $currentUser->user_email = $request->email;
        if (!empty($request->password)) $currentUser->user_password = bcrypt($request->password);

        if ($request->hasFile("foto")) {
            $image = $request->file('foto');
            $img = Image::make($request->file('foto')->getRealPath());
            $img->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $imageName = "/images/profiles/" . str_replace(" ", "_", $request->username) . '_' . Str::random(5) . '.' . $image->getClientOriginalExtension();
            $img->save(public_path($imageName), 80);
            $currentUser->user_picture = $imageName;
        }

        DB::transaction(function () use ($request, $currentUser) {
            // Delete User Group
            SysUserGroup::where("ug_user_id", $currentUser->user_id)->delete();
            // Reinsert User Group
            foreach ($request->group as $group) {
                SysUserGroup::create([
                    'ug_user_id' => $currentUser->user_id,
                    'ug_group_id' => $group,
                    'ug_is_default' => $request->group_default == $group ? 'yes' : 'no'
                ]);
            }

            $currentUser->save();
        });


        return redirect()->back()->with("message", "Edit data berhasil");
    }

    public function destroy($id)
    {
        $data = SysUser::findOrFail($id);
        $data->delete();

        return $this->responseJSON(200, [], "Sukses");
    }

    public function ajaxDatagrid(Request $request)
    {
        $data = SysUser::select('*');
        // Filter
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                $data->orderBy($sort[$i], $order[$i]);
            }
        }
        // Total
        $total = $data->get()->count();
        // Pagination
        $data->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['user_id'] = $d->user_id;
            $x['user_fullname'] = $d->user_fullname;
            $x['user_email'] = $d->user_email;
            $x['user_is_active'] = ucwords($d->user_is_active);
            $x['user_is_banned'] = ucwords($d->user_is_banned);
            $x['user_picture'] = url(config("app.url_profile_image") . $d->user_picture);
            $x['user_last_login'] = !empty($d->user_last_login) ? Carbon::createFromFormat('Y-m-d H:i:s', $d->user_last_login)->format("d M Y, h:i:s") : $d->user_last_login;
            $x['user_created_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $d->user_created_at)->format("d M Y, h:i:s");
            array_push($result, $x);
        }
        return response()->json(["total" => $total, "rows" => $result]);
    }

    public function ajaxBanned(Request $request)
    {

        foreach ($request->ids as $id) {
            $data = SysUser::findOrFail($id);
            $data->user_is_banned = $request->status;
            $data->user_banned_at = $request->status == 'yes' ? date("Y-m-d H:i:s") : NULL;
            $data->save();
        }

        return $this->responseJSON(200, [], "Delete berhasil");
    }
}
