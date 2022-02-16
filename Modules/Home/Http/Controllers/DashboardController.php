<?php

namespace Modules\Home\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\BbkkpSis\MasterJenisPerusahaan;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SisPermohonan;
use App\Models\BbkkpSis\SisPelangganSertifikasi;
use App\Models\BbkkpSis\SisBillingItems;
use App\Models\BbkkpSis\SisJadwalTim;
use App\Models\BbkkpSis\SisJadwalAudit;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        switch ((int) session('group_selected'))
        {
            case 3: // pelanggan
                $data = [
                    'certifications_process' => SisPermohonan::where([
                        'user_id' => auth()->id(),
                        'mohon_approved_status' => 'on-progress'
                    ]),
                    'certifications_approved' => SisPermohonan::where([
                        'user_id' => auth()->id(),
                        'mohon_approved_status' => 'accepted'
                    ]),
                    'certifications_rejected' => SisPermohonan::where([
                        'user_id' => auth()->id(),
                        'mohon_approved_status' => 'rejected'
                    ]),
                    'certifications_revision' => SisPermohonan::where([
                        'user_id' => auth()->id(),
                        'mohon_approved_status' => 'revisi'
                    ]),
                    'certifications_fix' => SisPermohonan::where([
                        'user_id' => auth()->id(),
                        'mohon_approved_status' => 'fix'
                    ]),
                    'certifications' => SisPermohonan::where('user_id', auth()->id())
                ];
                return view('home::dashboard.pelanggan')->with($data);
            break;
            case 4: // marketing
                $company_types = MasterJenisPerusahaan::withCount(['sis_pelanggans'])->get();

                $data = [
                    'total_pelanggan' => SisPelanggan::count(),
                    'company_types' => $company_types
                ];

                return view('home::dashboard.marketing')->with($data);
            break;
            case 5: // pegawai/auditor
                return view('home::dashboard.pegawai_auditor');
            break;
            case 6: // operator ls
                $company_types = MasterJenisPerusahaan::withCount(['sis_pelanggans'])->get();

                $data = [
                    'total_pelanggan' => SisPelanggan::count(),
                    'total_sertifikat' => SisPelangganSertifikasi::count(),
                    'total_sertifikat_active' => SisPelangganSertifikasi::whereIn('cust_sert_status', ['on_going'])->count(),
                    'total_sertifikat_expired' => SisPelangganSertifikasi::whereIn('cust_sert_status', ['expired'])->count(),
                    'total_sertifikat_banned' => SisPelangganSertifikasi::whereIn('cust_sert_status', ['dibekukan'])->count(),
                    'company_types' => $company_types
                ];

                return view('home::dashboard.operator_ls')->with($data);
            break;
            case 7: // keuangan
                return view('home::dashboard.keuangan');
            break;
            case 8: // kerjasama
                return view('home::dashboard.kerjasama');
            break;
            case 9: // PJT
                $company_types = MasterJenisPerusahaan::withCount(['sis_pelanggans'])->get();

                $data = [
                    'total_pelanggan' => SisPelanggan::count(),
                    'total_sertifikat' => SisPelangganSertifikasi::count(),
                    'total_sertifikat_active' => SisPelangganSertifikasi::whereIn('cust_sert_status', ['on_going'])->count(),
                    'total_sertifikat_expired' => SisPelangganSertifikasi::whereIn('cust_sert_status', ['expired'])->count(),
                    'total_sertifikat_banned' => SisPelangganSertifikasi::whereIn('cust_sert_status', ['dibekukan'])->count(),
                    'company_types' => $company_types
                ];

                return view('home::dashboard.pjt')->with($data);
            break;
            case 10: // paskal
                $company_types = MasterJenisPerusahaan::withCount(['sis_pelanggans'])->get();

                $data = [
                    'total_pelanggan' => SisPelanggan::count(),
                    'total_sertifikat' => SisPelangganSertifikasi::count(),
                    'total_sertifikat_active' => SisPelangganSertifikasi::whereIn('cust_sert_status', ['on_going'])->count(),
                    'total_sertifikat_expired' => SisPelangganSertifikasi::whereIn('cust_sert_status', ['expired'])->count(),
                    'total_sertifikat_banned' => SisPelangganSertifikasi::whereIn('cust_sert_status', ['dibekukan'])->count(),
                    'company_types' => $company_types
                ];

                return view('home::dashboard.paskal')->with($data);
            break;
            case 1: // root
            case 2: // admin
            	$company_types = MasterJenisPerusahaan::withCount(['sis_pelanggans'])->get();

               	$data = [
                	'total_pelanggan' => SisPelanggan::count(),
                	'total_sertifikat' => SisPelangganSertifikasi::count(),
                	'total_sertifikat_active' => SisPelangganSertifikasi::whereIn('cust_sert_status', ['on_going'])->count(),
                	'total_sertifikat_expired' => SisPelangganSertifikasi::whereIn('cust_sert_status', ['expired'])->count(),
                	'total_sertifikat_banned' => SisPelangganSertifikasi::whereIn('cust_sert_status', ['dibekukan'])->count(),
                	'company_types' => $company_types
                ];

                return view('home::dashboard.index')->with($data);
            break;
        }
    }

    public function ajax(Request $request)
    {
    	$year = $request->get('year') ?? null;

    	switch ($request->get('type'))
    	{
    		case 'summary-pnbp':
    			$data = SisBillingItems::join('sis_billing', 'sis_billing.bill_id', '=', 'sis_billing_items.bill_id')
    			->selectRaw('bill_payment_status AS status, SUM(itms_bil_total) as total')
    			->groupBy('bill_payment_status');

    			if ($year) $data = $data->whereYear('sis_billing.created_at', '=', $year);
    			$data = $data->get();

    			$results = [];
    			$total = 0;
    			foreach ($data as &$row)
    			{
    				$total += $row->total;
    				switch (strtolower($row->status))
    				{
    					case 'menunggu pembayaran':
    						$row->status = 'Menunggu pembayaran';
    						$row->color = '#f5222d';
    					break;
    					case 'menunggu konfirmasi':
    						$row->status = 'Menunggu konfirmasi';
    						$row->color = '#faad14';
    					break;
    					case 'lunas':
    						$row->status = 'Lunas';
    						$row->color = '#20c997';
    					break;
    					default:
    						# code...
    					break;
    				}

    				array_push($results, $row);
    			}

        		return response()->json([
        			'results' => $results,
        			'total' => $total
        		]);
    		break;
    		case 'grafik-pnbp':
    			$data = SisBillingItems::join('sis_billing', 'sis_billing.bill_id', '=', 'sis_billing_items.bill_id')
    			->selectRaw("MONTH(sis_billing.created_at) AS month_num, bill_payment_status AS status, SUM(itms_bil_total) as total")
    			->groupBy(DB::raw('MONTH(sis_billing.created_at)'), 'bill_payment_status')
    			->orderBy('month_num', 'ASC');

    			if ($year) $data = $data->whereYear('sis_billing.created_at', '=', $year);
    			$data = $data->get()->toArray();

    			$labels = [];
    			$datasets = [
    				[
    					'label' => 'Menunggu pembayaran',
    					'data' => [],
    					'backgroundColor' => '#f5222d'
    				],
    				[
    					'label' => 'Menunggu konfirmasi',
    					'data' => [],
    					'backgroundColor' => '#faad14'
    				],
    				[
    					'label' => 'Lunas',
    					'data' => [],
    					'backgroundColor' => '#20c997'
    				],
    			];

    			foreach (range(1,12) as $month_num)
    			{
    				array_push($labels, date('M', mktime(0,0,0, $month_num, 10)));
    				foreach ($data as $key => $row)
					{
						if ($row['month_num'] == $month_num)
						{
							switch (strtolower($row['status']))
							{
								case 'menunggu pembayaran':
									$datasets[0]['data'][$month_num - 1] = $row['total'] ?? 0;
								break;
								case 'menunggu konfirmasi':
									$datasets[1]['data'][$month_num - 1] = $row['total'] ?? 0;
								break;
								case 'lunas':
									$datasets[2]['data'][$month_num - 1] = $row['total'] ?? 0;
								break;
								default:

								break;
							}
						} else {
							if (!array_key_exists($month_num - 1, $datasets[0]['data'])) $datasets[0]['data'][$month_num - 1] = 0;
							if (!array_key_exists($month_num - 1, $datasets[1]['data'])) $datasets[1]['data'][$month_num - 1] = 0;
							if (!array_key_exists($month_num - 1, $datasets[2]['data'])) $datasets[2]['data'][$month_num - 1] = 0;
						}
					}
    			}

        		return response()->json([
        			'labels' => $labels,
        			'datasets' => $datasets
        		]);
    		break;
    		case 'summary-permohonan':
    			$data = SisPermohonan::selectRaw('mohon_approved_status AS status, COUNT(mohon_id) as total')
    			->groupBy('mohon_approved_status');

    			if ($year) $data = $data->whereYear('created_at', '=', $year);
    			$data = $data->get();

    			$results = [];
    			$total = 0;
    			foreach ($data as &$row)
    			{
    				$total += $row->total;
    				switch (strtolower($row->status))
    				{
						case 'fix':
							$row->status = 'Fix';
							$row->color = '#1976d2';
						break;
						case 'accepted':
							$row->status = 'Diterima';
							$row->color = '#20c997';
						break;
						case 'on-progress':
							$row->status = 'Proses';
							$row->color = '#faad14';
						break;
						case 'rejected':
							$row->status = 'Ditolak';
							$row->color = '#f5222d';
						break;
						case 'revisi':
							$row->status = 'Revisi';
							$row->color = '#e83e8c';
						break;
    					default:
    						# code...
    					break;
    				}

    				array_push($results, $row);
    			}

        		return response()->json([
        			'results' => $results,
        			'total' => $total
        		]);
    		break;
    		case 'grafik-permohonan':
    			$data = SisPermohonan::selectRaw('mohon_approved_status, MONTH(created_at) as month_num, COUNT(mohon_id) as total')
    			->groupBy(DB::raw('MONTH(created_at)'), 'mohon_approved_status');

    			if ($year) $data = $data->whereYear('created_at', '=', $year);
    			$data = $data->get()->toArray();

    			$labels = [];
    			$datasets = [
    				[
    					'label' => 'Fix',
    					'data' => [],
    					'backgroundColor' => '#1976d2'
    				],
    				[
    					'label' => 'Diterima',
    					'data' => [],
    					'backgroundColor' => '#20c997'
    				],
    				[
    					'label' => 'Proses',
    					'data' => [],
    					'backgroundColor' => '#faad14'
    				],
    				[
    					'label' => 'Ditolak',
    					'data' => [],
    					'backgroundColor' => '#f5222d'
    				],
    				[
    					'label' => 'Revisi',
    					'data' => [],
    					'backgroundColor' => '#e83e8c'
    				],
    			];

    			foreach (range(1,12) as $month_num)
    			{
    				array_push($labels, date('M', mktime(0,0,0, $month_num, 10)));

    				foreach ($data as $key => $row)
					{
	    				if ($row['month_num'] == $month_num)
						{
							switch (strtolower($row['mohon_approved_status']))
							{
								case 'fix':
									$datasets[0]['data'][$month_num - 1] = $row['total'] ?? 0;
								break;
								case 'accepted':
									$datasets[1]['data'][$month_num - 1] = $row['total'] ?? 0;
								break;
								case 'on-progress':
									$datasets[2]['data'][$month_num - 1] = $row['total'] ?? 0;
								break;
								case 'rejected':
									$datasets[3]['data'][$month_num - 1] = $row['total'] ?? 0;
								break;
								case 'revisi':
									$datasets[4]['data'][$month_num - 1] = $row['total'] ?? 0;
								break;
								default:

								break;
							}
						} else {
							if (!array_key_exists($month_num - 1, $datasets[0]['data'])) $datasets[0]['data'][$month_num - 1] = 0;
							if (!array_key_exists($month_num - 1, $datasets[1]['data'])) $datasets[1]['data'][$month_num - 1] = 0;
							if (!array_key_exists($month_num - 1, $datasets[2]['data'])) $datasets[2]['data'][$month_num - 1] = 0;
							if (!array_key_exists($month_num - 1, $datasets[3]['data'])) $datasets[3]['data'][$month_num - 1] = 0;
							if (!array_key_exists($month_num - 1, $datasets[4]['data'])) $datasets[4]['data'][$month_num - 1] = 0;
						}
					}
    			}

        		return response()->json([
        			'labels' => $labels,
        			'datasets' => $datasets
        		]);
    		break;
            case 'pie-sertifikat':
                $data = SisPelangganSertifikasi::selectRaw('sert_id, COUNT(cust_sert_id) as total')
                ->groupBy('sert_id');

                if ($year) $data = $data->whereYear('created_at', '=', $year);

                $results = [];
                $total = 0;
                foreach ($data->get() as &$row)
                {
                    $total += (int) $row['total'];
                    $row['color'] = "#".$this->random_color_part().$this->random_color_part().$this->random_color_part();
                    $row['sert_nama'] = $row->master_sertifikasi->sert_nama;
                    array_push($results, $row);
                }

                return response()->json([
                    'results' => $results,
                    'total' => $total
                ]);
            break;
            case 'performance-auditor':
                $performance_auditor = SisJadwalTim::from('sis_jadwal_tim AS s')
                ->selectRaw("*, (
                    SELECT COUNT(jadw_tim_id) 
                    FROM sis_jadwal_tim AS sja 
                    JOIN sis_jadwal sj2 ON sj2.jadw_id = sja.jadw_id
                    WHERE s.peg_id = sja.peg_id 
                    AND jadw_tim_posisi = 'ketua'
                    AND YEAR(sj2.jadw_tanggal_mulai) = $year
                ) AS total_ketua,
                (
                    SELECT COUNT(jadw_tim_id)
                    FROM sis_jadwal_tim AS sja
                    JOIN sis_jadwal sj2 ON sj2.jadw_id = sja.jadw_id
                    WHERE s.peg_id = sja.peg_id
                    AND jadw_tim_posisi = 'auditor'
                    AND YEAR(sj2.jadw_tanggal_mulai) = $year
                ) AS total_auditor")
                ->join('sis_jadwal AS sj', 's.jadw_id', '=', 'sj.jadw_id')
                ->whereYear('sj.jadw_tanggal_mulai', '=', $year)
                ->whereNotIn('s.jadw_tim_posisi', ['ppc'])
                ->groupBy('peg_id');

                $results = [];

                foreach ($performance_auditor->get() as $key => $row)
                {
                    $row->master_pegawai;
                    $row->master_pegawai->peg_nama = trim(preg_replace('/\s\s+/', ' ', $row->master_pegawai->peg_nama));
                    array_push($results, $row);
                }

                return response()->json([
                    'results' => $results
                ]);
            break;
            case 'performance-detail':
                $results = [];

                $peg_id = $request->get('peg');
                $position = $request->get('pos');

                if ($peg_id && $position)
                {
                    $performance_auditor = SisJadwalTim::where([
                        'jadw_tim_posisi' => $position,
                        'peg_id' => $peg_id
                    ]);

                    foreach ($performance_auditor->get() as $key => $row)
                    {
                        $row->sis_jadwal;
                        $row->sis_jadwal?->sis_jadwal_audits;

                        foreach ($row?->sis_jadwal?->sis_jadwal_audits ?? [] as $k => $aud)
                        {
                            $aud?->sis_permohonan?->sis_pelanggan;
                        }

                        array_push($results, $row);
                    }

                    return response()->json([
                        'results' => $results
                    ]);
                }
            break;
            case 'plan-audit':
                $results = [];

                if ($year)
                {
                    $audits = SisJadwalAudit::join('sis_jadwal', 'sis_jadwal_audit.jadw_id', '=', 'sis_jadwal.jadw_id')
                    ->whereYear('jadw_tanggal_mulai', '=', $year);

                    foreach ($audits->get() as $row)
                    {
                        $row->master_sertifikasi;
                        $row?->sis_permohonan?->sis_pelanggan;
                        foreach ($row?->sis_jadwal?->sis_jadwal_tims ?? [] as $r)
                        {
                            $r?->master_pegawai;
                        }
                        array_push($results, $row);
                    }

                    return response()->json([
                        'results' => $results
                    ]);
                }
            break;
    		default:
    			# code...
    		break;
    	}
    }

    function random_color_part() {
        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
    }
}
