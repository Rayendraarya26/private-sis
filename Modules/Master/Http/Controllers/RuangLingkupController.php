<?php

namespace Modules\Master\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RuangLingkupController extends Controller
{
    public $module = self::class;
    private $url = 'master/ruang-lingkup';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Master'),
            new BreadcrumbsStruct('Ruang Lingkup'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("master::ruang_lingkup.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid' => $this->ajax_datagrid($request),
            default    => null,
        };
    }

    private function ajax_datagrid(Request $request)
    {
        return responseJSON();
    }
}
