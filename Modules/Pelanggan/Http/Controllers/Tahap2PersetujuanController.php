<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Tahap2PersetujuanController extends Controller
{
    public $module = self::class;
    private $url = 'pelanggan/tahap2/persetujuan-temuan';
    private $view = "pelanggan::tahap2_persetujuan";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Tahap 2'),
            new BreadcrumbsStruct('Persetujuan Temuan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid' => $this->ajax_datagrid($request),
            default    => responseJSON(404, null, "Invalid url"),
        };
    }
}
