<?php

namespace Modules\Master\Http\Controllers;

use Illuminate\Routing\Controller;

class SisSertifikasiController extends Controller
{
    public $module = self::class;
    private $url = 'master/sis/sertifikasi';

    public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::sis_sertifikasi.index")->with($parser); // Lokasi di Modules\Master\Resources\views\provinsi
    }
}
