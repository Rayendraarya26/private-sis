<?php

namespace Modules\KerjaSama\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SPKController extends Controller
{
    public $module = self::class;
    private $url = 'kerjasama/spk';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Kerjasama'),
            new BreadcrumbsStruct('SPK'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("kerjasama::spk.index")->with($parser);
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
