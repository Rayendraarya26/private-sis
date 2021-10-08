<?php

namespace Modules\Marketing\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisPermohonan;
use App\Models\BbkkpSis\SisPermohonanDokumen;
use App\Models\BbkkpSis\SisPermohonanKomoditi;
use App\Models\BbkkpSis\SisPermohonanPabrik;
use App\Models\BbkkpSis\SisPermohonanStatus;
use App\Models\BbkkpSis\MasterJenisDokPerusahaan;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VerifikasiPermohonanController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public $module = self::class;
    private $url = 'marketing/verifikasi-permohonan';
	
    public function index()
    {
		$breadcrumbs = [
            new BreadcrumbsStruct('Marketing'),
            new BreadcrumbsStruct('Verifikasi Sertifikasi'),
        ];
		
        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("marketing::verifikasi_permohonan.index")->with($parser);
    }
	
	public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-permohonan'       => $this->ajax_datagrid_permohonan($request),
            'tinymce-uploadimage'       => $this->ajax_tinymce_uploadimage($request),
            default                     => null,
        };
    }
	
	private function ajax_tinymce_uploadimage(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimetypes:image/jpeg,image/png|max:1000', // 1MB
            ]);

            $img = $request->file('file');
            $imgName = $img->hashName();
            $img->move(public_path(config('app.path_file_master')), $imgName);
            $publicUrl = asset(config('app.path_file_master') . '/' . $imgName);

            return response()->json(["location" => $publicUrl]);
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }

    }
	
	private function ajax_datagrid_permohonan(Request $request)
    {
        $data = SisPermohonan::join('master_sertifikasi', "sis_permohonan.sert_id", "=", "master_sertifikasi.sert_id");
        // Filter
		$data->where('mohon_approved_status', '=', 'on-progress');
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                $data->orderBy($sort[$i], $order[$i]);
            }
        }
        // Total
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
			/* 
			`mohon_kajian_permohonan_file`
			`mohon_pernyataan_persetujuan_file`
			`mohon_spk_file`
			 */
			$x['status_step']       = '';
			if(is_null($d->mohon_pernyataan_persetujuan_file) && is_null($d->mohon_spk_file)){
				$x['status_step']       = 'verifikasi';
			}
            
            $x['cust_sert_id']          = $d->cust_sert_id;
            $x['mohon_id']              = $d->mohon_id;
            $x['cust_id']               = $d->cust_id;
            $x['user_id']               = $d->user_id;
            $x['sert_id']               = $d->sert_id;
            $x['sert_nama']             = $d->sert_nama;
            $x['mohon_cust_nama']       = $d->mohon_cust_nama;
            $x['mohon_jenis_status']    = $d->mohon_jenis_status;
            $x['created_at']            = $d->created_at?->format("Y-m-d H:i:s"); // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            $x['update_at']             = $d->update_at?->format("Y-m-d H:i:s");  // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
	
	public function detail(Request $request, $mohonID)
    {
		$request->validate(['action' => 'required']);
        return match ($request['action']) {
            'verifikasi'       => $this->detail_verifikasi($request, $mohonID),
            default            => null,
        };
       
    }
	
	private function detail_verifikasi(Request $request, $mohonID)
    {
		$dataPermohon = SisPermohonan::where('mohon_id', $mohonID);
		$dataPermohon->join('master_sertifikasi', 'master_sertifikasi.sert_id', '=', 'sis_permohonan.sert_id');
		
		$dataPermohon->join('master_jenis_perusahaan', 'master_jenis_perusahaan.jenis_perusahaan_id', '=', 'sis_permohonan.jenis_perusahaan_id');
		$dataPermohon->join('master_negara', 'master_negara.negara_id', '=', 'sis_permohonan.negara_id');
		$dataPermohon->join('master_kabupaten', 'master_kabupaten.kab_id', '=', 'sis_permohonan.kab_id');
		$dataPermohon->join('master_kecamatan', 'master_kecamatan.kec_id', '=', 'sis_permohonan.kec_id');
		$dataPermohon->join('master_provinsi', 'master_provinsi.prov_id', '=', 'sis_permohonan.prov_id');
		$dataPermohon->select('*');
        $breadcrumbs = [			
            new BreadcrumbsStruct('Marketing'),
            new BreadcrumbsStruct('Verifikasi Sertifikasi', url($this->url)),
            new BreadcrumbsStruct('Detail Permohonan "#'.$mohonID.'"'),
        ];
		$dataPermohonKomoditi = SisPermohonanKomoditi::where('mohon_id', $mohonID);
		$dataPermohonKomoditi->join('master_komoditi', 'master_komoditi.komodt_id', '=', 'sis_permohonan_komoditi.komodt_id');
		$dataPermohonKomoditi->select('*');
		
		
		$dataPermohonPabrik = SisPermohonanPabrik::where('mohon_id', $mohonID);
		$dataPermohonPabrik->join('master_kabupaten', 'master_kabupaten.kab_id', '=', 'sis_permohonan_pabrik.kab_id');
		$dataPermohonPabrik->join('master_kecamatan', 'master_kecamatan.kec_id', '=', 'sis_permohonan_pabrik.kec_id');
		$dataPermohonPabrik->join('master_provinsi', 'master_provinsi.prov_id', '=', 'sis_permohonan_pabrik.prov_id');
		$dataPermohonPabrik->select('*');
		
		$dataPermohonanDokumen = SisPermohonanDokumen::where('mohon_id', $mohonID);
		$dataPermohonanDokumen->join('master_jenis_dok_perusahaan', 'master_jenis_dok_perusahaan.jenis_dok_perusahaan_id', '=', 'sis_permohonan_dokumen.jenis_dok_perusahaan_id');
		$dataPermohonanDokumen->select('*');
		
		$dataPermohonanStatus = SisPermohonanStatus::where('status_mohon_id', $mohonID)->where('status_tipe', 'revisi');
		$dataPermohonanStatus->select('*');
		
        $parser      = [
            'module'      => $this->module,
            'url'         => $this->url,
            'dataPermohon' => $dataPermohon->get()[0],
            'dataPermohonKomoditi' => $dataPermohonKomoditi->get(),
            'dataPermohonPabrik' => $dataPermohonPabrik->get(),
            'dataPermohonanDokumen' => $dataPermohonanDokumen->get(),
            'dataPermohonanStatus' => $dataPermohonanStatus->get(),
            'breadcrumbs' => $breadcrumbs
        ];
        return view('marketing::verifikasi_permohonan.detail_verifikasi')->with($parser);
	}

	public function edit( Request $request) // menerima parameter ID dari Modules\Master\Routes\web.php
    {
		$request->validate(['status' => 'required']);
		return match ($request['status']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'revisi' => $this->edit_revisi($request),
            'accepted' => $this->edit_accepted($request),
            'rejected' => $this->edit_rejected($request),
            default => null,
        };
    }

	private function edit_rejected( Request $request)
	{
		$data = SisPermohonan::findOrFail($request['mohon_id']);
		$dataInsert = [
			'mohon_id' => $request['mohon_id'],
			'status_mohon_id' => $request['mohon_id'],
			'status_tipe' => 'Informasi',
			'status_pesan' => 'Permohonan anda untuk nomor #'.$request->mohon_id.' telah diterima.',
			'status_judul' => 'Informasi Pengajuan Permohonan'
		];
		
		DB::transaction(function () use ($request, $dataInsert) {
				SisPermohonanStatus::create([
					'status_mohon_id' => $dataInsert['status_mohon_id'],
					'status_tipe' => $dataInsert['status_tipe'],
					'status_pesan' => $dataInsert['status_pesan'],
					'status_judul' => $dataInsert['status_judul']
				]);
				// Delete User Group
				SisPermohonan::findOrFail($request['mohon_id'])->update(['mohon_approved_status' => 'rejected', 'mohon_kajian_permohonan_file' => null]);
			});
			
		return redirect($this->url)->with('message', "Data permohonan #".$request->mohon_id." sudah diverifikasi dengan status '<strong>Ditolak</strong>'.");
	}
	
	private function edit_revisi( Request $request)
	{
		$breadcrumbs = [			
            new BreadcrumbsStruct('Marketing'),
            new BreadcrumbsStruct('Verifikasi Sertifikasi', url($this->url)),
            new BreadcrumbsStruct('Detail Permohonan "#'.$request['mohon_id'].'"', url($this->url.'/'.'detail/'.$request['mohon_id'].'?action=verifikasi')),
            new BreadcrumbsStruct('Revisi Permohonan "#'.$request['mohon_id'].'"'),
        ];
		
		$dataPermohon = SisPermohonan::where('mohon_id', $request['mohon_id']);
		$dataPermohon->join('master_sertifikasi', 'master_sertifikasi.sert_id', '=', 'sis_permohonan.sert_id');
		$dataPermohon->select('*');
		
		$parser = [
			'module' => $this->module, 
			'url' => $this->url,  
			'dataPermohon' => $dataPermohon->get()[0], 
			'breadcrumbs' => $breadcrumbs
		];
        return view("marketing::verifikasi_permohonan.edit_revisi")->with($parser);
	}
	
	private function edit_accepted( Request $request)
	{
		$breadcrumbs = [			
            new BreadcrumbsStruct('Marketing'),
            new BreadcrumbsStruct('Verifikasi Sertifikasi', url($this->url)),
            new BreadcrumbsStruct('Detail Permohonan "#'.$request['mohon_id'].'"', url($this->url.'/'.'detail/'.$request['mohon_id'].'?action=verifikasi')),
            new BreadcrumbsStruct('Terima Permohonan "#'.$request['mohon_id'].'"'),
        ];
		
		$dataPermohon = SisPermohonan::where('mohon_id', $request['mohon_id']);
		$dataPermohon->join('master_sertifikasi', 'master_sertifikasi.sert_id', '=', 'sis_permohonan.sert_id');
		$dataPermohon->select('*');
		
		$parser = [
			'module' => $this->module, 
			'url' => $this->url,  
			'dataPermohon' => $dataPermohon->get()[0], 
			'breadcrumbs' => $breadcrumbs
		];
        return view("marketing::verifikasi_permohonan.edit_accepted")->with($parser);
	}
	
	public function update(Request $request)
    {
		$request->validate(['tipe' => 'required']);
		return match ($request['tipe']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'update-revisi' => $this->update_revisi($request),
            'update-accepted' => $this->update_accepted($request),
            default => null,
        };
    }
	
	private function update_revisi( Request $request)
	{
		$request->validate([
							'mohon_id' => 'required|integer',
							'status_tipe' => 'required|string',
							'status_judul' => 'required|string',
							'status_pesan' => 'required|string'
						]); // auto redirect back jika tidak valid
		$dataInsert = [
            'status_mohon_id' => $request->mohon_id,
            'status_tipe' => $request->status_tipe,
            'status_pesan' => $request->status_pesan,
            'status_judul' => $request->status_judul
        ];

        try {
            SisPermohonanStatus::create($dataInsert);
            return redirect()->back()->with('message', "Tambah informasi revisi berhasil");
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
	}
	
	private function update_accepted( Request $request)
	{
		$request->validate([
            'mohon_id' => 'required|integer',
			'status_tipe' => 'required|string',
			'mohon_harus_lunas_status' => 'required|string',
			'mohon_harga_permohonan' => 'numeric|string',
            'mohon_kajian_permohonan_file' => 'required|mimes:pdf'
        ]);

       $dataInsert = [
            'mohon_id' => $request->mohon_id,
            'status_mohon_id' => $request->mohon_id,
            'mohon_harga_permohonan' => $request->mohon_harga_permohonan,
            'mohon_harus_lunas_status' => $request->mohon_harus_lunas_status,
            'status_tipe' => $request->status_tipe,
            'status_pesan' => 'Permohonan anda untuk nomor #'.$request->mohon_id.' telah diterima.',
            'status_judul' => 'Informasi Pengajuan Permohonan'
        ];

        if ($request->hasFile("mohon_kajian_permohonan_file")) {
            $file     = $request->file('mohon_kajian_permohonan_file');
            $namaFile = Str::slug($request->mohon_id) . '_kajian_permohonan_file_'. time() . '.' . $file->getClientOriginalExtension();
            $path     = config('app.path_file_pengajuan'); // "files/master"| lokasi di config/app.php
            $file->move(public_path($path), $namaFile);
            $dataInsert['mohon_kajian_permohonan_file'] = $path . '/' . $namaFile;

			DB::transaction(function () use ($request, $dataInsert) {
				SisPermohonanStatus::create([
					'status_mohon_id' => $dataInsert['status_mohon_id'],
					'status_tipe' => $dataInsert['status_tipe'],
					'status_pesan' => $dataInsert['status_pesan'],
					'status_judul' => $dataInsert['status_judul']
				]);
				// Delete User Group
				SisPermohonan::findOrFail($request['mohon_id'])->update(['mohon_approved_status' => 'accepted', 'mohon_kajian_permohonan_file' => $dataInsert['mohon_kajian_permohonan_file'], 'mohon_harus_lunas_status' => $dataInsert['mohon_harus_lunas_status'], 'mohon_harga_permohonan' => $dataInsert['mohon_harga_permohonan']]);
			});
			
			return redirect($this->url)->with('message', "Data permohonan #".$request->mohon_id." sudah diverifikasi dengan status diterima.");
        }
		else{
			return redirect()->back()->withInput($request->all())->withErrors(['message' => 'File tidak dapat di upload.']);
		}
		
        
	}
}
