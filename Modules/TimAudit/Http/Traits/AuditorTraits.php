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
        $data      = SisJadwal::with(['sis_jadwal_audits', 'sis_pelanggan', 'sis_jadwal_tims'])
            ->where('jadw_id', $jadwalID)->first();

        if (empty($data)) throw new Exception("Data jadwal tidak ditemukan");

        $involved = false;
        foreach ($data->sis_jadwal_tims as $tim) {
            if ($tim->peg_id == $pegawaiID && in_array($tim->jadw_tim_posisi, ['ketua', 'auditor'])) $involved = true;
        }
        if (!$involved) throw new Exception("Anda tidak bergabung dalam Tim Auditor sebagai ketua/auditor");

        $open = false;
        foreach ($data->sis_jadwal_audits as $ja) {
            if ($ja->jadw_audit_status_komite == 'on-going') $open = true;
        }

        if (!$open) throw new Exception("Proses audit sudah diajukan ke Komite");

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
            'sis_jadwal_audits.sis_audit_lks',
            'sis_jadwal_audits.sis_permohonan',
            'sis_jadwal_audits.master_komoditi',
            'sis_pelanggan',
            'sis_jadwal_tims',
            'sis_audit_lap_lengkap',
            'sis_audit_lap_ringkas',
            'sis_audit_ppcs',
            'sis_billing'
        ])
            ->where('jadw_id', $jadwalID)->first();

        if (empty($data)) throw new Exception("Data jadwal tidak ditemukan");

        $involved = false;
        foreach ($data->sis_jadwal_tims as $tim) {
            if ($tim->peg_id == $pegawaiID && in_array($tim->jadw_tim_posisi, ['ketua'])) $involved = true;
        }
        if (!$involved) throw new Exception("Anda bukan Kepala Auditor");

        $open = false;
        foreach ($data->sis_jadwal_audits as $ja) {
            if ($ja->jadw_audit_status_komite == 'on-going') $open = true;
        }

        if (!$open) throw new Exception("Proses audit sudah diajukan ke Komite");

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
            'sis_jadwal_audits.sis_audit_lks',
            'sis_jadwal_audits.sis_permohonan',
            'sis_jadwal_audits.master_komoditi',
            'sis_pelanggan',
            'sis_jadwal_tims',
            'sis_audit_lap_lengkap',
            'sis_audit_lap_ringkas',
            'sis_audit_ppcs',
            'sis_billing'
        ])
            ->where('jadw_id', $jadwalID)->first();

        if (empty($data)) throw new Exception("Data jadwal tidak ditemukan");

        $involved = false;
        foreach ($data->sis_jadwal_tims as $tim) {
            if ($tim->peg_id == $pegawaiID && in_array($tim->jadw_tim_posisi, ['ketua'])) $involved = true;
        }
        if (!$involved) throw new Exception("Anda bukan Kepala Auditor");

        return $data;
    }
	
	public function isKepalaKomite(int $jadwalID)
    {
        $pegawaiID = auth()->user()->master_pegawai->peg_id;
        $data      = SisJadwal::with([
            'sis_jadwal_audits.sis_audit_lks',
            'sis_jadwal_audits.sis_permohonan',
            'sis_jadwal_audits.master_komoditi',
            'sis_pelanggan',
            'sis_audit_tim_komites',
            'sis_audit_lap_lengkap',
            'sis_audit_lap_ringkas',
            'sis_billing'
        ])
            ->where('jadw_id', $jadwalID)->first();

        if (empty($data)) throw new Exception("Data jadwal tidak ditemukan");

        $involved = false;
        foreach ($data->sis_audit_tim_komites as $tim) {
            if ($tim->peg_id == $pegawaiID && in_array($tim->komite_posisi, ['ketua'])) $involved = true;
        }
        if (!$involved) throw new Exception("Anda bukan Kepala Auditor");

        $open = false;
        foreach ($data->sis_jadwal_audits as $ja) {
            if ($ja->jadw_audit_status_komite == 'submited') $open = true;
        }

        if (!$open) throw new Exception("Proses audit belum diajukan ke Komite");

        return $data;
    }
}
