<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HASIL rekap-lks DOKUMEN</title>
    <style>
        .text-center {
            text-align: center;
            justify-content: center;
        }

        .table-formatter {
            font-family: Arial, Helvetica, sans-serif;
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .table-formatter thead {
            vertical-align: middle !important;
        }

        .table-formatter th, .table-formatter td {
            padding: 5px !important;
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

        .information {
            margin-top: -10px;
        }
    </style>
</head>
<body style="margin-top: 50px">
<header>
    <div style="float: left; margin-left: 40px">
        <img src="{{public_path('/images/logos/sis_ls_bbkkp.png')}}" alt="Logo"
             style="max-width: 120px;">
    </div>
    <div class="text-center">
        <span style="font-weight: bold; font-size: 20px">LAPORAN RINGKAS</span><br>
        <span>(Lembar asli disimpan oleh LS BBKKP)</span>
    </div>
</header>

<section style="padding-top: 20px; margin-left: 40px; margin-right: 40px">
    <table class="table-formatter">
        <tbody>
        <tr>
            <td>
                <div>
                    <p>Nama Perusahaan: {{$dataJadwal->sis_pelanggan->cust_nama}}</p>
                    <p class="information">
                        No Ref:
                        @foreach($dataJadwal->sis_jadwal_audits as $audit)
                            @if($audit->jadw_audit_nomor_referensi != "")
                                {{$audit->jadw_audit_nomor_referensi . (!$loop->last ? ' ; ' : '.')}}
                            @endif
                        @endforeach
                    </p>
                    <p class="information">
                        Komoditas:
                        @foreach($dataJadwal->sis_jadwal_audits as $audit)
                            @if($audit->master_komoditi->komodt_nama != "")
                                {{$audit->master_komoditi->komodt_nama . (!$loop->last ? ' ; ' : '.')}}
                            @endif
                        @endforeach
                    </p>
                    <p class="information">
                        Alamat: {{$dataJadwal->sis_pelanggan->cust_alamat}}
                    </p>
                </div>


            </td>
            <td>
                <div>
                    <p>
                        Kegiatan:

                        @foreach($dataJadwal->sis_jadwal_audits as $audit)
                            {{$audit->jadw_audit_kegiatan . (!$loop->last ? ' - ' : '.')}}
                        @endforeach
                    </p>
                    <p class="information">
                        Tanggal Asesmen:

                        {{ $dataJadwal->jadw_tanggal_mulai->isoFormat("LL") }}
                        s/d {{ $dataJadwal->jadw_tanggal_selesai->isoFormat("LL") }}
                    </p>
                    <p class="information">
                        Standar Acuan:

                        @foreach($dataJadwal->sis_jadwal_audits as $audit)
                            @if($audit->jadw_audit_standart_acuan != "")
                                {{$audit->jadw_audit_standart_acuan . (!$loop->last ? ' ; ' : '.')}}
                            @endif
                        @endforeach
                    </p>
                    <p class="information">
                        Ketua TIM:
                        {{$dataKetua->master_pegawai->peg_nama}}
                    </p>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

    <table id="rekap-lks">
        <thead>
        <tr>
            <th>KATEGORI</th>
            <th>JUMLAH</th>
            <th>NOMOR LKS</th>
            <th>KLAUSUL</th>
            <th>TANGGAL PENYELESAIAN</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Kritis</td>
            <td>{{$dataLKS['jumlah']['kritis']}}</td>
            <td>{{$dataLKS['no_lks']['kritis'] ?: '-' }}</td>
            <td>{{$dataLKS['klausul']['kritis'] ?: '-'}}</td>
            <td>{{$dataLKS['tgl_pelyelesaian']['kritis'] ?: '-'}}</td>
        </tr>
        <tr>
            <td>Mayor</td>
            <td>{{$dataLKS['jumlah']['mayor']}}</td>
            <td>{{$dataLKS['no_lks']['mayor'] ?: '-'}}</td>
            <td>{{$dataLKS['klausul']['mayor'] ?: '-'}}</td>
            <td>{{$dataLKS['tgl_pelyelesaian']['mayor'] ?: '-'}}</td>
        </tr>
        <tr>
            <td>Minor</td>
            <td>{{$dataLKS['jumlah']['minor']}}</td>
            <td>{{$dataLKS['no_lks']['minor'] ?: '-'}}</td>
            <td>{{$dataLKS['klausul']['minor'] ?: '-'}}</td>
            <td>{{$dataLKS['tgl_pelyelesaian']['minor'] ?: '-'}}</td>
        </tr>
        <tr>
            <td>Total</td>
            <td>{{$dataLKS['jumlah']['total']}}</td>
            <td>{{$dataLKS['no_lks']['total'] ?: '-'}}</td>
            <td>{{$dataLKS['klausul']['total'] ?: '-'}}</td>
            <td>{{$dataLKS['tgl_pelyelesaian']['total'] ?: '-'}}</td>
        </tr>
        </tbody>
    </table>
    <table style="border-top:0px;" class="table-formatter">
        <thead>
        <tr>
            <th class="left">Ringkasan hasil (Kesimpulan)</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{!! $dataJadwal->sis_audit_lap_ringkas->lap_ringkas_kesimpulan !!}</td>
        </tr>
        </tbody>
    </table>

    @if(!empty($dataJadwal?->sis_audit_lap_ringkas->lap_ringkas_rekomendasi))
        <table style="border-top:0px;" class="table-formatter">
            <thead>
            <tr>
                <th class="left">Rekomendasi</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{!! $dataJadwal->sis_audit_lap_ringkas->lap_ringkas_rekomendasi !!}</td>
            </tr>
            </tbody>
        </table>
    @endif


    <table class="table-formatter" style="border-top:0px;">
        <tr>
            <td style="padding-left: 100px">
                <table style="width: 300px">
                    <tbody>
                    <tr>
                        <td style="font-size: 11pt;" colspan="2">
                            <strong>Diterbitkan oleh: LS BBKKP</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            @if(!empty($dataKetua->master_pegawai->peg_ttd_base64))
                                <img src="{{ $dataKetua->master_pegawai->peg_ttd_base64 }}" alt="ttd ketua"
                                     style="max-height: 100px;">
                            @else
                                <img src="{{public_path($dataKetua->master_pegawai->peg_ttd_file)}}" alt="ttd ketua"
                                     style="max-height: 100px;">
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 10%;">Nama</td>
                            <td>
                                : {{$dataKetua->master_pegawai->peg_nama}}
                            </td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>
                            : Ketua Tim
                        </td>
                    </tr>
                    </tbody>
                </table>

            </td>
            <td style="padding-left: 150px"></td>
            <td>
                <table style="width: 300px">
                    <tbody>
                    <tr>
                        <td style="font-size: 11pt;" colspan="2">
                            <strong>Diketahui oleh:</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="height: 100px"></td>
                    </tr>
                    <tr>
                        <td style="width: 10%;">Nama</td>
                        <td>
                            : {{$dataJadwal->jadw_setujui_nama}}
                            </td>
                        </tr>
                        <tr>
                            <td>Jabatan</td>
                            <td>
                                : {{$dataJadwal->jadw_setujui_jabatan}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

</section>


<script type="text/php">
    if (isset($pdf)) {
        // FTM
        $pdf->page_script('
            $x = 60;
            $y = 790;
            $text = "F-TA-11";
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
            $y = 790;
            $text = "Rev. 1";
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
