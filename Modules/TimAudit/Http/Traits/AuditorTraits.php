<?php

namespace Modules\TimAudit\Http\Traits;

use App\Exceptions\ExpectedException;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisJadwal;
use Exception;
use Illuminate\Support\Facades\DB;

trait AuditorTraits
{
    /** InvolvedAuditor
     * Cek apakah jadwal exist dan merupakan tim member, juga bagaimana status komitenya
     * @throws Exception
     */
    public function involvedAuditor(int $jadwalID)
    {
        $pegawaiID = auth()->user()->master_pegawai->peg_id;
        $data      = SisJadwal::with(['sis_jadwal_audits', 'sis_pelanggan', 'sis_jadwal_tims.master_pegawai', 'sis_audit_lks'])
            ->where('jadw_id', $jadwalID)->first();

        if (empty($data)) throw new ExpectedException("Data jadwal tidak ditemukan");

        $involved = false;
        foreach ($data->sis_jadwal_tims as $tim) {
            if ($tim->peg_id == $pegawaiID && in_array($tim->jadw_tim_posisi, ['ketua', 'auditor'])) $involved = true;
        }
        if (!$involved) throw new ExpectedException("Anda tidak bergabung dalam Tim Auditor sebagai ketua/auditor");

        $open = false;
        foreach ($data->sis_jadwal_audits as $ja) {
            if ($ja->jadw_audit_status_komite == 'on-going') $open = true;
        }

        if (!$open) throw new ExpectedException("Proses audit sudah diajukan ke Komite");

        return $data;
    }

    /** InvolvedAuditor
     * Cek apakah jadwal exist dan merupakan tim member, juga bagaimana status komitenya + filter hanya audit lks dengan kode auditor tertentu
     * @throws Exception
     */
    public function involvedAuditorWithFilter(int $jadwalID, string $auditorKode, $status = "all")
    {
        $pegawaiID = auth()->user()->master_pegawai->peg_id;
        $data      = SisJadwal::with(['sis_jadwal_audits', 'sis_pelanggan', 'sis_jadwal_tims.master_pegawai']);
        $data      = $data->where('jadw_id', $jadwalID);


        $data = $data->with([
            'sis_audit_lks' => function ($query) use ($auditorKode, $status) {
                $data = $query->join("sis_jadwal_tim", "sis_jadwal_tim.jadw_tim_id", "sis_audit_lks.jadw_tim_id")
                    ->orderBy(DB::raw("FIELD(sis_jadwal_tim.jadw_tim_posisi, 'ketua', 'auditor', 'ppc', 'observer')"))
                    ->orderBy("sis_jadwal_tim.jadw_tim_id");
                if ($auditorKode != "all") {
                    $data = $data->where('sis_jadwal_tim.jadw_tim_kode', $auditorKode);
                }
                if ($status != 'all'){
                    $data = $data->where('lks_status', $status);
                }
                return $data;
            }]);
        
        $data = $data->first();

        if (empty($data)) throw new ExpectedException("Data jadwal tidak ditemukan");

        $involved = false;
        foreach ($data->sis_jadwal_tims as $tim) {
            if ($tim->peg_id == $pegawaiID && in_array($tim->jadw_tim_posisi, ['ketua', 'auditor'])) $involved = true;
        }
        if (!$involved) throw new ExpectedException("Anda tidak bergabung dalam Tim Auditor sebagai ketua/auditor");

        $open = false;
        foreach ($data->sis_jadwal_audits as $ja) {
            if ($ja->jadw_audit_status_komite == 'on-going') $open = true;
        }

        if (!$open) throw new ExpectedException("Proses audit sudah diajukan ke Komite");

        return $data;
    }

