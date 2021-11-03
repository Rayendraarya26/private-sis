<?php

namespace Modules\OperatorLs\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use Illuminate\Routing\Controller;

class SertifikatUjiController extends Controller
{
    public $module = self::class;
    private $url = 'operatorls/sertifikat-uji';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Upload Sertifikat Hasil Uji'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("operatorls::sertifikat_uji.index")->with($parser);
    }
}
