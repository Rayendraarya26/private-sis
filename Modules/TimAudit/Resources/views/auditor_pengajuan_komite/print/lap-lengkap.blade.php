<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Lengkap</title>
    <style>
        .text-center {
            text-align: center;
            justify-content: center;
        }


        section, span, table, tr, th, td, #rekap-lks {
            font-size: 12px;
        }

        #rekap-lks {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #rekap-lks td, #rekap-lks th {
            border: 1px solid black;
        }

        #rekap-lks tr:hover {
            background-color: #ddd;
        }

        #rekap-lks th {
            padding-top: 5px;
            padding-bottom: 5px;
            background-color: #FBD4B4;
            color: black;
        }

        #rekap-lks td {
            padding: 0 10px 0 10px
        }

        header {
            /*position: absolute;*/
            right: 0px;
            /*background-color: lightblue;*/
            height: 50px;
        }
		
		table{
            font-family: Arial, Helvetica, sans-serif;
			width:100%;
			border-collapse:collapse; 
			border:0.5px solid #000;
		}
		thead{
			vertical-align:middle !important;
			// background:#ECF0F5;
		}
		th, td{
			padding:5px !important;
		}
		.center{
			text-align:center;
		}
		.right{
			text-align:right; padding:10px;
		}
		.left{
			text-align:left; padding:10px;
		}
    </style>
</head>
<body style="margin-top: 50px">
<header>
    <div style="float: left">
        <img src="{{public_path('/images/logos/sis_ls_bbkkp.png')}}" alt="Logo"
             style="max-width: 120px;">
    </div>
    <div class="text-center">
        <span style="font-weight: bold; font-size: 16px;font-family: Arial, Helvetica, sans-serif;">LAPORAN LENGKAP HASIL AUDIT</span>
    </div>
</header>

