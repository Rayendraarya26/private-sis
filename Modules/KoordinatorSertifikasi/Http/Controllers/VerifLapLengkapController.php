<?php

namespace Modules\KoordinatorSertifikasi\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VerifLapLengkapController extends Controller
{
    public $module = self::class;
    private $view = "";
    private $url = 'koordinatorsertifikasi::verif_lap_lengkap';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Koorinator Sertifikasi'),
            new BreadcrumbsStruct('Verifikasi'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-billing' => $this->ajax_datagrid($request),
            default            => null,
        };
    }

    private function ajax_datagrid()
    {

    }
}
