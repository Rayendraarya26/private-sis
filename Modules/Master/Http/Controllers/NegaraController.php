<?php

namespace Modules\Master\Http\Controllers;

use App\Models\BbkkpSis\MasterBadanHukum;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class NegaraController extends Controller
{
    public $module = self::class;
    private $url = 'master/negara';

    public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::negara.index")->with($parser); // Lokasi di Modules\Master\Resources\views\badan_hukum
    }

    public function create()
    {

    }

    public function store(Request $request)
    {

    }

    public function edit(Request $request, $negaraId) // menerima parameter ID dari Modules\Master\Routes\web.php
    {

    }

    public function update(Request $request)
    {

    }

    public function destroy($negaraId)
    {

    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'datagrid' => $this->ajax_datagrid($request),
            default => null,
        };
    }

    private function ajax_datagrid(Request $request)
    {
        return response()->json();
    }
}
