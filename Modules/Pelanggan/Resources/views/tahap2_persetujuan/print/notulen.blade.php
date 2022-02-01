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


        section, span, table, tr, th, td {
            font-size: 12px;
        }

        header {
            /*position: absolute;*/
            right: 0px;
            /*background-color: lightblue;*/
            height: 50px;
        }
    </style>
</head>
<body style="margin-top: 50px">
<header>
    <div style="float: left;margin-left: 60px;">
        <img src="{{public_path('/images/logos/sis_ls_bbkkp.png')}}" alt="Logo"
             style="max-width: 120px;">
    </div>
    <div class="text-center">
        <span style="font-weight: bold; font-size: 20px">NOTULEN RAPAT</span>
    </div>
</header>

<section style="padding-top: 20px; margin-left: 60px; margin-right: 40px">
    <table>
        <tr>
            <td>Hari, Tanggal</td>
            <td>: {{$dataJadwal->jadw_tanggal_selesai->isoFormat("LL")}}
            </td>
        </tr>
        <tr>
            <td>Tempat</td>
            <td>: {{$dataJadwal->sis_pelanggan->cust_nama}}
            </td>
        </tr>
        <tr>
            <td>Pimpinan</td>
            <td>: {{$dataKetua->master_pegawai->peg_nama}}</td>
        </tr>

        <tr>
            <td>Jumlah Peserta <br><i>(Rekaman Kehadiran Terlampir)</i></td>
            <td>:
        </tr>

        <tr>
            <td>Materi</td>
            <td>: Rapat penutupan

                @foreach($dataJadwal->sis_jadwal_audits as $audit)
                    @if($loop->last)
                        {{$audit->master_sertifikasi?->sert_nama}}
                    @else
                        {{$audit->master_sertifikasi?->sert_nama}},
                    @endif
                @endforeach
            </td>
        </tr>
    </table>

    <div style="padding-top: 20px">
        <hr>
        <h4>HASIL</h4>
        <p>
            {!! $dataJadwal->jadw_notulen_rapat !!}
        </p>
    </div>

    <div style="padding-top: 50px">
        <table>
            <tr>
                <td style="padding-left: 100px">
                    <table style="width: 200px">
                        <tbody>
                        <tr>
                            <td style="font-size: 11pt;" colspan="2">
                                <strong>Mengetahui:</strong>
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
                <td style="padding-left: 150px"></td>
                <td>
                    <table style="width: 200px">
                        <tbody>
                        <tr>
                            <td style="font-size: 11pt;" colspan="2">
                                <strong>Yogyakarta {{$dataJadwal->jadw_tanggal_selesai->isoFormat('LL')}}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                @if(!empty($dataKetua->master_pegawai->peg_ttd_base64))
                                    <img src="{{ $dataKetua->master_pegawai->peg_ttd_base64 }}" alt="ttd ketua"
                                         style="max-height: 100px;">
                                @elseif(!empty($dataKetua->master_pegawai->peg_ttd_file))
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
            </tr>
        </table>
    </div>
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
