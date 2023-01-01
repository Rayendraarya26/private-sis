<?php

namespace Modules\SiHalal\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use Illuminate\Routing\Controller;

class DataAuditorController extends Controller
{

    public $module = self::class;
    private $url = 'sihalal/ref-auditor';
    private $view = "sihalal::ref_auditor";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('SiHalal'),
            new BreadcrumbsStruct('Data Referensi Auditor'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }
}
