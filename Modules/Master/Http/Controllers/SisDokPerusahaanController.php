<?php

namespace Modules\Master\Http\Controllers;

use Illuminate\Routing\Controller;

class SisDokPerusahaanController extends Controller
{
    public $module = self::class;
    private $url = 'master/sis/dok-perusahaan';

    public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::sis_dok_perusahaan.index")->with($parser); // Lokasi di Modules\Master\Resources\views\provinsi
    }
}
