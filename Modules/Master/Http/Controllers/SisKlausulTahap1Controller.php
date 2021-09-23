<?php

namespace Modules\Master\Http\Controllers;

use Illuminate\Routing\Controller;

class SisKlausulTahap1Controller extends Controller
{
    public $module = self::class;
    private $url = 'master/sis/klausul-tahap-1';

    public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::sis_klausul_tahap1.index")->with($parser); // Lokasi di Modules\Master\Resources\views\provinsi
    }
}
