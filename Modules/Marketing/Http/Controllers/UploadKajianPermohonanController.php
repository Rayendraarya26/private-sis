<?php

namespace Modules\Marketing\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UploadKajianPermohonanController extends Controller
{
    public $module = self::class;
    private $url = 'marketing/kajian-permohonan';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Marketing'),
            new BreadcrumbsStruct('Kajian Permohonan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("marketing::spk.index")->with($parser);
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
