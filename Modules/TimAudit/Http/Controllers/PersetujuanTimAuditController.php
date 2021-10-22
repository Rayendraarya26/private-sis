<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use Illuminate\Routing\Controller;

class PersetujuanTimAuditController extends Controller
{
    public $module = self::class;
    private $url = 'timaudit/persetujuan-tim/audit';
    private $view = "timaudit::persetujuan_tim_auditor";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Persetujuan Tim Auditor'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }
}
