<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use Illuminate\Routing\Controller;
use Modules\TimAudit\Http\Traits\AuditorTraits;

class HistoriPenugasanController extends Controller
{
    use AuditorTraits;

    public $module = self::class;
    private $url = 'timaudit/histori-audit';
    private $view = "timaudit::histori_audit";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Histori Penugasan', url($this->url)),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }


    public function detail()
    {

    }

    public function ajax()
    {

    }
}
