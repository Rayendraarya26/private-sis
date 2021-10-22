<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use Illuminate\Routing\Controller;

class PpcLaporanController extends Controller
{
    public $module = self::class;
    private $url = 'timaudit/persetujuan-tim/komite';
    private $view = "timaudit::ppc_laporan";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('PPC', url($this->url)),
            new BreadcrumbsStruct('Laporan PPC'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }
}
