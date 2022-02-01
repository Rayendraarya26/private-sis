<?php

namespace Modules\TimAudit\Http\Traits;

use App\Models\BbkkpSis\SisJadwal;

trait LksTrait
{
    public function calculateTemuanLKS(SisJadwal $dataJadwal)
    {
        $dataLKS = [
            'jumlah'           => ['kritis' => 0, 'mayor' => 0, 'minor' => 0, 'total' => 0],
            'no_lks'           => ['kritis' => '', 'mayor' => '', 'minor' => '', 'total' => ''],
            'klausul'          => ['kritis' => '', 'mayor' => '', 'minor' => '', 'total' => ''],
            'tgl_pelyelesaian' => ['kritis' => null, 'mayor' => null, 'minor' => null, 'total' => null]
        ];

        foreach ($dataJadwal->sis_audit_lks as $lks) {
            switch ($lks->lks_kategori_ketidaksesuaian) {
                case 'kritis':
                    // jumlah
                    $dataLKS['jumlah']['kritis'] += 1;
                    $dataLKS['jumlah']['total']  += 1;
                    // klausul
                    $dataLKS['klausul']['kritis'] .= strip_tags($lks->lks_klausul_ketidaksesuaian . '; ');
                    // no lks
                    $dataLKS['no_lks']['kritis'] .= $lks->lks_id . '; ';
                    // tgl penyelesaian
                    if (!empty($lks->lks_expired_date_perbaikan)) {
                        if ($dataLKS['tgl_pelyelesaian']['kritis'] == null) {
                            $dataLKS['tgl_pelyelesaian']['kritis'] = $lks->lks_expired_date_perbaikan->isoFormat("LL");
                        } else {
                            if ($lks->lks_expired_date_perbaikan->isAfter($dataLKS['tgl_pelyelesaian']['kritis'])) {
                                $dataLKS['tgl_pelyelesaian']['kritis'] = $lks->lks_expired_date_perbaikan->isoFormat("LL");
                            }
                        }
                    }
                    break;
                case 'mayor':
                    // jumlah
                    $dataLKS['jumlah']['mayor'] += 1;
                    $dataLKS['jumlah']['total'] += 1;
                    // klausul
                    $dataLKS['klausul']['mayor'] .= strip_tags($lks->lks_klausul_ketidaksesuaian . '; ');
                    // no lks
                    $dataLKS['no_lks']['mayor'] .= $lks->lks_id . '; ';
                    // tgl penyelesaian
                    if (!empty($lks->lks_expired_date_perbaikan)) {
                        if ($dataLKS['tgl_pelyelesaian']['mayor'] == null) {
                            $dataLKS['tgl_pelyelesaian']['mayor'] = $lks->lks_expired_date_perbaikan->isoFormat("LL");
                        } else {
                            if ($lks->lks_expired_date_perbaikan->isAfter($dataLKS['tgl_pelyelesaian']['mayor'])) {
                                $dataLKS['tgl_pelyelesaian']['mayor'] = $lks->lks_expired_date_perbaikan->isoFormat("LL");
                            }
                        }
                    }
                    break;
                case 'minor':
                case 'observasi':
                    // jumlah
                    $dataLKS['jumlah']['minor'] += 1;
                    $dataLKS['jumlah']['total'] += 1;
                    // klausul
                    $dataLKS['klausul']['minor'] .= strip_tags($lks->lks_klausul_ketidaksesuaian . '; ');
                    // no lks
                    $dataLKS['no_lks']['minor'] .= $lks->lks_id . '; ';
                    // tgl penyelesaian
                    if (!empty($lks->lks_expired_date_perbaikan)) {
                        if ($dataLKS['tgl_pelyelesaian']['minor'] == null) {
                            $dataLKS['tgl_pelyelesaian']['minor'] = $lks->lks_expired_date_perbaikan->isoFormat("LL");
                        } else {
                            if ($lks->lks_expired_date_perbaikan->isAfter($dataLKS['tgl_pelyelesaian']['minor'])) {
                                $dataLKS['tgl_pelyelesaian']['minor'] = $lks->lks_expired_date_perbaikan->isoFormat("LL");
                            }
                        }
                    }
                    break;

            }
        }

        return $dataLKS;
    }

    public function syncNomorLKS(int $jadwalID)
    {
        $data = SisJadwal::with('sis_audit_lks')->find($jadwalID);
        if (!empty($data)) {
            foreach ($data->sis_audit_lks as $idx => $lks) {
                $lks->lks_nomor = $idx + 1;
                $lks->save();
            }
        }
    }
}
