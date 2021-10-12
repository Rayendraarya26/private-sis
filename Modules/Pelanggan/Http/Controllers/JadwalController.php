<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use Illuminate\Routing\Controller;

class JadwalController extends Controller
{
    public $module = self::class;
    private $url = 'pelanggan/jadwal';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Jadwal'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view('pelanggan::jadwal.index')->with($parser);
    }
}
