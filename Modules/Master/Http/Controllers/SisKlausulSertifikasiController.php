<?php

namespace Modules\Master\Http\Controllers;

use Illuminate\Routing\Controller;

class SisKlausulSertifikasiController extends Controller
{
    public $module = self::class;
    private $url = 'master/sis/klausul-sertifikasi';

    public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::sis_klausul_sertifikasi.index")->with($parser); // Lokasi di Modules\Master\Resources\views\provinsi
    }
}
