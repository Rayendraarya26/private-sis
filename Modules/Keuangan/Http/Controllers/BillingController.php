<?php

namespace Modules\Keuangan\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\EmailStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisBilling;
use App\Models\BbkkpSis\SisBillingItems;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SisPelangganSertifikasi;
use App\Models\BbkkpSis\SisPermohonan;
use App\Models\BbkkpSis\SisPermohonanStatus;
use App\Models\BbkkpSis\SysUser;
use App\Models\BbkkpSis\SysUserGroup;
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
    private $url = 'keuangan/billing';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Keuangan'),
            new BreadcrumbsStruct('Billing'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("keuangan::billing.index")->with($parser);
    }

    public function upload(Request $request, $billing_id)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Keuangan'),
            new BreadcrumbsStruct('Billing', url($this->url)),
            new BreadcrumbsStruct('Upload'),
        ];

        $data = SisBilling::with('sis_billing_items')
            ->where("bill_id", $billing_id)
            //  ->where("cust_id", auth()->user()->sis_pelanggan->cust_id)
            ->firstOrFail();

        $totalBiling = $data->sis_billing_items->sum('itms_bil_total');

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $data, 'total_billing' => $totalBiling];
        return view("keuangan::billing.upload")->with($parser);
    }

    public function processUpload(Request $request, $billing_id)
    {
        $request->validate([
            'bill_payment_date' => 'required',
            'bill_payment_file' => 'required|mimetypes:application/pdf,image/png,image/jpeg|max:2048000',
        ]);

        $billing = SisBilling::with('sis_billing_items')
            ->where("bill_id", $billing_id)
            // ->where("cust_id", auth()->user()->sis_pelanggan->cust_id)
            ->firstOrFail();

        $totalBilling = 0;
        foreach ($billing->sis_billing_items as $det) {
            $totalBilling += $det->itms_bil_total;
        }

        try {

            $oldPath = [];
            $newPath = [];

            $paymentDate = Carbon::createFromFormat('m/d/Y g:i A', Str::of($request['bill_payment_date'])->remove(','));
            $filePath    = sprintf(config("app.path_file_billing"), $billing_id);
            if (!File::exists($filePath)) {
                File::makeDirectory($filePath, 0777, true, true);
            }
            if (!empty($billing->bill_payment_file)) {
                $oldPath[] = public_path($billing->bill_payment_file);
            }

            DB::beginTransaction();
            $dataKuitansi = $request->file("bill_payment_file");
            $kuitansiName = Str::slug("bukti-pembayaran" . $dataKuitansi->getClientOriginalName()) . '-' . time() . '.' . $dataKuitansi->getClientOriginalExtension();
            $kuitansiPath = sprintf("%s/%s", $filePath, $kuitansiName);
            $dataKuitansi->move($filePath, $kuitansiName);
            $newPath[] = $kuitansiPath;

            $billing->bill_payment_status = $request['bill_payment_status'] == 'ya' ? 'lunas' : 'menunggu konfirmasi';
            $billing->bill_payment_note   = $request['bill_payment_note'];
            $billing->bill_payment_date   = $paymentDate;
            $billing->bill_payment_tipe   = 'transfer';
            $billing->bill_payment_file   = $kuitansiPath;
            $billing->save();

            // Notif ke finance
            if ($request['bill_payment_status'] != 'ya') {
                $groupUsers = SysUserGroup::with('user')->whereIn('ug_group_id', [7])->get();
                $timeNow    = Carbon::now();
                if ($groupUsers) {
                    foreach ($groupUsers as $user) {
                        $notifStruct = new NotifStruct();
                        // Send Push
                        $notifStruct->title     = sprintf("Billing #%d Lunas", $billing->bill_nomor_billing);
                        $notifStruct->message   = sprintf("%s telah membayar sebesar Rp %s", $billing->sis_pelanggan->cust_nama, moneyFormat($totalBilling));
                        $notifStruct->user_id   = $user?->ug_user_id;
                        $notifStruct->click_url = url(sprintf('/keuangan/billing/edit?tipe=pelunasan&bill_id=%d', $billing->bill_id));
                        sendNotification($notifStruct);

                        // Add Pengajuan Status
                        foreach ($billing->sis_billing_items as $det) {
                            SisPermohonanStatus::updateOrCreate([
                                "status_mohon_id" => $det->mohon_id,
                                "status_tipe"     => "informasi",
                                "status_judul"    => "Pemohon melakukan pelunasan pembayaran",
                                "status_pesan"    => sprintf("%s telah membayar biaya sertifikasi sebesar Rp %s", $billing->sis_pelanggan->cust_nama, moneyFormat($totalBilling)),
                                "created_at"      => $timeNow,
                            ], [
                                "updated_at" => $timeNow,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            if (count($oldPath) > 0) {
                foreach ($oldPath as $path) {
                    @unlink($path);
                }
            }
            return redirect($this->url)->with('message', "Upload bukti pembayaran berhasil");
        } catch (Exception $e) {
            DB::rollBack();
            if (count($newPath) > 0) {
                foreach ($newPath as $path) {
                    @unlink($path);
                }
            }
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-billing'       => $this->ajax_datagrid_billing($request),
            'datagrid-billing-items' => $this->ajax_datagrid_billing_items($request),
            'combogrid-pelanggan'    => $this->ajax_combogrid_pelanggan($request),
            'combogrid-permohonan'   => $this->ajax_combogrid_permohonan($request),
            'combogrid-sertifikat'   => $this->ajax_combogrid_sertifikat($request),
            'combobox-tipe'          => $this->ajax_combobox_tipe($request),
            default                  => null,
        };
    }

    private function ajax_datagrid_billing(Request $request)
    {
        $data = SisBilling::join('sis_billing_items', "sis_billing.bill_id", "=", "sis_billing_items.bill_id");
        $data->join('sis_pelanggan', "sis_billing.cust_id", "=", "sis_pelanggan.cust_id")
            ->leftJoin('sis_jadwal', "sis_billing.bill_id", "=", "sis_jadwal.bill_id");
        // Filter
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                if ($f->field == 'bill_status_pembayaran') {
                    if ($f->value == 'belum')
                        $data->whereNull('bill_payment_file');
                    else
                        $data->whereNotNull('bill_payment_file');
                } else {
                    $data->where($f->field, 'LIKE', '%' . $f->value . '%');
                }
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
        $data->select("*", DB::raw('SUM(sis_billing_items.itms_bil_total) as itms_bil_total'), "sis_billing.bill_id AS bill_id", "sis_jadwal.bill_id AS jdwl_bill_id")->skip(($request->page - 1) * $request->rows)->take($request->rows);
        $data->groupBy('sis_billing.bill_id');

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['can_delete'] = 'true';
            if ($d->jdwl_bill_id != '') {
                $x['can_delete'] = 'false';
            } else if ($d->bill_payment_status == 'menunggu konfirmasi') {
                $x['can_delete'] = 'false';
            } else if ($d->bill_payment_status == 'menunggu pembayaran') {
                $x['can_delete'] = 'false';
            } else if ($d->bill_payment_status == 'lunas') {
                $x['can_delete'] = 'false';
            }
            $x['jdwl_bill_id']           = ($d->jdwl_bill_id != '') ? 'terjadwalkan' : 'belum';
            $x['bill_status_pembayaran'] = ($d->bill_payment_file != '') ? 'sudah' : 'belum';
            $x['bill_payment_status']    = $d->bill_payment_status;
            $x['itms_bil_total']         = number_format($d->itms_bil_total, 2, ',', '.');
            $x['bill_id']                = $d->bill_id;
            $x['cust_nama']              = $d->cust_nama;
            $x['bill_nomor_billing']     = $d->bill_nomor_billing;
            $x['status_payment']         = ($d->bill_payment_file != '') ? 'ya' : 'tidak';
            $x['bill_invoice_file']      = $d->bill_invoice_file;
            $x['bill_payment_date']      = $d->bill_payment_date?->format("Y-m-d");
            $x['bill_due_date']          = $d->bill_due_date?->format("Y-m-d");
            $x['bill_billing_date']      = $d->bill_billing_date?->format("Y-m-d");
            $x['bill_harus_lunas']       = $d->bill_harus_lunas;
            $result[]                    = $x;
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_datagrid_billing_items(Request $request)
    {
        $data = SisBilling::join('sis_billing_items', "sis_billing.bill_id", "=", "sis_billing_items.bill_id");
        $data->join('sis_pelanggan', "sis_billing.cust_id", "=", "sis_pelanggan.cust_id");
        $data->leftJoin('sis_pelanggan_sertifikasi', "sis_billing_items.cust_sert_id", "=", "sis_pelanggan_sertifikasi.cust_sert_id");

        $data->where('sis_billing.bill_id', '=', $request['bill_id']);
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Pagination
        $data->select("*", "sis_billing_items.mohon_id AS mohon_id");

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['mohon_id']       = $d->mohon_id;
            $x['itms_bil_id']    = $d->itms_bil_id;
            $x['itms_bil_total'] = $d->itms_bil_total;
            $x['itms_bil_tipe']  = $d->itms_bil_tipe;
            $x['itms_bil_desc']  = $d->itms_bil_desc;
            $x['mohon_det_id']   = $d->mohon_det_id;
            $x['cust_sert_id']   = $d->cust_sert_id;
            $x['is_new']         = false;
            $x['tipe']           = 'data-billing';
            $x['bill_id']        = $d->bill_id;
            $result[]            = $x;
        }

        return response()->json(["rows" => $result]);
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
            $x['cust_id']     = $d->cust_id;
            $x['cust_nama']   = $d->cust_nama;
            $x['cust_alamat'] = $d->cust_alamat;
            $result[]         = $x;
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_permohonan(Request $request)
    {
        $data = SisPermohonan::join('sis_permohonan_detail', "sis_permohonan_detail.mohon_id", "=", "sis_permohonan.mohon_id")
            ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id");
        // Filter
        $data->where('mohon_approved_status', '=', 'accepted')
            ->where('mohon_cancel_status', '=', 'no')
            ->where('mohon_verif_kajian_permohonan_pjt', '=', 'ya')
            ->where('mohon_verif_kajian_permohonan_paskal', '=', 'ya')
            ->where('mohon_tagihan_biaya_status', '=', 'setuju')
            ->whereNotNull('mohon_pernyataan_persetujuan_file');
        if (empty($request->mohon_id)) {

            $data->whereNotIn('sis_permohonan_detail.mohon_id', function ($query) use ($request) {
                $query->selectRaw("mohon_id FROM sis_billing_items
										JOIN sis_billing ON sis_billing.bill_id = sis_billing_items.bill_id
										WHERE sis_billing.cust_id = '" . $request->cust_id . "'
										AND sis_billing_items.mohon_id IS NOT NULL
										");
            });
        } else {
            $data->where('sis_permohonan.mohon_id', $request->mohon_id);
        }
        /*  ->whereNotIn('sis_permohonan_detail.mohon_det_id', function ($query) use ($request) {
             $query->selectRaw("mohon_det_id AS id
                             FROM
                               sis_jadwal_audit
                               INNER JOIN sis_jadwal
                                 ON sis_jadwal.jadw_id = sis_jadwal_audit.jadw_id
                             WHERE mohon_det_id IS NOT NULL
                               AND cust_id = '". $request->cust_id ."'
                             GROUP BY mohon_det_id
                             UNION
                             SELECT
                               mohon_det_id AS id
                             FROM
                               sis_audit_tahap1
                               INNER JOIN sis_billing
                                 ON sis_billing.bill_id = sis_audit_tahap1.bill_id
                             WHERE cust_id = '". $request->cust_id ."'");
         }) */
        $data->where('cust_id', '=', $request->cust_id);
        /*
        if ($request->jenis_status == 're-sertifikasi') {
            $data->where('mohon_det_jenis_status', '=', 'lama');
        } else if ($request->jenis_status == 'sertifikasi') {
            $data->where('mohon_det_jenis_status', '=', 'baru');
        }
        */
        if (!empty($request->q)) {
            $data->where('sert_nama', 'LIKE', '%' . $request->q . '%');
        }
        // Total
        $total = $data->select(DB::raw('COUNT(DISTINCT sis_permohonan.mohon_id) as total'))->first()->total;
        // Pagination
        $data->select("*", DB::raw("GROUP_CONCAT(DISTINCT CONCAT(sert_nama, IF(mohon_det_jenis_status = 'baru', '(Baru)', '(Perpanjang)')) SEPARATOR ', ') as nama_sert"))->skip(($request->page - 1) * $request->rows)->take($request->rows);
        $data->groupBy('sis_permohonan.mohon_id');
        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['deskripsi']                = "Permohonan nomor #" . $d->mohon_id . " " . $d->nama_sert;
            $x['id']                       = $d->mohon_id;
            $x['nama']                     = $d->nama_sert;
            $x['cust_sert_id']             = $d->cust_sert_id;
            $x['mohon_id']                 = $d->mohon_id;
            $x['mohon_det_id']             = $d->mohon_det_id;
            $x['cust_id']                  = $d->cust_id;
            $x['user_id']                  = $d->user_id;
            $x['sert_id']                  = $d->sert_id;
            $x['sert_nama']                = $d->sert_nama;
            $x['mohon_harga_permohonan']   = $d->mohon_harga_permohonan;
            $x['mohon_harus_lunas_status'] = $d->mohon_harus_lunas_status;
            $x['mohon_cust_nama']          = $d->mohon_cust_nama;
            $x['mohon_jenis_status']       = $d->mohon_det_jenis_status;
            $x['created_at']               = $d->created_at?->format("Y-m-d H:i:s"); // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            $x['update_at']                = $d->update_at?->format("Y-m-d H:i:s");  // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            $result[]                      = $x;
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_sertifikat(Request $request)
    {
        $data = SisPelangganSertifikasi::join('master_sertifikasi', "sis_pelanggan_sertifikasi.sert_id", "=", "master_sertifikasi.sert_id");
        // Filter
        $data->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_pelanggan_sertifikasi.komodt_id");
        $data->where('cust_id', '=', $request->cust_id);

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
            $x['deskripsi'] = "Surveilans untuk sertifikat " . $d->sert_nama . " nomor referensi " . $d->cust_sert_nomor_referensi;
            $x['id']        = $d->cust_sert_id;
            $x['nama']      = $d->sert_nama;

            $x['komodt_id']        = $d->komodt_id;
            $x['komodt_nama']      = $d->komodt_nama;
            $x['kode_ea']          = $d->kode_ea_nama;
            $x['kode_nace']        = $d->kode_nace_nama;
            $x['tipe']             = $d->cust_sert_tipe;
            $x['merk']             = $d->cust_sert_merk;
            $x['nomor_sertifikat'] = $d->cust_sert_nomor_sertifikat;
            $x['nomor_referensi']  = $d->cust_sert_nomor_referensi;
            $x['nomor_sni']        = $d->cust_sert_nomor_sni;
            $x['lingkup']          = $d->cust_sert_lingkup;

            $x['cust_sert_tgl_sertifikat_awal']      = $d->cust_sert_tgl_sertifikat_awal?->format("Y-m-d");
            $x['cust_sert_tgl_sertifikat_perubahan'] = $d->cust_sert_tgl_sertifikat_perubahan?->format("Y-m-d");
            $result[]                                = $x;
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combobox_tipe(Request $request)
    {
        $data = [
            ['id' => 'lain-lain', 'name' => 'lain-lain'],
            ['id' => 'sertifikasi', 'name' => 'sertifikasi'],
            ['id' => 're-sertifikasi', 'name' => 're-sertifikasi'],
            ['id' => 'surveilans', 'name' => 'surveilans'],
        ];

        return response()->json($data);
    }

    public function create()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Keuangan'),
            new BreadcrumbsStruct('Billing'),
            new BreadcrumbsStruct('Input Billing'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("keuangan::billing.create")->with($parser);
    }

    public function store(Request $request)
    {
        $request->validate([
            "cust_id"            => 'required',
            "bill_nomor_billing" => 'required',
            "bill_billing_date"  => 'required',
            "bill_due_date"      => 'required',
            "data_billing_item"  => 'required',
            "bill_invoice_file"  => 'required',
            "bill_harus_lunas"   => 'required',
            "bill_total"         => 'required',
        ]);

        //print_r(json_decode($request['data_billing_item']));
        // Set data uploaded file path (digunakan untuk delete file yang diupload ketika catch error)
        $uploadedPath = [];
        try {
            if (!$request->hasFile('bill_invoice_file')) throw new ExpectedException("Mohon unggah file billing", 400);

            DB::beginTransaction();
            // 3.1 add sis_permohonan
            $newSisBilling                      = new SisBilling();
            $newSisBilling->cust_id             = $request['cust_id'];
            $newSisBilling->bill_nomor_billing  = $request['bill_nomor_billing'];
            $newSisBilling->bill_billing_date   = $request['bill_billing_date'];
            $newSisBilling->bill_due_date       = $request['bill_due_date'];
            $newSisBilling->bill_harus_lunas    = $request['bill_harus_lunas'];
            $newSisBilling->bill_payment_date   = ($request['bill_total'] == 0) ? Carbon::now() : null;
            $newSisBilling->bill_payment_status = ($request['bill_total'] == 0) ? 'lunas' : 'menunggu pembayaran';
            $newSisBilling->created_at          = Carbon::now();
            $newSisBilling->updated_at          = Carbon::now();
            $newSisBilling->save();

            // DEFINE BASE UPLOAD AND UPDATE bill_invoice_file
            $baseFileUpload  = sprintf(config("app.path_file_billing"), $newSisBilling->bill_id);
            $fileInvoice     = $request->file('bill_invoice_file');
            $fileInvoiceName = Str::slug('file-invoice-' . $fileInvoice->getClientOriginalName()) . '-' . time() . '.' . $fileInvoice->getClientOriginalExtension();
            $fileInvoicePath = sprintf("%s/%s", $baseFileUpload, $fileInvoiceName);
            $fileInvoice->move($baseFileUpload, $fileInvoiceName);
            $newSisBilling->bill_invoice_file = $fileInvoicePath;
            $newSisBilling->save();

            $uploadedPath[] = $fileInvoicePath;
            // add billing items
            $dataItems  = json_decode($request['data_billing_item']);
            $bil_total  = 0;
            $mohon_data = [];
            $iItems     = 0;
            foreach ($dataItems as $itm) {
                $iItems++;
                $cust_sert_id = null;
                $mohon_id     = null;
                $mohon_det_id = null;

                if (!is_null($itm->mohon_id) && $itm->bil_tipe != 'surveilans') {
                    $mohon_id = $itm->mohon_id;
                    // $mohon_det_id = $itm->mohon_det_id;
                    if (in_array($mohon_id, $mohon_data)) {
                        $mohon_data[] = $mohon_id;
                    }
                } else if (!is_null($itm->mohon_id) && $itm->bil_tipe == 'surveilans') {
                    $cust_sert_id = $itm->mohon_id;
                }

                $newSisBillingItems                = new SisBillingItems();
                $newSisBillingItems->bill_id       = $newSisBilling->bill_id;
                $newSisBillingItems->itms_bil_tipe = $itm->bil_tipe == 'surveilans' ? 'surveilans' : 'lain-lain';
                $newSisBillingItems->mohon_id      = $mohon_id;
                $newSisBillingItems->mohon_det_id  = $mohon_det_id;
                $newSisBillingItems->cust_sert_id  = $cust_sert_id;
                $newSisBillingItems->itms_bil_desc = $itm->bil_desc;
                $newSisBillingItems->created_at    = Carbon::now();
                $newSisBillingItems->updated_at    = Carbon::now();
                if ($iItems == count($dataItems)) {
                    $newSisBillingItems->itms_bil_total = $request['bill_total'];
                    $bil_total                          = $bil_total + $request['bill_total'];
                } else {
                    $newSisBillingItems->itms_bil_total = 0;
                }
                $newSisBillingItems->save();
                // $bil_total = $bil_total + $itm->bil_total;
            }

            if (!empty($mohon_data)) {
                $timeNow = Carbon::now();
                foreach ($mohon_data as $dt) {
                    SisPermohonanStatus::updateOrCreate([
                        "status_mohon_id" => $dt,
                        "status_tipe"     => "informasi",
                        "status_judul"    => "Informasi Pengajuan",
                        "status_pesan"    => sprintf("Permohonan dengan nomor #%s telah diinputkan pada billing, silahkan lihat pada menu Billing anda.", $dt),
                        "created_at"      => $timeNow,
                    ], [
                        "updated_at" => $timeNow,
                    ]);
                }
            }


            // $newSisBilling->bill_harus_lunas = $harus_lunas;
            $newSisBilling->save();

            DB::commit();
            $data_pelanggan = SisPelanggan::where('cust_id', $request['cust_id'])->select('user_id', 'cust_nama', 'cust_email')->first();
            // Send Push
            $notifStruct            = new NotifStruct();
            $notifStruct->title     = 'Billing Invoice';
            $notifStruct->message   = sprintf("Billing dengan nomor %s telah terbit, silahkan lakukan pembayaran dan konfirmasi ke sistem.", $request['bill_nomor_billing']);
            $notifStruct->user_id   = $data_pelanggan?->user_id;
            $notifStruct->click_url = url('/pelanggan/billing');
            sendNotification($notifStruct);

            // Send Email
            $structEmail          = new EmailStruct();
            $structEmail->subject = "Billing Invoice";
            $structEmail->body    = view('keuangan::billing.mails.publish')
                ->with([
                    'nama'       => $data_pelanggan?->cust_nama,
                    'message'    => sprintf("Billing dengan nomor %s telah terbit dengan total biaya Rp. %s, silahkan lakukan pembayaran dan konfirmasi ke sistem.", $request['bill_nomor_billing'], $bil_total),
                    'link_verif' => url('/pelanggan/billing'),
                ])->render();
            $structEmail->to      = $data_pelanggan?->cust_email;
            sendEmail($structEmail);

            return responseJSON(200, null, "Data billing berhasil disimpan.");
        } catch (Exception $e) {
            DB::rollBack();

            foreach ($uploadedPath as $delPath) { // delete uploaded file
                @unlink($delPath);
            }

            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));

            return responseJSON(500, null, $e->getMessage());
        }
    }

    public function detail(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Keuangan'),
            new BreadcrumbsStruct('Billing'),
            new BreadcrumbsStruct('Detail Billing'),
        ];

        $dataBilling = SisBilling::where('sis_billing.bill_id', $request['bill_id']);
        $dataBilling->join('sis_billing_items', "sis_billing.bill_id", "=", "sis_billing_items.bill_id");
        $dataBilling->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_billing.cust_id');
        $dataBilling->select("*", DB::raw('SUM(sis_billing_items.itms_bil_total) AS itms_bil_total'), "sis_billing.bill_id AS bill_id");
        $dataBilling->groupBy('sis_billing.bill_id');

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data_billing' => $dataBilling->get()[0]];
        return view("keuangan::billing.detail")->with($parser);
    }

    public function edit(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'data'      => $this->edit_data($request),
            'pelunasan' => $this->edit_pelunasan($request),
            default     => null,
        };
    }

    private function edit_data(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Keuangan'),
            new BreadcrumbsStruct('Billing'),
            new BreadcrumbsStruct('Edit Billing'),
        ];

        $dataBilling = SisBilling::where('sis_billing.bill_id', $request['bill_id']);
        $dataBilling->join('sis_billing_items', "sis_billing.bill_id", "=", "sis_billing_items.bill_id");
        $dataBilling->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_billing.cust_id');
        $dataBilling->select("*", DB::raw('SUM(sis_billing_items.itms_bil_total) AS itms_bil_total'), "sis_billing.bill_id AS bill_id", "sis_billing_items.itms_bil_id AS itms_bil_id");
        $dataBilling->groupBy('sis_billing.bill_id');

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data_billing' => $dataBilling->get()[0]];
        return view("keuangan::billing.edit_data")->with($parser);
    }

    private function edit_pelunasan(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Keuangan'),
            new BreadcrumbsStruct('Billing'),
            new BreadcrumbsStruct('Set Pelunasan'),
        ];

        $dataBilling = SisBilling::where('sis_billing.bill_id', $request['bill_id']);
        $dataBilling->join('sis_billing_items', "sis_billing.bill_id", "=", "sis_billing_items.bill_id");
        $dataBilling->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_billing.cust_id');
        $dataBilling->select("*", DB::raw('SUM(sis_billing_items.itms_bil_total) AS itms_bil_total'), "sis_billing.bill_id AS bill_id");
        $dataBilling->groupBy('sis_billing.bill_id');

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data_billing' => $dataBilling->get()[0]];
        return view("keuangan::billing.edit_pelunasan")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'data'            => $this->update_data($request),
            'data-billing'    => $this->update_data_billing($request),
            'pelunasan'       => $this->update_pelunasan($request),
            'reset-pelunasan' => $this->update_belum_pelunasan($request),
            default           => null,
        };
    }

    private function update_data(Request $request)
    {
        $request->validate([
            "bil_id"             => 'required',
            "bill_nomor_billing" => 'required',
            "bill_billing_date"  => 'required',
            "bill_due_date"      => 'required',
            "bill_harus_lunas"   => 'nullable',
            "bill_invoice_file"  => 'nullable',
            "bill_total"         => 'required',
            "itms_bil_ids"       => 'nullable',
        ]);

        $dataUpdate = [
            'bill_nomor_billing'  => $request->bill_nomor_billing,
            'bill_billing_date'   => $request->bill_billing_date,
            'bill_due_date'       => $request->bill_due_date,
            'bill_harus_lunas'    => $request->bill_harus_lunas == 'ya' ? 'ya' : 'tidak',
            'bill_payment_date'   => $request->bill_total == 0 ? Carbon::now() : null,
            'bill_payment_status' => $request->bill_total == 0 ? 'lunas' : 'menunggu pembayaran',
        ];

        try {
            if ($request->hasFile("bill_invoice_file")) {
                $baseFileUpload  = sprintf(config("app.path_file_billing"), $request->bil_id);
                $fileInvoice     = $request->file('bill_invoice_file');
                $fileInvoiceName = Str::slug('file-invoice-' . $fileInvoice->getClientOriginalName()) . '-' . time() . '.' . $fileInvoice->getClientOriginalExtension();
                $fileInvoicePath = sprintf("%s/%s", $baseFileUpload, $fileInvoiceName);
                $fileInvoice->move($baseFileUpload, $fileInvoiceName);
                $dataUpdate['bill_invoice_file'] = $fileInvoicePath;
            }

            SisBilling::findOrFail($request['bil_id'])->update($dataUpdate);

            SisBillingItems::where("bill_id", $request['bil_id'])->update(['itms_bil_total' => 0]);
            SisBillingItems::where("itms_bil_id", $request['itms_bil_ids'])->firstOrFail()->update(['itms_bil_total' => $request->bill_total]);

            return redirect($this->url)->with('message', "Billing berhasil diubah untuk nomor #" . $request->bill_nomor_billing . " sudah berhasil disimpan.");
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return redirect($this->url)->with('message', $e->getMessage());
        }
    }

    private function update_data_billing(Request $request)
    {
        $request->validate([
            "bill_id"        => 'required',
            "itms_bil_tipe"  => 'required',
            "itms_bil_total" => 'required',
            "itms_bil_desc"  => 'required',
            "mohon_id"       => 'nullable',
            "cust_sert_id"   => 'nullable',
            "itms_bil_id"    => 'nullable',
        ]);

        try {
            $cust_sert_id = null;
            $mohon_id     = null;

            if ($request->itms_bil_tipe != 'surveilans') {
                $mohon_id = $request->mohon_id;
            } else if ($request->itms_bil_tipe == 'surveilans') {
                $cust_sert_id = $request->cust_sert_id;
            }

            if ($request->itms_bil_id != '') {
                $dataUpdate = [
                    'bill_id'        => $request->bill_id,
                    'itms_bil_tipe'  => $request->itms_bil_tipe,
                    'itms_bil_total' => $request->itms_bil_total,
                    'itms_bil_desc'  => $request->itms_bil_desc,
                    'mohon_id'       => $mohon_id,
                    'cust_sert_id'   => $cust_sert_id,
                ];
                SisBillingItems::findOrFail($request['itms_bil_id'])->update($dataUpdate);
            } else {
                $dataInsert = [
                    'bill_id'        => $request->bill_id,
                    'itms_bil_tipe'  => $request->itms_bil_tipe,
                    'itms_bil_total' => $request->itms_bil_total,
                    'itms_bil_desc'  => $request->itms_bil_desc,
                    'mohon_id'       => $mohon_id,
                    'cust_sert_id'   => $cust_sert_id,
                ];
                SisBillingItems::create($dataInsert);
            }


            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return responseJSON(500, [], $e->getMessage());
        }

    }

    private function update_pelunasan(Request $request)
    {
        $request->validate([
            "bil_id"  => 'required',
            "cust_id" => 'required',
        ]);
        SisBilling::findOrFail($request['bil_id'])->update(['bill_payment_status' => 'lunas']);
        try {
            $data_pelanggan = SisPelanggan::where('cust_id', $request['cust_id'])->select('user_id', 'cust_nama', 'cust_email')->first();
            // Send Push
            $notifStruct            = new NotifStruct();
            $notifStruct->title     = 'Informasi Billing';
            $notifStruct->message   = sprintf("Billing dengan nomor %s telah dinyatakan LUNAS.", $request['bill_nomor_billing']);
            $notifStruct->user_id   = $data_pelanggan?->user_id;
            $notifStruct->click_url = url('/pelanggan/billing');
            sendNotification($notifStruct);

            $dataUser = SysUser::whereIn('ug_group_id', ['6'])->select('*')->join('sys_user_group', 'ug_user_id', '=', 'user_id');
            foreach ($dataUser->get() as $us) {
                $notifUsr            = new NotifStruct();
                $notifUsr->title     = 'Informasi Billing';
                $notifUsr->message   = sprintf("Billing dengan nomor %s telah dinyatakan LUNAS, silahkan lakukan penjadwalan.", $request['bill_nomor_billing']);
                $notifUsr->user_id   = $us->user_id;
                $notifUsr->click_url = url('/operatorls/penjadwalan');
                sendNotification($notifUsr);
            }

            // Send Email
            $structEmail          = new EmailStruct();
            $structEmail->subject = "Informasi Billing";
            $structEmail->body    = view('keuangan::billing.mails.publish')
                ->with([
                    'nama'       => $data_pelanggan?->cust_nama,
                    'message'    => sprintf("Billing dengan nomor %s telah dinyatakan LUNAS.", $request['bill_nomor_billing']),
                    'link_verif' => url('/pelanggan/billing'),
                ])->render();
            $structEmail->to      = $data_pelanggan?->cust_email;
            sendEmail($structEmail);

            return redirect($this->url)->with('message', "Nomor biling #" . $request->bill_nomor_billing . " sudah berhasil dilunaskan.");
        }catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return redirect()->back()->withInput($request->except('_token'));
        }
    }

    private function update_belum_pelunasan(Request $request)
    {
        $request->validate([
            "bil_id" => 'required',
        ]);
        SisBilling::findOrFail($request['bil_id'])->update(['bill_payment_status' => 'menunggu konfirmasi']);
        return responseJSON(200, [], "Data berhasil di-set menjadi belum lunas atau proses.");
    }

    public function destroy(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'data_billing' => $this->delete_data_billing($request),
            'data_items'   => $this->delete_data_items($request),
            default        => null,
        };
    }

    private function delete_data_billing(Request $request)
    {
        try {
            $status_return = true;
            if (!empty($request->ids)) {
                foreach ($request->ids as $id) {
                    $data = SisBilling::where("bill_id", $id)->firstOrFail();
                    if ($data->delete()) {

                    } else {
                        $status_return = false;
                        break;
                    }
                }
            } else {
                $status_return = false;
            }

            if ($status_return) {
                return responseJSON(200, [], "Berhasil menghapus data");
            } else {
                return responseJSON(500, [], "Terjadi kesalahan saat menghapus data, data belum dipilih atau kesalahan system, silahkan ulangi lagi.");
            }
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return responseJSON(500, [], $e->getMessage());
        }
    }

    private function delete_data_items(Request $request)
    {
        try {
            $status_return = true;

            $data = SisBilling::where("sis_billing.bill_id", $request->bill_id);
            $data->join('sis_billing_items', "sis_billing.bill_id", "=", "sis_billing_items.bill_id");

            $total_data  = 0;
            $harga_total = 0;
            $item        = [];
            foreach ($data->get() as $d) {
                $total_data++;
                if ($d->itms_bil_total > 0) {
                    $harga_total = $d->itms_bil_total;
                }
                $item[] = $d->itms_bil_id;
            }

            if ($total_data > 1) {
                foreach ($request->ids as $id) {
                    if (($key = array_search($id, $item)) !== false) {
                        unset($item[$key]);
                    }

                    $dataItems = SisBillingItems::where("itms_bil_id", $id)->firstOrFail();
                    if ($dataItems->delete()) {

                    } else {
                        $status_return = false;
                        break;
                    }
                }

                if ($status_return == true) {
                    SisBillingItems::where("bill_id", $request['bill_id'])->update(['itms_bil_total' => 0]);

                    $k = array_rand($item);
                    $v = $item[$k];

                    SisBillingItems::where("itms_bil_id", $v)->firstOrFail()->update(['itms_bil_total' => $harga_total]);
                    return responseJSON(200, [], "Berhasil menghapus data");
                } else {
                    return responseJSON(500, [], "Terjadi kesalahan saat menghapus data");
                }
            } else {
                return responseJSON(500, [], "Data items billing harus lebih dari 1.");
            }
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
