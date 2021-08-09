<?php

namespace Modules\System\Http\Controllers;

use App\Http\Traits\GeneralTraits;
use App\Models\BbkkpSis\SysMenu;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\System\Http\Traits\MenuTraits;

class ManageMenuController extends Controller
{
    use GeneralTraits, MenuTraits;

    private $module = 'system/menu';


    public function index()
    {
        $parse = ['module' => $this->module];
        return view('system::menu.index')->with($parse);
    }


    public function create()
    {
        $parse = ['module' => $this->module];
        return view('system::menu.create')->with($parse);
    }


    public function store(Request $request)
    {
        $request->validate([
            'menu_name' => 'required|unique:App\Models\BbkkpSis\SysGroup,group_name',
            'menu_is_active' => 'required',
        ]);

        try {
            SysMenu::create($request->all());
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->withErrors($e->getMessage());
        }

        return redirect()->back()->with('message', "Tambah data behasil");
    }

    public function show($id)
    {
        return redirect($this->module . "/$id/edit");
    }

    public function edit($id)
    {
        $data = SysMenu::findOrFail($id);
        $parse = ['module' => $this->module, 'id' => $id, 'data' => $data];
        return view('system::menu.edit')->with($parse);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'menu_name' => 'required|string',
            'menu_is_active' => 'required|string',
        ]);

        // TODO: check apakah ada menu yg kembar
        $currentData = SysMenu::findOrFail($id);
        $newName = SysMenu::where("menu_name", $request->menu_name)->where('menu_name', '<>', $currentData->menu_name)->first();
        if (!empty($newName)) return redirect()->back()->withInput($request->all())->withErrors("Nama menu sudah dipakai");

        $currentData->menu_name = $request->menu_name;
        $currentData->menu_desc = $request->menu_desc;
        $currentData->menu_icon = $request->menu_icon;
        $currentData->menu_order = $request->menu_order;
        $currentData->menu_is_active = $request->menu_is_active;
        if (!empty($request->menu_parent_id)) $currentData->menu_parent_id = $request->menu_parent_id;

        $currentData->save();
        return redirect()->back()->with("message", "Edit data berhasil");
    }


    public function destroy($id)
    {
        $data = SysMenu::findOrFail($id);
        $data->delete();

        return $this->responseJSON(200, [], "Sukses");
    }

    public function ajaxTreegrid()
    {
        $data = SysMenu::select(
            "menu_id as id",
            "menu_parent_id as parent_id",
            "menu_name",
            "menu_desc",
            "menu_icon",
            DB::raw("concat('fas ', menu_icon) as iconCls"),
            "menu_is_active",
            "menu_order"
        )->orderBy("menu_parent_id")->orderBy("menu_order")->orderBy("menu_name")
            ->get()
            ->toArray();
        $menu = $this->buildTree($data);

        return response()->json($menu);
    }

    public function ajaxDeactive(Request $request)
    {
        foreach ($request->ids as $id) {
            $data = SysMenu::findOrFail($id);
            $data->menu_is_active = $request->status;
            $data->save();
        }

        return $this->responseJSON(200, [], "Deactive berhasil");
    }
}
