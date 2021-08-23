<?php

namespace Modules\System\Http\Controllers;


use App\Models\BbkkpSis\SysMenu;
use App\Models\BbkkpSis\SysMenuAction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ManageMenuActionController extends Controller
{
    public string $module = self::class;
    private string $url;

    public function __construct(Request $request)
    {
        $menu_id = $request->route('id');
        if (empty($menu_id)) return redirect()->back();
        $this->dataMenu = SysMenu::findOrFail($menu_id);
        $this->url = "system/menu/" . $menu_id . "/menu-action";
    }


    public function index()
    {
        $parse = ['url' => $this->url, 'module' => $this->module, 'menu' => $this->dataMenu];
        return view('system::menu_action.index')->with($parse);
    }

    public function create()
    {
        return redirect($this->url);
    }


    public function store(Request $request)
    {
        $request->validate([
            'action_name' => 'required',
            'action_controller' => 'required',
        ]);

        $data = [
            'action_menu_id' => $this->dataMenu->menu_id,
            'action_name' => $request->action_name,
            'action_controller' => $request->action_controller,
        ];
        SysMenuAction::create($data);

        return responseJSON(200, ['message' => "success"], "success");
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
        $action = SysMenuAction::findOrFail($request->action_id);
        $action->action_name = $request->action_name;
        $action->action_controller = $request->action_controller;
        $action->save();
        return responseJSON(200, ['message' => "success"], "success");
    }


    public function destroy($id, $actionID)
    {
        $data = SysMenuAction::findOrFail($actionID);
        $data->delete();

        return responseJSON(200, [], "Sukses");
    }

    public function ajaxDatagrid(Request $request)
    {
        $data = SysMenuAction::select('*')->where('action_menu_id', $this->dataMenu->menu_id);
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
            $x['action_id'] = $d->action_id;
            $x['action_name'] = $d->action_name;
            $x['action_controller'] = $d->action_controller;
            $x['action_created_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $d->action_created_at)->format("d M Y, h:i:s");
            array_push($result, $x);
        }
        return response()->json(["total" => $total, "rows" => $result], 200);
    }
}
