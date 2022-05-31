<?php

namespace Modules\Pelanggan\Http\Traits;

use App\Models\BbkkpSis\SisBillingItems;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisPermohonan;

trait PermohonanTrait
{
    // allowCancel memastikan permohonan belum masuk ke penjadwalan
    public function allowCancel(SisPermohonan $dataPemohon): bool
    {
        $dataBilling = SisBillingItems::with('sis_billing')->where('mohon_id', $dataPemohon->mohon_id)->first();
        if (!empty($dataBilling?->sis_billing?->bill_id)) {
            $billingID  = $dataBilling?->sis_billing?->bill_id;
            $dataJadwal = SisJadwal::where('bill_id', $billingID)->first();
            if (!empty($dataJadwal)) return false;
        }
        return true;
    }
}
