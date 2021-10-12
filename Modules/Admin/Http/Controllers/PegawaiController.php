<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use Illuminate\Routing\Controller;

class PegawaiController extends Controller
{
    public $module = self::class;
    private $url = 'admin/data/pegawai';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Administrator'),
            new BreadcrumbsStruct('Data Pegawai'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view('admin::data_pegawai.index')->with($parser);
    }
}
