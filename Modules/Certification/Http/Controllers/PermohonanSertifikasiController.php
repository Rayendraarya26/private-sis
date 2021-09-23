<?php

namespace Modules\Certification\Http\Controllers;

use App\Models\BbkkpSis\SisPelanggan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PermohonanSertifikasiController extends Controller
{
    public $module = self::class;
    private $url = 'sertifikasi/permohonan-sertifikasi';

    public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view('certification::permohonan_sertifikasi.index')->with($parser);
    }

    public function create()
    {
        $dataPelanggan = SisPelanggan::where("user_id", auth()->id())->first();
        $parser = [
            'module' => $this->module,
            'url' => $this->url,
            'dataPelanggan' => $dataPelanggan,
        ];
        return view('certification::permohonan_sertifikasi.create')->with($parser);
    }

    public function store()
    {

    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid' => $this->ajax_datagrid($request),
            default => null,
        };
    }

    private function ajax_datagrid()
    {
        return response()->json([]);
    }
}
