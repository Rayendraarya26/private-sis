<?php

namespace Modules\Master\Http\Controllers;

use Illuminate\Routing\Controller;

class SisKomoditiController extends Controller
{
    public $module = self::class;
    private $url = 'master/sis/komoditi';

    public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::sis_komoditi.index")->with($parser); // Lokasi di Modules\Master\Resources\views\provinsi
    }
}
