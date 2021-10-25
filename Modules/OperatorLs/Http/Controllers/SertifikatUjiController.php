<?php

namespace Modules\OperatorLs\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SisPelangganSertifikasi;
use App\Models\BbkkpSis\SisPermohonan;
use App\Models\BbkkpSis\SisPermohonanDokumen;
use App\Models\BbkkpSis\SisPermohonanKomoditi;
use App\Models\BbkkpSis\SisPermohonanPabrik;
use App\Models\BbkkpSis\SisPermohonanStatus;
use App\Models\BbkkpSis\SisBilling;
use App\Models\BbkkpSis\SisBillingItems;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalAudit;
use App\Models\BbkkpSis\SisAuditTimKomite;
use App\Models\BbkkpSis\SisJadwalLog;

use App\Models\BbkkpSis\MasterKodeEa;
use App\Models\BbkkpSis\MasterKodeNace;
use App\Models\BbkkpSis\MasterPegawai;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SertifikatUjiController extends Controller
{
    public $module = self::class;
    private $url = 'operatorls/sertifikat-uji';
	
    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Upload Sertifikat Hasil Uji'),
        ];
		
        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("operatorls::sertifikat_uji.index")->with($parser);
    }
}
