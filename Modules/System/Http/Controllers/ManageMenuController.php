<?php

namespace Modules\System\Http\Controllers;


use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\MasterIcons;
use App\Models\BbkkpSis\SysMenu;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\System\Http\Traits\MenuTraits;

class ManageMenuController extends Controller
{
    use MenuTraits;

    public $module = self::class;
    private $url = 'system/menu';


    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('System'),
            new BreadcrumbsStruct('Manage Menu'),
        ];

        $parse = ['url' => $this->url, 'module' => $this->module, 'breadcrumbs' => $breadcrumbs];
        return view('system::menu.index')->with($parse);
    }


    public function create()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('System'),
            new BreadcrumbsStruct('Manage Menu', url($this->url)),
            new BreadcrumbsStruct('Tambah'),
        ];

        $parse = ['url' => $this->url, 'module' => $this->module, 'breadcrumbs' => $breadcrumbs];
        return view('system::menu.create')->with($parse);
    }


    public function store(Request $request)
    {
        $request->validate([
            'menu_name'      => [
                'required',
                Rule::unique('sys_menu')->where(function ($query) use ($request) {
                    return $query
                        ->where('menu_parent_id', $request['menu_parent_id'])
                        ->where('menu_name', $request['menu_name']);
                }),
            ],
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
        return redirect($this->url . "/$id/edit");
    }

    public function edit($id)
    {
        $data = SysMenu::findOrFail($id);

        $breadcrumbs = [
            new BreadcrumbsStruct('System'),
            new BreadcrumbsStruct('Manage Menu', url($this->url)),
            new BreadcrumbsStruct('Edit'),
        ];

        $parse = ['url' => $this->url, 'id' => $id, 'data' => $data, 'module' => $this->module, 'breadcrumbs' => $breadcrumbs];
        return view('system::menu.edit')->with($parse);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'menu_name'      => 'required|string',
            'menu_is_active' => 'required|string',
        ]);

        // TODO: check apakah ada menu yg kembar
        $currentData = SysMenu::findOrFail($id);
        $newName     = SysMenu::where("menu_name", $request->menu_name)->where('menu_name', '<>', $currentData->menu_name)->first();
        if (!empty($newName)) return redirect()->back()->withInput($request->all())->withErrors("Nama menu sudah dipakai");

        $currentData->menu_name      = $request->menu_name;
        $currentData->menu_desc      = $request->menu_desc;
        $currentData->menu_icon      = $request->menu_icon;
        $currentData->menu_order     = $request->menu_order;
        $currentData->menu_is_active = $request->menu_is_active;
        if (!empty($request->menu_parent_id)) $currentData->menu_parent_id = $request->menu_parent_id;

        $currentData->save();
        return redirect()->back()->with("message", "Edit data berhasil");
    }


    public function destroy($id)
    {
        $data = SysMenu::findOrFail($id);
        $data->delete();

        return responseJSON(200, [], "Sukses");
    }

    public function ajaxTreegrid()
    {
        $data = SysMenu::select(
            "menu_id as id",
            "menu_parent_id as parent_id",
            "menu_name",
            "menu_desc",
            "menu_icon",
            DB::raw("menu_icon as iconCls"),
            "menu_is_active",
            "menu_order"
        )->orderBy("menu_parent_id")->orderBy("menu_order")->orderBy("menu_name")
            ->get()
            ->toArray();
        $menu = $this->buildTree($data);

        return response()->json($menu);
    }

    public function ajaxActive(Request $request)
    {
        foreach ($request->ids as $id) {
            $data                 = SysMenu::findOrFail($id);
            $data->menu_is_active = $request->status;
            $data->save();
        }

        return responseJSON(200, [], "Deactive berhasil");
    }

    public function ajaxDataIcon(Request $request)
    {
        $dataIcons = MasterIcons::when($request['q'], function ($query, $search) {
            return $query->where('icon_name', 'like', '%' . $search . '%');
        })->limit(10)->get();
        $response  = [];
        foreach ($dataIcons as $icon) {
            $response[] = ['icon_name' => $icon->icon_name, 'icon_code' => $icon->icon_name];
        }
        return response()->json($response);
    }
}
