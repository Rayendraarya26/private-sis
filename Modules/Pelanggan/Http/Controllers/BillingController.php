<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisBilling;
use App\Models\BbkkpSis\SisPermohonanStatus;
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
    private $url = 'pelanggan/billing';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Billing'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view('pelanggan::billing.index')->with($parser);
    }

    public function upload(Request $request, $billing_id)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Billing', url($this->url)),
            new BreadcrumbsStruct('Upload'),
        ];

        $data = SisBilling::with('sis_billing_items')
            ->where("bill_id", $billing_id)
            ->where("cust_id", auth()->user()->sis_pelanggan->cust_id)
            ->firstOrFail();

        $totalBiling = $data->sis_billing_items->sum('itms_bil_total');

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $data, 'total_billing' => $totalBiling];
        return view("pelanggan::billing.upload")->with($parser);
    }

    public function processUpload(Request $request, $billing_id)
    {
        $request->validate([
            'bill_payment_date' => 'required',
            'bill_payment_file' => 'required|mimetypes:application/pdf,image/png,image/jpeg|max:2048000',
        ]);
        $billing = SisBilling::with('sis_billing_items')
            ->where("bill_id", $billing_id)
            ->where("cust_id", auth()->user()->sis_pelanggan->cust_id)
            ->firstOrFail();

        $totalBilling = 0;
        foreach ($billing->sis_billing_items as $det) {
            $totalBilling += $det->itms_bil_total;
        }

        try {
            $oldPath = [];
            $newPath = [];

            $filePath = sprintf(config("app.path_file_billing"), $billing->bill_id);
            if (!File::exists($filePath)) {
                File::makeDirectory($filePath, 0777, true, true);
            }
            if (!empty($billing->bill_payment_file)) {
                array_push($oldPath, public_path($billing->bill_payment_file));
            }

            DB::beginTransaction();
            $dataKuitansi = $request->file("bill_payment_file");
            $kuitansiName = Str::slug("bukti-pembayaran" . $dataKuitansi->getClientOriginalName()) . '-' . time() . '.' . $dataKuitansi->getClientOriginalExtension();
            $kuitansiPath = sprintf("%s/%s", $filePath, $kuitansiName);
            $dataKuitansi->move($filePath, $kuitansiName);
            $newPath[] = $kuitansiPath;

            $billing->bill_payment_status = 'menunggu konfirmasi';
            $billing->bill_payment_note   = $request['bill_payment_note'];
            $billing->bill_payment_date   = $request['bill_payment_date'];
            $billing->bill_payment_tipe   = 'transfer';
            $billing->bill_payment_file   = $kuitansiPath;
            $billing->save();

            // Notif ke finance
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
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid' => $this->ajax_datagrid($request),
            default    => responseJSON(404, null, "Invalid url"),
        };
    }

    private function ajax_datagrid(Request $request)
    {
        $data = SisBilling::with('sis_billing_items')
            ->where("cust_id", auth()->user()->sis_pelanggan->cust_id);
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
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['bill_id']             = $d->bill_id;
            $x['bill_nomor_billing']  = $d->bill_nomor_billing;
            $x['bill_due_date']       = $d->bill_due_date->format("Y-m-d");
            $x['bill_billing_date']   = $d->bill_billing_date->format("Y-m-d");
            $x['bill_payment_status'] = $d->bill_payment_status;
            $x['bill_payment_date']   = $d->bill_payment_date?->isoFormat('LLLL');
            $x['bill_payment_file']   = asset($d->bill_payment_file);
            $x['bill_payment_note']   = $d->bill_payment_note;
            $x['bill_invoice_file']   = asset($d->bill_invoice_file);
            $x['bill_items']          = $d->sis_billing_items;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
