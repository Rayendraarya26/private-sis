<?php

namespace Modules\Master\Http\Controllers;

use Illuminate\Routing\Controller;

class SisKodeNaceController extends Controller
{
    public $module = self::class;
    private $url = 'master/sis/kode-nace';

    public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::sis_kode_nace.index")->with($parser); // Lokasi di Modules\Master\Resources\views\provinsi
    }
}
