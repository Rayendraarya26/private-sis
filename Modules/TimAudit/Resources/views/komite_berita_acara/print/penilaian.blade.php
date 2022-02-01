<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LEMBAR PERIKSA KOMITE SERTIFIKASI</title>
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
        <img src="{{public_path('/images/logos/sis_ls_bbkkp.png')}}" alt="Logo" style="max-width: 120px;">
    </div>
    <div class="text-center">
        <span style="font-weight: bold; font-size: 16px;font-family: Arial, Helvetica, sans-serif;border-bottom:1px #000 solid;">LEMBAR PERIKSA KOMITE SERTIFIKASI</span><br/>
        <span style="font-weight: normal; font-size: 16px;font-family: Arial, Helvetica, sans-serif;">Nomor</span>
    </div>
</header>

<section>
	<table style="border:0px;">
		<tbody>
			<tr><td>Nama Perusahaan</td><td>: {{$dataJadwal->cust_nama}}</td></tr>
			<tr><td>Alamat</td><td>: {{$dataJadwal->cust_alamat}}</td></tr>
			<tr><td>No. Referensi</td><td>: {{$dataJadwal->jadw_audit_standart_acuan}}</td></tr>
			<tr><td>Acuan Standar</td><td>: {{$dataJadwal->jadw_audit_sni}}</td></tr>
			<tr><td>Jenis Produk</td><td>: {{  $dataJadwal->komodt_nama}}</td></tr>
			<tr><td>Type</td><td>: {{$dataJadwal->jadw_audit_tipe}}</td></tr>
			<tr><td>Merk</td><td>: {{$dataJadwal->jadw_audit_merk}}</td></tr>
		</tbody>
	</table>
	<table>
		<thead>
			<tr><th class="left" style="border:1px solid;">1. Penilaian</th></tr>
		</thead>
		<tbody>
			<tr><td class="left" style="border:1px solid;"><b>1.1. Persyaratan Administrasi dan prosedur sertifikasi</b><br/>@if(isset($dataJadwal->komte_priksa_penilaian_1)) {!! $dataJadwal->komte_priksa_penilaian_1 !!} @endif</td></tr>

			<tr><td class="left" style="border:1px solid;"><b>1.2. Konfirmasi Hasil Pengkajian Permohonan</b><br/>@if(isset($dataJadwal->komte_priksa_penilaian_2)) {!! $dataJadwal->komte_priksa_penilaian_2 !!} @endif</td></tr>

			<tr><td class="left" style="border:1px solid;"><b>1.3. Evaluasi waktu audit yang direncanakan dengan realisasi pelaksanaan</b><br/>@if(isset($dataJadwal->komte_priksa_penilaian_3)) {!! $dataJadwal->komte_priksa_penilaian_3 !!} @endif</td></tr>

			<tr><td class="left" style="border:1px solid;"><b>1.4. Evaluasi kedalamam Laporan Audit yang dibuat oleh Auditor</b><br/>@if(isset($dataJadwal->komte_priksa_penilaian_4)) {!! $dataJadwal->komte_priksa_penilaian_4 !!} @endif</td></tr>

			<tr><td class="left" style="border:1px solid;"><b>1.5. Komentar terhadap ketidaksesuaian, tindakan koreksi dan tindakan korektif</b><br/>@if(isset($dataJadwal->komte_priksa_penilaian_5)) {!! $dataJadwal->komte_priksa_penilaian_5 !!} @endif</td></tr>

			<tr><td class="left" style="border:1px solid;"><b>1.6. Hasil Inspeksi/ Asesmen Sistem Mutu/ Lingkungan*)</b><br/>@if(isset($dataJadwal->komte_priksa_penilaian_6)) {!! $dataJadwal->komte_priksa_penilaian_6 !!} @endif</td></tr>

			<tr><td class="left" style="border:1px solid;"><b>1.7. Konfirmasi terhadap ketercapaian tujuan audit</b><br/>@if(isset($dataJadwal->komte_priksa_penilaian_7)) {!! $dataJadwal->komte_priksa_penilaian_7 !!} @endif</td></tr>

			<tr><td class="left" style="border:1px solid;"><b>1.8. Rekaman Tahapan Sertifikasi</b><br/>@if(isset($dataJadwal->komte_priksa_penilaian_8)) {!! $dataJadwal->komte_priksa_penilaian_8 !!} @endif</td></tr>

			<tr><td class="left" style="border:1px solid;"><b>1.9. Hal-hal negative yang mempengaruhi penerbitan sertifikat</b><br/>@if(isset($dataJadwal->komte_priksa_penilaian_9)) {!! $dataJadwal->komte_priksa_penilaian_9 !!} @endif</td></tr>

			<tr><td class="left" style="border:1px solid;"><b>1.10. Hal-hal yang diperbaiki/ditambahkan</b><br/>@if(isset($dataJadwal->komte_priksa_penilaian_10)) {!! $dataJadwal->komte_priksa_penilaian_10 !!} @endif</td></tr>

			<tr><td class="left" style="border:1px solid;"><b>1.11. Hasil Perbaikan</b><br/>@if(isset($dataJadwal->komte_priksa_penilaian_11)) {!! $dataJadwal->komte_priksa_penilaian_11 !!} @endif</td></tr>

			<tr><td class="left" style="border:1px solid;"><b>1.12. Pelaksanaan Pengambilan contoh (khusus LS Produk)</b><br/>@if(isset($dataJadwal->komte_priksa_penilaian_12)) {!! $dataJadwal->komte_priksa_penilaian_12 !!} @endif</td></tr>

			<tr><td class="left" style="border:1px solid;"><b>1.13. Hasil Uji Laboratorium (khusus LS Produk)</b><br/>@if(isset($dataJadwal->komte_priksa_penilaian_13)) {!! $dataJadwal->komte_priksa_penilaian_13 !!} @endif</td></tr>
		</tbody>
	</table>
	<table  style="border-top:0px;">
		<thead>
			<tr><th class="left" style="border:1px solid;border-top:0px;border-bottom:0px;">2. Keputusan/Rekomendasi</th></tr>
		</thead>
		<tbody>
			<tr>
			<td class="left" style="border:1px solid;border-top:0px;">
			@foreach($dataAudit as $au)
				<b>- {!! $dataJadwal->cust_nama !!} {!! str_replace('-', ' ', $au->jadw_audit_status) !!} {!! $au->jadw_audit_sni !!}</b> @if($au->sert_is_product == 'ya') untuk produk {!! $au->komodt_nama !!} dengan merk {!! $au->jadw_audit_merk !!} dan tipe {!! $au->jadw_audit_tipe !!} @endif <br/>
			@endforeach
			</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<tbody>
			<tr>
				<td class="left" style="border:1px solid;border-right:0px;border-top:0px;">
                    Dibuat di : Yogyakarta<br/>
                    Pada tanggal : ......
                </td>
                <td class="left" style="border:1px solid;border-left:0px;border-top:0px;">
                    Komite Sertifikasi
                </td>
            </tr>
            <tr>
                <td class="left" style="border:1px solid;border-right:0px;"></td>
                <td class="left" style="border:1px solid;border-right:0px;border-left:0px;">
                    <table style="border:0px;">
                        <tbody>
                        <tr>
                            <td class="left">Nama</td>
                            <td class="left">Tanda Tangan</td>
                        </tr>
                        @foreach($dataTim as $tim)
                            <?php $i = 1;?>
                            <tr>
                                <td class="left"><?=$i;?>. {{$tim->peg_nama}}</td>
                                <td class="center">
                                    @if(!empty($tim->peg_ttd_base64))
                                        <img src="{{ $tim->peg_ttd_base64 }}" alt="ttd ketua" style="max-height: 50px;">
                                    @elseif(!empty($tim->peg_ttd_file))
                                        <img src="{{public_path($tim->peg_ttd_file)}}" alt="ttd ketua"
                                             style="max-height: 50px;">
                                    @endif
                                </td>
                            </tr>
                            <?php $i++;?>
                        @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <i>*) Coret yang tidak perlu</i>
</section>


<script type="text/php">
    if (isset($pdf)) {
        // FTM
        $pdf->page_script('
            $x = 60;
            $y = 810;
            $text = "F-KEP-3";
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
            $text = "Rev. 2";
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
