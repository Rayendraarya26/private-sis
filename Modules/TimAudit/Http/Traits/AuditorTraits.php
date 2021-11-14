<?php

namespace Modules\TimAudit\Http\Traits;

use App\Models\BbkkpSis\SisJadwal;
use Exception;

trait AuditorTraits
{
    /** InvolvedAuditor
     * Cek apakah jadwal exist dan merupakan tim member, juga bagaimana status komitenya
     * @throws Exception
     */
    public function involvedAuditor(int $jadwalID)
    {
        $pegawaiID = auth()->user()->master_pegawai->peg_id;
        $data      = SisJadwal::with(['sis_jadwal_audits', 'sis_pelanggan'])
            ->with([
                'sis_jadwal_tims' => function ($query) use ($pegawaiID) {
                    $query->where('peg_id', $pegawaiID);
                }
            ])->where('jadw_id', $jadwalID)->first();

        if (empty($data)) throw new Exception("Data jadwal tidak ditemukan");

        $open = false;
        foreach ($data->sis_jadwal_audits as $ja) {
            if ($ja->jadw_audit_status_komite == 'on-going') $open = true;
        }

        if (!$open) throw new Exception("Audit sudah diajukan ke komite");

        return $data;
    }
}