<section>
	<table>
		<thead>
			<tr>
				<th class="left" colspan="2">I. Pengesahan</th>
			</tr>
		</thead>
		<tbody>
			<tr><td>Dibuat Oleh : Ketua Tim Auditor</td><td>Disetujui Oleh : {!! $dataJadwal->lap_lengkp_verifikasi_jabatan !!}</td></tr>
			<tr>
				<td>@if(!empty($dataKetua->master_pegawai->peg_ttd_base64))
						<img src="{{ $dataKetua->master_pegawai->peg_ttd_base64 }}" alt="ttd ketua"
							 style="max-height: 100px;">
					@else
						<img src="{{public_path($dataKetua->master_pegawai->peg_ttd_file)}}" alt="ttd ketua"
							 style="max-height: 100px;">
					@endif</td>
				<td></td>
			</tr>
			<tr><td>Nama : {{$dataKetua->master_pegawai->peg_nama}}</td><td>Nama : {!! $dataJadwal->lap_lengkp_verifikasi_oleh !!}</td></tr>
			<tr><td>Tanggal : <?=date('d M Y', strtotime($dataJadwal->jadw_tanggal_mulai))?> s/d <?=date('d M Y', strtotime($dataJadwal->jadw_tanggal_selesai))?></td><td>Tanggal : <?=date('d M Y', strtotime($dataJadwal->jadw_tanggal_mulai))?> s/d <?=date('d M Y', strtotime($dataJadwal->jadw_tanggal_selesai))?></td></tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" colspan="2">II. Umum</th>
			</tr>
		</thead>
		<tbody>
			<tr><td>Tahap Kegiatan : </td><td>{!! $dataJadwal->jadw_audit_kegiatan !!}</td></tr>
			<tr><td>Tanggal Pelaksanaan : </td><td><?=date('d M Y', strtotime($dataJadwal->jadw_tanggal_mulai))?> s/d <?=date('d M Y', strtotime($dataJadwal->jadw_tanggal_selesai))?></td></tr>
			<tr><td>Nama Perusahaan: </td><td>{{$dataJadwal->cust_nama}}</td></tr>
			<tr><td>No. Referensi : </td><td>{{$dataJadwal->jadw_audit_nomor_referensi}}</td></tr>
			<tr><td>Jumlah Karyawan : </td><td>{{$dataJadwal->cust_jumlah_operasional}}</td></tr>
			<tr><td>Ruang Lingkup : </td><td>{!! $dataJadwal->jadw_audit_ruang_lingkup !!}</td></tr>
			<tr><td>Komoditas : </td><td>{!!  $dataJadwal->komodt_nama !!}</td></tr>
			<tr><td>Audit : </td><td>{!! $dataJadwal->sert_nama !!}</td></tr>
			<tr><td>Personel Penghubung : </td><td>{{$dataJadwal->cust_nama_pemilik}}</td></tr>
			<tr><td>Alamat, Telp, Fax : </td><td>{{$dataJadwal->cust_alamat}}; Telp {{$dataJadwal->cust_nomor_telp}} ; Fax {{$dataJadwal->cust_nomor_fax}}</td></tr>
			<tr><td>Tujuan Audit : </td><td>{!! $dataJadwal->jadw_audit_tujuan_audit !!}</td></tr>
			<tr><td>Jenis Audit : </td><td>Audit {{$dataJadwal->jadw_jenis}}</td></tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" colspan="2">III. Susunan Tim Audit</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Ketua :</td>
				<td>{!! $dataJadwal->ketua !!}</td>
			</tr>
			<tr>
				<td>Anggota :</td>
				<td>{!! $dataJadwal->anggota !!}</td>
			</tr>
			<tr>
				<td>Supervisor :</td>
				<td>-</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" colspan="4">IV. Jumlah Temuan LKS</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Kritis : {{$dataLKS['jumlah']['kritis']}}</td>
				<td></td>
				<td>Total : {{$dataLKS['jumlah']['total']}}</td>
				<td></td>
			</tr>
			<tr>
				<td>Mayor : {{$dataLKS['jumlah']['mayor']}}</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>Minor : {{$dataLKS['jumlah']['minor']}}</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >V. Penilaian secara umum penerapan SMM/SML/SPPT SNI</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_lengkp_penilaian !!}</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >VI. Penyimpangan dari Program Audit dan Alasannya</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_lengkp_penyimpangan !!}</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >VII. Isu (masalah) Signifikan yang Berdampak Terhadap Program Audit</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_lengkp_isu_berdampak !!}</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >VIII. Isu-isu (permasalahan) yang Tidak Terselesaikan (jika teridentifikasi)</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_lengkp_isu_tidak_terselesaikan !!}</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >IX. Perubahan Signifikan (jika ada) yang Mempengaruhi Sistem Manajemen Perusahaan</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_lengkp_perubahan !!}</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >X. Kekuatan</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_lengkp_kekuatan !!}</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >XI. Kelemahan</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_lengkp_kelemahan !!}</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >XII. Tinjauan terhadap Keluhan Pelanggan</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_lengkp_tinjauan_keluhan !!}</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >XIII. Pengendalian Penggunaan Tanda Sertifikat Lembaga dan atau Tanda SNI</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_lengkp_pengendalian_penggunaan !!}</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >XIV. Kedalaman Audit Internal dan Tinjauan Manajemen. Verifikasi TK /P audit sebelumnya (bila ada)</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_lengkp_kedalaman_audit !!}</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >XV. Pernyataan kesesuaian dan efektifitas pelaksanaan sistem manajemen</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_lengkp_pernyataan_kesesuaian !!}</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >XVI. Kesimpulan ketaatan terhadap lingkup sertifikasi</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_lengkp_kesimpulan_ketaatan !!}</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >XVII. Konfirmasi bahwa tujuan audit telah terpenuhi</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_lengkp_konfirmasi_tujuan !!}</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >XVIII. Saran untuk Tim berikutnya</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_lengkp_saran !!}</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >XIX. Kesimpulan</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_lengkp_kesimpulan !!}</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" colspan="4">XX. Rincian Ketidaksesuaian</th>
			</tr>
			<tr>
				<th class="left" style="border :0.5px solid #000;border-collapse: collapse; ">No. LKS</th>
				<th class="left" style="border :0.5px solid #000;border-collapse: collapse; ">Rincian Ketidak Sesuaian</th>
				<th class="left" style="border :0.5px solid #000;border-collapse: collapse; ">Kategori</th>
				<th class="left" style="border :0.5px solid #000;border-collapse: collapse; ">Keterangan</th>
			</tr>
		</thead>
		<tbody>
			@foreach($itemLKS as $lks)
			<tr>
				<td style="border :0.5px solid #000;border-collapse: collapse; ">{{$lks->lks_nomor}}</td>
				<td style="border :0.5px solid #000;border-collapse: collapse; ">{!! $lks->lks_uraian_ketidaksesuaian !!}<br/>{!! $lks->lks_klausul_ketidaksesuaian !!}</td>
				<td style="border :0.5px solid #000;border-collapse: collapse; ">{!! $lks->lks_kategori_ketidaksesuaian !!}</td>
				<td style="border :0.5px solid #000;border-collapse: collapse; ">Target Penyelesaian <?=date('d M Y', strtotime($lks->lks_expired_date_perbaikan))?></td>
			</tr>
			@endforeach
		</tbody>
	</table>
</section>


<script type="text/php">
    if (isset($pdf)) {
        // FTM
        $pdf->page_script('
            $x = 60;
            $y = 810;
            $text = "F-TA-13";
            $font = $fontMetrics->get_font("helvetica", "italic");
            $size = 10;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        ');

        // FTM
        $pdf->page_script('
            $x = 160;
            $y = 810;
            $text = "Rev. 4";
            $font = $fontMetrics->get_font("helvetica");
            $size = 10;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        ');

        // Halaman (ID)
        $pdf->page_script('
            $x = 750;
            $y = 570;
            $text = "{$PAGE_NUM} dari {$PAGE_COUNT}";
            $font = $fontMetrics->get_font("helvetica", "italic");
            $size = 10;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        ');
    }

</script>
</body>
</html>
