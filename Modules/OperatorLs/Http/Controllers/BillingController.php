<?php

namespace Modules\OperatorLs\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SisPermohonan;
use App\Models\BbkkpSis\SisPermohonanDokumen;
use App\Models\BbkkpSis\SisPermohonanKomoditi;
use App\Models\BbkkpSis\SisPermohonanPabrik;
use App\Models\BbkkpSis\SisPermohonanStatus;
use App\Models\BbkkpSis\SisBilling;
use App\Models\BbkkpSis\SisBillingItems;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BillingController extends Controller
{
	public $module = self::class;
    private $url = 'operatorls/billing';
	
    public function index()
    {
		$breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Billing'),
        ];
		
        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("operatorls::billing.index")->with($parser);
    }
	
	public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-billing'       => $this->ajax_datagrid_billing($request),
            'combogrid-pelanggan'       => $this->ajax_combogrid_pelanggan($request),
            'combogrid-permohonan'       => $this->ajax_combogrid_permohonan($request),
            default                     => null,
        };
    }
	
	private function ajax_combogrid_permohonan(Request $request)
	{
		$data = SisPermohonan::join('master_sertifikasi', "sis_permohonan.sert_id", "=", "master_sertifikasi.sert_id");
        // Filter
		$data->where('mohon_approved_status', '=', 'accepted');
		$data->whereNotNull('mohon_kajian_permohonan_file');
		$data->whereNotNull('mohon_pernyataan_persetujuan_file');
		$data->where('cust_id', '=', $request->cust_id);
		
		if($request->jenis_status == 're-sertifikasi'){
			$data->where('mohon_jenis_status', '=', 'lama');
		}
		else if($request->jenis_status == 'sertifikasi'){
			$data->where('mohon_jenis_status', '=', 'baru');
		}
		
        if (!empty($request->q)) {
            $data->where('sert_nama', 'LIKE', '%' . $request->q . '%');
        }
        // Total
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['cust_sert_id']          = $d->cust_sert_id;
            $x['mohon_id']              = $d->mohon_id;
            $x['cust_id']               = $d->cust_id;
            $x['user_id']               = $d->user_id;
            $x['sert_id']               = $d->sert_id;
            $x['sert_nama']             = $d->sert_nama;
            $x['mohon_harga_permohonan']       = $d->mohon_harga_permohonan;
            $x['mohon_harus_lunas_status']       = $d->mohon_harus_lunas_status;
            $x['mohon_cust_nama']       = $d->mohon_cust_nama;
            $x['mohon_jenis_status']    = $d->mohon_jenis_status;
            $x['created_at']            = $d->created_at?->format("Y-m-d H:i:s"); // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            $x['update_at']             = $d->update_at?->format("Y-m-d H:i:s");  // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
	}
	
	private function ajax_combogrid_pelanggan(Request $request)
	{
		$data = SisPelanggan::orderBy("cust_nama");
        // Filter
		if (!empty($request->q)) {
            $data->where('cust_nama', 'LIKE', '%' . $request->q . '%');
        }
		
        // Total
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
			$x['cust_id']         = $d->cust_id;
            $x['cust_nama']       = $d->cust_nama;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
	}
	
	private function ajax_datagrid_billing(Request $request)
    {
        $data = SisBilling::join('sis_billing_items', "sis_billing.bill_id", "=", "sis_billing_items.bill_id");
		$data->join('sis_pelanggan', "sis_billing.cust_id", "=", "sis_pelanggan.cust_id");
        // Filter
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
        $total = $data->select(DB::raw('count(distinct sis_billing.bill_id) as total'))->first()->total;
        // Pagination
        $data->select("*" , DB::raw('SUM(sis_billing_items.itms_bil_total) as itms_bil_total'))->skip(($request->page - 1) * $request->rows)->take($request->rows);
		$data->groupBy('sis_billing.bill_id');

        // Result
        $result = [];
        foreach ($data->get() as $d) {
			$x['bill_payment_status']         = $d->bill_payment_status;
			$x['itms_bil_total']         = number_format($d->itms_bil_total, 2, ',', '.');
			$x['bill_id']         = $d->bill_id;
            $x['cust_nama']       = $d->cust_nama;
            $x['bill_nomor_billing']    = $d->bill_nomor_billing;
            $x['bill_file_spk']    = $d->bill_file_spk;
            $x['bill_invoice_file']    = $d->bill_invoice_file;
            $x['bill_payment_date']            = $d->bill_payment_date?->format("Y-m-d");
            $x['bill_due_date']            = $d->bill_due_date?->format("Y-m-d");
            $x['bill_billing_date']             = $d->bill_billing_date?->format("Y-m-d");
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    public function create()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Billing'),
            new BreadcrumbsStruct('Input Billing'),
        ];
		
        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
		return view("operatorls::billing.create")->with($parser);
    }

    public function store(Request $request)
    {
        $request->validate([
            "cust_id" => 'required',
            "bill_nomor_billing"    => 'required',
            "bill_billing_date"   => 'required',
            "bill_due_date"   => 'required',
            "data_billing_item"   => 'required',
            "bill_invoice_file"   => 'required',
        ]);

        // Set data uploaded file path (digunakan untuk delete file yang diupload ketika catch error)
        $uploadedPath = [];
        try {
            if (!$request->hasFile('bill_invoice_file')) throw new Exception("Mohon unggah file billing", 400);
            
            DB::beginTransaction();
            // 3.1 add sis_permohonan
            $newSisBilling		                              = new SisBilling();
            $newSisBilling->cust_id                           = $request['cust_id'];
            $newSisBilling->bill_nomor_billing                = $request['bill_nomor_billing'];
            $newSisBilling->bill_billing_date                      = $request['bill_billing_date'];
            $newSisBilling->bill_due_date                      = $request['bill_due_date'];
            $newSisBilling->created_at                        = Carbon::now();
            $newSisBilling->updated_at                        = Carbon::now();
            $newSisBilling->save();

            // DEFINE BASE UPLOAD AND UPDATE bill_invoice_file
            $baseFileUpload     = sprintf(config("app.path_file_pengajuan"), $newSisBilling->bill_id);
            $fileInvoice     = $request->file('bill_invoice_file');
            $fileInvoiceName = Str::slug('pertanyaan-tambahan' . $fileInvoice->getClientOriginalName()) . '-' . time() . '.' . $fileInvoice->getClientOriginalExtension();
            $fileInvoicePath = sprintf("%s/%s", $baseFileUpload, $fileInvoiceName);
            $fileInvoice->move($baseFileUpload, $fileInvoiceName);
            array_push($uploadedPath, $fileInvoicePath);
            $newSisBilling->bill_invoice_file = $fileInvoicePath;
            $newSisBilling->save();

            // add billing items
            $dataItems = json_decode($request['data_billing_item']);
			$harus_lunas = 'ya';
			foreach ($dataItems as $itm) {
				$newSisBillingItems                                                = new SisBillingItems();
				$newSisBillingItems->bill_id                                       = $newSisBilling->bill_id;
				$newSisBillingItems->itms_bil_tipe                                      = $itm->bil_tipe;
				$newSisBillingItems->mohon_id                                      = $itm->mohon_id;
				$newSisBillingItems->itms_bil_desc                                      = $itm->bil_desc;
				$newSisBillingItems->itms_bil_total                                      = $itm->bil_total;
				$newSisBillingItems->created_at                                     = Carbon::now();
				$newSisBillingItems->updated_at                                     = Carbon::now();
				$newSisBillingItems->save();
				
				if(!is_null($itm->mohon_id)){
					SisPermohonanStatus::create([
						"status_mohon_id" => $itm->mohon_id,
						"status_tipe"     => "informasi",
						"status_judul"    => "Informasi Pengajuan",
						"status_pesan"    => sprintf("Permohonan dengan nomor #%s telah diinputkan pada billing, silahkan lihat pada menu Billing anda.", $itm->mohon_id),
						"created_at"      => Carbon::now(),
						"updated_at"      => Carbon::now(),
					]);
					
					if($itm->bil_lunas == 'tidak'){
						$harus_lunas = 'tidak';
					}
				}
			}
			
			$newSisBilling->bill_harus_lunas = $harus_lunas;
            $newSisBilling->save();
			
            DB::commit();

            return responseJSON(200, null, "Permohonan berhasil dan sedang tahap verifikasi");
        } catch (Exception $e) {
            DB::rollBack();

            foreach ($uploadedPath as $delPath) { // delete uploaded file
                @unlink($delPath);
            }

            return responseJSON(500, null, $e->getMessage());
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function detail(Request $request)
    {
        return view('operatorls::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('operatorls::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request)
    {
        /*
        responseJSON : adalah helper standar untuk output JSON pada aplikasi (kecuali ajax easyui)
        Lokasi helper ada di App\Helpers\GlobalHelper
        */
        try {
            $status_return = TRUE;
            foreach ($request->ids as $id) {
                $data = SisBilling::where("bill_id", $id)->firstOrFail();
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
