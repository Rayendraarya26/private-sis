<?php

namespace Modules\Paskal\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VerifKajianPermohonanController extends Controller
{
    public $module = self::class;
    private $url = 'paskal/verifikasi';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Paskal'),
            new BreadcrumbsStruct('Verifikasi Kajian Permohonan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("paskal::verif_kajian.index")->with($parser);
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