    /** isKepalaAudit
     * Validasi agar harus kepala audit yang boleh mengkases
     * @throws Exception
     */
    public function isKepalaAudit(int $jadwalID)
    {
        $pegawaiID = auth()->user()->master_pegawai->peg_id;
        $data      = SisJadwal::with([
            'sis_jadwal_audits.sis_permohonan',
            'sis_jadwal_audits.master_komoditi',
            'sis_audit_lks',
            'sis_pelanggan',
            'sis_jadwal_tims',
            'sis_audit_lap_lengkap',
            'sis_audit_lap_ringkas',
            'sis_audit_ppcs',
            'sis_billing'
        ])
            ->where('jadw_id', $jadwalID)->first();

        if (empty($data)) throw new ExpectedException("Data jadwal tidak ditemukan");

        $involved = false;
        foreach ($data->sis_jadwal_tims as $tim) {
            if ($tim->peg_id == $pegawaiID && in_array($tim->jadw_tim_posisi, ['ketua'])) $involved = true;
        }
        if (!$involved) throw new ExpectedException("Anda bukan Kepala Auditor");

        $open = false;
        foreach ($data->sis_jadwal_audits as $ja) {
            if ($ja->jadw_audit_status_komite == 'on-going') $open = true;
        }

        if (!$open) throw new ExpectedException("Proses audit sudah diajukan ke Komite");

        return $data;
    }

    /** isKepalaAuditDetail
     * Validasi agar harus kepala audit yang boleh mengkases
     * @throws Exception
     */
    public function isKepalaAuditDetail(int $jadwalID)
    {
        $pegawaiID = auth()->user()->master_pegawai->peg_id;
        $data      = SisJadwal::with([
            'sis_jadwal_audits.sis_permohonan',
            'sis_jadwal_audits.master_komoditi',
            'sis_audit_lks',
            'sis_pelanggan',
            'sis_jadwal_tims',
            'sis_audit_lap_lengkap',
            'sis_audit_lap_ringkas',
            'sis_audit_ppcs',
            'sis_billing'
        ])
            ->where('jadw_id', $jadwalID)->first();

        if (empty($data)) throw new ExpectedException("Data jadwal tidak ditemukan");

        $involved = false;
        foreach ($data->sis_jadwal_tims as $tim) {
            if ($tim->peg_id == $pegawaiID && in_array($tim->jadw_tim_posisi, ['ketua'])) $involved = true;
        }
        if (!$involved) throw new ExpectedException("Anda bukan Kepala Auditor");

        return $data;
    }

    /**
     * @throws Exception
     */
    public function isKepalaKomite(int $jadwalID)
    {
        $pegawaiID = auth()->user()->master_pegawai->peg_id;
        $data      = SisJadwal::with([
            'sis_jadwal_audits.sis_permohonan',
            'sis_jadwal_audits.master_komoditi',
            'sis_audit_lks',
            'sis_pelanggan',
            'sis_audit_tim_komites',
            'sis_audit_lap_lengkap',
            'sis_audit_lap_ringkas',
            'sis_billing'
        ])
            ->where('jadw_id', $jadwalID)->first();

        if (empty($data)) throw new ExpectedException("Data jadwal tidak ditemukan");

        $involved = false;
        foreach ($data->sis_audit_tim_komites as $tim) {
            if ($tim->peg_id == $pegawaiID && in_array($tim->komite_posisi, ['ketua'])) $involved = true;
        }
        if (!$involved) throw new ExpectedException("Anda bukan Kepala Auditor");

        $open = false;
        foreach ($data->sis_jadwal_audits as $ja) {
            if ($ja->jadw_audit_status_komite == 'submited') $open = true;
        }

        if (!$open) throw new ExpectedException("Proses audit belum diajukan ke Komite");

        return $data;
    }

    public function sendNotifToLeadAuditorIfAllClose(int $jadwalID)
    {
        $data     = SisJadwal::with('sis_audit_lks')->find($jadwalID);
        $dataLead = $data->sis_jadwal_tims()->where('jadw_tim_posisi', 'ketua')->first();
        if (!empty($data)) {
            $allClosed = true;
            foreach ($data->sis_audit_lks as $lks) {
                if ($lks->lks_sudah_ditutup == "tidak") {
                    $allClosed = false;
                }
            }

            if ($allClosed) {
                // Send Notif ke Ketua Tim Auditor
                // Send Push
                $notifStruct            = new NotifStruct();
                $notifStruct->title     = 'Isikan Rekomendasi';
                $notifStruct->message   = sprintf("Semua LKS pada perusahaan %s telah disi dan di tutup, segera isikan rekomendasi", $data->sis_pelanggan->cust_nama);
                $notifStruct->user_id   = $dataLead->master_pegawai->user_id;
                $notifStruct->click_url = url('/timaudit/auditor/lks');
                sendNotification($notifStruct);
            }
        }
    }
}
