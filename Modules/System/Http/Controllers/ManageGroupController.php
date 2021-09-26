<?php

namespace Modules\System\Http\Controllers;


use App\Models\BbkkpSis\SysGroup;
use App\Models\BbkkpSis\SysGroupPermission;
use App\Models\BbkkpSis\SysMenu;
use App\Models\BbkkpSis\SysMenuAction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\System\Http\Traits\GroupTraits;

class ManageGroupController extends Controller
{
    use GroupTraits;

    public $module = self::class;
    private $url = 'system/group';

    public function index()
    {
        $parse = ['url' => $this->url, 'module' => $this->module];
        return view('system::group.index')->with($parse);
    }


    public function create()
    {
        $parse = ['url' => $this->url, 'module' => $this->module];
        return view('system::group.create')->with($parse);
    }


    public function store(Request $request)
    {
        $request->validate([
            'group_name'      => 'required|unique:App\Models\BbkkpSis\SysGroup,group_name',
            'group_desc'      => 'required',
            'group_is_active' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $insert = SysGroup::create($request->except('permission'));

            if (!empty($request->permission)) {
                $permission = explode(',', $request->permission);
                foreach ($permission as $p) {
                    $x = explode('-', $p);
                    try {
                        if (count($x) > 1) {
                            // Berarti Action
                            SysGroupPermission::create(['group_id' => $insert->group_id, 'action_id' => $x[1]]);
                        } else {
                            // Berarti Menu
                            $action = SysMenuAction::where("action_menu_id", $x[0])->first();
                            SysGroupPermission::create(['group_id' => $insert->group_id, 'action_id' => $action->action_id]);
                        }
                    } catch (Exception $e) {

                    }
                }
            }
        });

        return redirect()->back()->with('message', "Sukses menambah data");
    }

    public function show($id)
    {
        return redirect($this->url . "/$id/edit");
    }

    public function edit($id)
    {
        $data = SysGroup::findOrFail($id);
        $parse = ['url' => $this->url, 'id' => $id, 'data' => $data, 'module' => $this->module];
        return view('system::group.edit')->with($parse);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'group_name'      => 'required',
            'group_desc'      => 'required',
            'group_is_active' => 'required',
        ]);

        DB::transaction(function () use ($request, $id) {
            $data = SysGroup::findOrFail($id);
            $data->group_name = $request->group_name;
            $data->group_desc = $request->group_desc;
            $data->group_is_active = $request->group_is_active;
            $data->save();
            // Remove
            SysGroupPermission::where('group_id', $id)->delete();

            // Reinsert
            if (!empty($request->permission)) {
                $permission = explode(',', $request->permission);
                foreach ($permission as $p) {
                    $x = explode('-', $p);
                    try {
                        if (count($x) > 1) {
                            // Berarti Action
                            SysGroupPermission::create(['group_id' => $data->group_id, 'action_id' => $x[1]]);
                        } else {
                            // Berarti Menu
                            $action = SysMenuAction::where("action_menu_id", $x[0])->first();
                            SysGroupPermission::create(['group_id' => $data->group_id, 'action_id' => $action->action_id]);
                        }
                    } catch (Exception $e) {

                    }
                }
            }
        });

        return redirect()->back()->with('message', "Sukses memperbarui data");
    }

    public function destroy($id)
    {
        $data = SysGroup::findOrFail($id);
        $data->delete();

        return responseJSON(200, [], "Sukses");
    }

    public function ajaxDatagrid(Request $request)
    {
        $data = SysGroup::select('*');
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
            $x['group_id'] = $d->group_id;
            $x['group_name'] = $d->group_name;
            $x['group_desc'] = $d->group_desc;
            $x['group_is_active'] = ucwords($d->group_is_active);
            $x['group_created_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $d->group_created_at)->format("d M Y, h:i:s");
            array_push($result, $x);
        }
        return response()->json(["total" => $total, "rows" => $result]);
    }

    public function ajaxTreegrid(Request $request)
    {
        $groupId = is_null($request->group_id) ? 0 : $request->group_id;

        $data = SysMenu::join("sys_menu_action", "menu_id", '=', 'action_menu_id')
            ->groupBy("menu_id")
            ->orderBy("menu_parent_id")
            ->orderBy("menu_order")
            ->orderBy("menu_name")
            ->with('action')
            ->get();
        $menu = $this->buildTree($data, 0, $groupId);

        return response()->json($menu, 200);

    }

    public function ajaxActive(Request $request)
    {
        foreach ($request->ids as $id) {
            $data = SysGroup::findOrFail($id);
            $data->group_is_active = $request->status;
            $data->save();
        }

        return responseJSON(200, [], "Deactive berhasil");
    }
}
