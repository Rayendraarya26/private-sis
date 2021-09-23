<?php

namespace Modules\Master\Http\Controllers;

use Illuminate\Routing\Controller;

class SisKodeEaController extends Controller
{
    public $module = self::class;
    private $url = 'master/sis/kode-ea';

    public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::sis_kode_ea.index")->with($parser); // Lokasi di Modules\Master\Resources\views\provinsi
    }
}
