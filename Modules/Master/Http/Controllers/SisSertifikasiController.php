<?php

namespace Modules\Master\Http\Controllers;

use App\Models\BbkkpSis\MasterSertifikasi;
use App\Models\BbkkpSis\MasterSertifikasiDokuman;
use App\Models\BbkkpSis\MasterSertifikasiKlausul;
use App\Models\BbkkpSis\MasterKlausulTahap1;
use App\Models\BbkkpSis\MasterJenisDokPerusahaan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SisSertifikasiController extends Controller
{
    public $module = self::class;
    private $url = 'master/sis/sertifikasi';
	
	public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::sis_sertifikasi.index")->with($parser); // Lokasi di Modules\Master\Resources\views\sis_sertifikasi
    }
	
	public function detail(Request $request)
    {
		$request->validate(['tipe' => 'required']);
        return match ($request['tipe']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'detail-klausul-tahap1' => $this->detail_klausul_tahap1($request),
            'detail-klausul' => $this->detail_klausul($request),
            'detail-dokumen' => $this->detail_dokumen($request),
            default => null,
        };
    }
	
	private function detail_klausul_tahap1( Request $request)
    {
		// Check apakah ID tersedia
        $data = MasterSertifikasi::findOrFail($request['sert_id']);
        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data];
        return view("master::sis_sertifikasi.detail-klausul-tahap1")->with($parser); // Lokasi di Modules\Master\Resources\views\sis_sertifikasi
	}
	
	private function detail_klausul( Request $request)
    {
		// Check apakah ID tersedia
        $data = MasterSertifikasi::findOrFail($request['sert_id']);

        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data];
        return view("master::sis_sertifikasi.detail-klausul")->with($parser); // Lokasi di Modules\Master\Resources\views\sis_sertifikasi
	}
	
	private function detail_dokumen( Request $request)
    {
		// Check apakah ID tersedia
        $data = MasterSertifikasi::findOrFail($request['sert_id']); // SELECT * FROM master_sertifikasi where kab_id = $sertId | findOrFail akan otomatis redirect 404 jika data tidak ditemukan primary key degan id tersebut

        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data];
        return view("master::sis_sertifikasi.detail-dokumen")->with($parser); // Lokasi di Modules\Master\Resources\views\sis_sertifikasi
	}
	
    public function ajax(Request $request)
    {
		$request->validate(['action' => 'required']);
        $response = null;
        switch ($request['action']) {
            case 'datagrid-sertifikasi':
                $response = $this->ajax_datagrid_sertifikasi($request);
                break;
			case 'datagrid-sertifikasi-dokumen':
                $response = $this->ajax_datagrid_sertifikasi_dokumen($request);
                break;
			case 'datagrid-sertifikasi-klausul':
                $response = $this->ajax_datagrid_sertifikasi_klausul($request);
                break;
			case 'datagrid-sertifikasi-klausul-tahap1':
                $response = $this->ajax_datagrid_sertifikasi_klausul_tahap1($request);
                break;
			case 'combobox-jenis-dokumen':
                $response = $this->ajax_combobox_jenis_dokumen($request);
                break;
            case 'tinymce-uploadimage':
                $response = $this->ajax_tinymce_uploadimage($request);
                break;
            default:
                abort(404);
        }

        return $response;
    }
	
	private function ajax_combobox_jenis_dokumen(Request $request)
    {
        $result = [];
		$data = MasterJenisDokPerusahaan::select("*");
		// Filter
		$data->whereNotIn('jenis_dok_perusahaan_id', function ($query) {
            $query->select('jenis_dok_perusahaan_id')
				->from('master_sertifikasi_dokumen')
				->where('sert_id', '=', $_GET['sert_id']);;
        });
		if (!empty($request->q)) {
			$data->where('jenis_dok_perusahaan_text', 'LIKE', '%' . $request->q . '%');
		}
		// Sorter
		$data->orderBy('jenis_dok_perusahaan_text', 'ASC');

		foreach ($data->get() as $d) {
			$x['jenis_dok_perusahaan_id'] = $d->jenis_dok_perusahaan_id;
			$x['jenis_dok_perusahaan_text'] = $d->jenis_dok_perusahaan_text;
			array_push($result, $x);
		}
		
        return response()->json($result);
    }
	
	private function ajax_datagrid_sertifikasi_klausul_tahap1(Request $request)
	{
		$data = MasterKlausulTahap1::select("*");
        // Filter
		$data->where('sert_id', '=', $request['sert_id']);;
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort = explode(",", $request->sort);
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
            $x['klausul_thp1_id'] = $d->klausul_thp1_id;
            $x['sert_id'] = $d->sert_id;
            $x['klausul_thp1_nomor'] = $d->klausul_thp1_nomor;
            $x['klausul_thp1_peryataan'] = $d->klausul_thp1_peryataan;
            $x['klausul_thp1_is_tinjauan'] = $d->klausul_thp1_is_tinjauan;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
	}
	
	private function ajax_datagrid_sertifikasi_klausul(Request $request)
	{
		 $data = MasterSertifikasiKlausul::select("*");
        // Filter
		$data->where('sert_id', '=', $request['sert_id']);;
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort = explode(",", $request->sort);
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
            $x['sert_klau_id'] = $d->sert_klau_id;
            $x['sert_id'] = $d->sert_id;
            $x['sert_klau_nomor'] = $d->sert_klau_nomor;
            $x['sert_klau_peryataan'] = $d->sert_klau_peryataan;
            $x['sert_klau_is_item'] = $d->sert_klau_is_item;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
	}
	
	private function ajax_datagrid_sertifikasi_dokumen(Request $request)
    {
        $data = MasterJenisDokPerusahaan::select("*");
		
		$data->join('master_sertifikasi_dokumen', function ($join) {
            $join->on('master_sertifikasi_dokumen.jenis_dok_perusahaan_id', '=', 'master_jenis_dok_perusahaan.jenis_dok_perusahaan_id')->where('master_sertifikasi_dokumen.sert_id', '=', $_GET['sert_id']);
        });
		
        // Filter
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
				$data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort = explode(",", $request->sort);
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
            $x['jenis_dok_perusahaan_id'] = $d->jenis_dok_perusahaan_id;
            $x['jenis_dok_perusahaan_text'] = $d->jenis_dok_perusahaan_text;
            $x['sert_dok_id'] = $d->sert_dok_id;
            $x['sert_dok_required'] = $d->sert_dok_required;
            $x['sert_dok_keterangan'] = $d->sert_dok_keterangan;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
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
	
    private function ajax_datagrid_sertifikasi(Request $request)
    {
        $data = MasterSertifikasi::select("*");
        // Filter
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort = explode(",", $request->sort);
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
            $x['sert_id'] = $d->sert_id;
            $x['sert_nama'] = $d->sert_nama;
            $x['sert_deskripsi'] = $d->sert_deskripsi;
            $x['sert_expired'] = $d->sert_expired;
            $x['sert_format_referensi'] = $d->sert_format_referensi;
            $x['sert_is_product'] = $d->sert_is_product;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
	
	public function create( Request $request)
    {
		$request->validate(['tipe' => 'required']);
		return match ($request['tipe']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'create-sertifikasi' => $this->create_sertifikasi($request),
            'create-sertifikasi-dokumen' => $this->create_sertifikasi_dokumen($request),
            'create-sertifikasi-klausul' => $this->create_sertifikasi_klausul($request),
            'create-sertifikasi-klausul-tahap1' => $this->create_sertifikasi_klausul_tahap1($request),
            default => null,
        };
    }
	
	private function create_sertifikasi_klausul_tahap1(Request $request)
    {
		$data = MasterSertifikasi::findOrFail($request['sert_id']);
        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data];
        return view("master::sis_sertifikasi.create-sertifikasi-klausul-tahap1")->with($parser);
	}
	
	private function create_sertifikasi_klausul(Request $request)
    {
		$data = MasterSertifikasi::findOrFail($request['sert_id']);
        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data];
        return view("master::sis_sertifikasi.create-sertifikasi-klausul")->with($parser);
	}
	
	private function create_sertifikasi_dokumen(Request $request)
    {
		$data = MasterSertifikasi::findOrFail($request['sert_id']);
        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data];
        return view("master::sis_sertifikasi.create-sertifikasi-dokumen")->with($parser);
	}
	
	private function create_sertifikasi(Request $request)
    {
		$parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::sis_sertifikasi.create-sertifikasi")->with($parser); // Lokasi di Modules\Master\Resources\views\sis_sertifikasi
	}
	
	public function store( Request $request)
    {
		$request->validate(['tipe' => 'required']);
		return match ($request['tipe']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'store-sertifikasi' => $this->store_sertifikasi($request),
            'store-sertifikasi-dokumen' => $this->store_sertifikasi_dokumen($request),
            'store-sertifikasi-klausul' => $this->store_sertifikasi_klausul($request),
            'store-sertifikasi-klausul-tahap1' => $this->store_sertifikasi_klausul_tahap1($request),
            default => null,
        };
    }
	
	private function store_sertifikasi_klausul_tahap1( Request $request)
    {
        $request->validate([
							'klausul_thp1_nomor' => 'required|string',
							'sert_id' => 'required|integer',
							'klausul_thp1_peryataan' => 'required|string'
						]); // auto redirect back jika tidak valid
		$dataInsert = [
            'klausul_thp1_nomor' => $request->klausul_thp1_nomor,
            'sert_id' => $request->sert_id,
            'klausul_thp1_peryataan' => $request->klausul_thp1_peryataan
        ];

        try {
            MasterKlausulTahap1::create($dataInsert);
            return redirect()->back()->with('message', "Tambah data berhasil");
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
    }
	
	private function store_sertifikasi_klausul( Request $request)
    {
        $request->validate([
							'sert_klau_nomor' => 'required|string',
							'sert_id' => 'required|integer',
							'sert_klau_peryataan' => 'required|string'
						]); // auto redirect back jika tidak valid
		$dataInsert = [
            'sert_klau_nomor' => $request->sert_klau_nomor,
            'sert_id' => $request->sert_id,
            'sert_klau_peryataan' => $request->sert_klau_peryataan
        ];

        try {
            MasterSertifikasiKlausul::create($dataInsert);
            return redirect()->back()->with('message', "Tambah data berhasil");
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
    }
	
	private function store_sertifikasi_dokumen( Request $request)
    {
        $request->validate([
							'jenis_dok_perusahaan_id' => 'required|integer',
							'sert_id' => 'required|integer',
							'sert_dok_keterangan' => 'nullable|string',
							'sert_dok_required' => 'required|string'
						]); // auto redirect back jika tidak valid
		$dataInsert = [
            'jenis_dok_perusahaan_id' => $request->jenis_dok_perusahaan_id,
            'sert_id' => $request->sert_id,
            'sert_dok_keterangan' => $request->sert_dok_keterangan,
            'sert_dok_required' => $request->sert_dok_required
        ];

        try {
            MasterSertifikasiDokuman::create($dataInsert);
            return redirect()->back()->with('message', "Tambah data berhasil");
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
    }
	
    private function store_sertifikasi( Request $request)
    {
        $request->validate([
							'sert_nama' => 'required|string',
							'sert_deskripsi' => 'required|string',
							'sert_expired' => 'required|integer',
							'sert_format_referensi' => 'required|string',
							'sert_is_product' => 'required|string'
						]); // auto redirect back jika tidak valid
		$dataInsert = [
            'sert_nama'      => $request->sert_nama,
            'sert_deskripsi' => $request->sert_deskripsi,
            'sert_expired' => $request->sert_expired,
            'sert_format_referensi' => $request->sert_format_referensi,
            'sert_is_product' => $request->sert_is_product,
        ];

        try {
            MasterSertifikasi::create($dataInsert);
            return redirect()->back()->with('message', "Tambah data berhasil");
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function edit( Request $request) // menerima parameter ID dari Modules\Master\Routes\web.php
    {
		$request->validate(['tipe' => 'required']);
		return match ($request['tipe']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'edit-sertifikasi' => $this->edit_sertifikasi($request),
            'edit-sertifikasi-dokumen' => $this->edit_sertifikasi_dokumen($request),
            'edit-sertifikasi-klausul' => $this->edit_sertifikasi_klausul($request),
            'edit-sertifikasi-klausul-tahap1' => $this->edit_sertifikasi_klausul_tahap1($request),
            default => null,
        };
    }
	
	private function edit_sertifikasi_klausul_tahap1( Request $request)
	{
		$data_sertifikat = MasterSertifikasi::findOrFail($request['sert_id']); 
        $data = MasterKlausulTahap1::findOrFail($request['klausul_thp1_id']); 
        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data, 'data_sertifikat' => $data_sertifikat];
        return view("master::sis_sertifikasi.edit-sertifikasi-klausul-tahap1")->with($parser);
	}
	
	private function edit_sertifikasi_klausul( Request $request)
	{
        $data_sertifikat = MasterSertifikasi::findOrFail($request['sert_id']); 
        $data = MasterSertifikasiKlausul::findOrFail($request['sert_klau_id']); 
        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data, 'data_sertifikat' => $data_sertifikat];
        return view("master::sis_sertifikasi.edit-sertifikasi-klausul")->with($parser);
	}
	
	private function edit_sertifikasi_dokumen( Request $request)
    {
		// Check apakah ID tersedia
        $data_sertifikat = MasterSertifikasi::findOrFail($request['sert_id']); 
        $data = MasterSertifikasiDokuman::findOrFail($request['sert_dok_id']); 
		
        $data_dok = MasterJenisDokPerusahaan::findOrFail($data->jenis_dok_perusahaan_id); 
        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data, 'data_sertifikat' => $data_sertifikat, 'data_dok' => $data_dok];
        return view("master::sis_sertifikasi.edit-sertifikasi-dokumen")->with($parser);
	}
	
	private function edit_sertifikasi( Request $request)
    {
		// Check apakah ID tersedia
        $data = MasterSertifikasi::findOrFail($request['sert_id']);
        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data];
        return view("master::sis_sertifikasi.edit-sertifikasi")->with($parser); // Lokasi di Modules\Master\Resources\views\sis_sertifikasi
	}
	

    public function update(Request $request)
    {
		$request->validate(['tipe' => 'required']);
		return match ($request['tipe']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'update-sertifikasi' => $this->update_sertifikasi($request),
            'update-sertifikasi-dokumen' => $this->update_sertifikasi_dokumen($request),
            'update-sertifikasi-klausul' => $this->update_sertifikasi_klausul($request),
            'update-sertifikasi-klausul-tahap1' => $this->update_sertifikasi_klausul_tahap1($request),
            default => null,
        };
    }
	
	private function update_sertifikasi_klausul_tahap1( Request $request)
	{
		$request->validate([
							'klausul_thp1_id' => 'required|integer',
							'sert_id' => 'required|integer',
							'klausul_thp1_nomor' => 'required|string',
							'klausul_thp1_peryataan' => 'required|string'
						]); // auto redirect back jika tidak valid
		$dataUpdate = [
            'klausul_thp1_nomor' => $request->klausul_thp1_nomor,
            'klausul_thp1_peryataan' => $request->klausul_thp1_peryataan
        ];

        try {
            $data = MasterKlausulTahap1::findOrFail($request['klausul_thp1_id'])
                ->update($dataUpdate);
            return redirect()->back()->with('message', "Update data berhasil");
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
	}
	
	private function update_sertifikasi_klausul( Request $request)
    {
        $request->validate([
							'sert_klau_id' => 'required|integer',
							'sert_id' => 'required|integer',
							'sert_klau_nomor' => 'required|string',
							'sert_klau_peryataan' => 'required|string'
						]); // auto redirect back jika tidak valid
		$dataUpdate = [
            'sert_klau_nomor' => $request->sert_klau_nomor,
            'sert_klau_peryataan' => $request->sert_klau_peryataan
        ];

        try {
            $data = MasterSertifikasiKlausul::findOrFail($request['sert_klau_id'])
                ->update($dataUpdate);
            return redirect()->back()->with('message', "Update data berhasil");
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
    }
	
	private function update_sertifikasi_dokumen(Request $request)
	{
		$request->validate([
            'sert_dok_id' => 'required|integer',
			'sert_dok_required' => 'required|string',
			'sert_dok_keterangan' => 'nullable|string'
		]); // auto redirect back jika tidak valid
		
		$dataUpdate = [
            'sert_dok_required'      => $request->sert_dok_required,
            'sert_dok_keterangan' => $request->sert_dok_keterangan
        ];
        try {
            //DB::beginTransaction(); // Jika mau menggunkan transaction
            $data = MasterSertifikasiDokuman::findOrFail($request['sert_dok_id'])
                ->update($dataUpdate);
            //DB::commit();
            return redirect()->back()->with('message', "Update data berhasil");
        } catch (Exception $e) {
            //DB::rollBack();
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
	}
	
	private function update_sertifikasi(Request $request)
	{
		 $request->validate([
            'sert_id' => 'required|integer',
			'sert_nama' => 'required|string',
			'sert_deskripsi' => 'required|string',
			'sert_expired' => 'required|integer',
			'sert_format_referensi' => 'required|string',
			'sert_is_product' => 'required|string'
		]); // auto redirect back jika tidak valid
		
		$dataUpdate = [
            'sert_nama'      => $request->sert_nama,
            'sert_deskripsi' => $request->sert_deskripsi,
            'sert_expired' => $request->sert_expired,
            'sert_format_referensi' => $request->sert_format_referensi,
            'sert_is_product' => $request->sert_is_product,
        ];
        try {
            //DB::beginTransaction(); // Jika mau menggunkan transaction
            $data = MasterSertifikasi::findOrFail($request['sert_id'])
                ->update($dataUpdate);
            //DB::commit();
            return redirect()->back()->with('message', "Update data berhasil");
        } catch (Exception $e) {
            //DB::rollBack();
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
	}
	
	public function destroy(Request $request)
    {
		$request->validate(['tipe' => 'required']);
		return match ($request['tipe']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'delete-sertifikasi' => $this->delete_sertifikasi($request),
            'delete-sertifikasi-dokumen' => $this->delete_sertifikasi_dokumen($request),
            'delete-sertifikasi-klausul' => $this->delete_sertifikasi_klausul($request),
            'delete-sertifikasi-klausul-tahap1' => $this->delete_sertifikasi_klausul_tahap1($request),
            default => null,
        };
    }
	
	private function delete_sertifikasi_klausul_tahap1(Request $request)
	{
		/*
        responseJSON : adalah helper standar untuk output JSON pada aplikasi (kecuali ajax easyui)
        Lokasi helper ada di App\Helpers\GlobalHelper
        */
		
		try {
            $status_return = TRUE;
            foreach ($request->ids as $id) {
                $data = MasterKlausulTahap1::where("klausul_thp1_id", $id)->firstOrFail();
                if ($data->delete()) {

                } else {
                    $status_return = FALSE;
                    break;
                }
            }

            if ($status_return == TRUE) {
                return responseJSON(200, [], "Berhasil menghapus data");
            } else {
                return responseJSON(500, [], "Terjadi kesalahan saat menghapus data");
            }
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
	}
	
	private function delete_sertifikasi_klausul(Request $request)
	{
		/*
        responseJSON : adalah helper standar untuk output JSON pada aplikasi (kecuali ajax easyui)
        Lokasi helper ada di App\Helpers\GlobalHelper
        */
		
		try {
            $status_return = TRUE;
            foreach ($request->ids as $id) {
                $data = MasterSertifikasiKlausul::where("sert_klau_id", $id)->firstOrFail();
                if ($data->delete()) {

                } else {
                    $status_return = FALSE;
                    break;
                }
            }

            if ($status_return == TRUE) {
                return responseJSON(200, [], "Berhasil menghapus data");
            } else {
                return responseJSON(500, [], "Terjadi kesalahan saat menghapus data");
            }
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
	}
	
	private function delete_sertifikasi_dokumen(Request $request)
	{
		/*
        responseJSON : adalah helper standar untuk output JSON pada aplikasi (kecuali ajax easyui)
        Lokasi helper ada di App\Helpers\GlobalHelper
        */
		
		try {
            $status_return = TRUE;
            foreach ($request->ids as $id) {
                $data = MasterSertifikasiDokuman::where("sert_dok_id", $id)->firstOrFail();
                if ($data->delete()) {

                } else {
                    $status_return = FALSE;
                    break;
                }
            }

            if ($status_return == TRUE) {
                return responseJSON(200, [], "Berhasil menghapus data");
            } else {
                return responseJSON(500, [], "Terjadi kesalahan saat menghapus data");
            }
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
	}
	
	private function delete_sertifikasi(Request $request)
	{
		/*
        responseJSON : adalah helper standar untuk output JSON pada aplikasi (kecuali ajax easyui)
        Lokasi helper ada di App\Helpers\GlobalHelper
        */
		
		try {
            $status_return = TRUE;
            foreach ($request->ids as $id) {
                $data = MasterSertifikasi::where("sert_id", $id)->firstOrFail();
                if ($data->delete()) {

                } else {
                    $status_return = FALSE;
                    break;
                }
            }

            if ($status_return == TRUE) {
                return responseJSON(200, [], "Berhasil menghapus data");
            } else {
                return responseJSON(500, [], "Terjadi kesalahan saat menghapus data");
            }
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
	}
}
