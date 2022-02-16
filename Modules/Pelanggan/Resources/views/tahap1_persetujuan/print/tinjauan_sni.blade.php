<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HASIL TINJAUAN DOKUMEN</title>
    <style>
        .text-center {
            text-align: center;
            justify-content: center;
        }

        #tinjauan {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #tinjauan td, #tinjauan th {
            border: 1px solid black;
        }

        #tinjauan tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #tinjauan tr:hover {
            background-color: #ddd;
        }

        #tinjauan th {
            padding-top: 5px;
            padding-bottom: 5px;
            background-color: #FBD4B4;
            color: black;
        }

        section, span, table, tr, th, td, #tinjauan {
            font-size: 11px;
        }

        .headers {
            display: flex;
        }

        .headers_one {
            flex: 1 1 auto;
        }

        .headers_two {
            flex: 1 1 auto;
        }

    </style>
</head>
<body>

<div class="headers">
    <div class="headers_one">
        <img src="{{public_path('/images/logos/sis_ls_bbkkp.png')}}" alt="Logo" style="max-width: 120px; margin-top: -15px">
    </div>
    <div class="headers_two">
        <div class="text-center">
            <div style="font-weight: bold">
                HASIL TINJAUAN DOKUMEN SNI ISO 9001:2015
                <br>
                {{strtoupper($data->sis_permohonan->mohon_cust_nama)}}
                <br>
                @php
                    $komoditi = "";
                    foreach($data->sis_permohonan_detail->sis_permohonan_komoditis as $idx => $kom){
                        if ($idx == count($data->sis_permohonan_detail->sis_permohonan_komoditis) - 1){
                            $komoditi .= strtoupper($kom->master_komoditi->komodt_nama);
                        }else{
                            $komoditi .= strtoupper($kom->master_komoditi->komodt_nama . ', ');
                        }
                    }
                @endphp
            </div>
            Komoditi: {{$komoditi}}
        </div>
    </div>
</div>


<section>
    <table id="tinjauan">
        <thead>
        <tr>
            <th rowspan="2">Klausul</th>
            <th rowspan="2">Persyaratan</th>
            <th colspan="2">
                Dokumen {{strtoupper($data->sis_permohonan->mohon_cust_nama)}}
            </th>

            <th rowspan="2">Hasil Tinjauan <br>(OK / NO)</th>
            <th rowspan="2">Keterangan</th>
        </tr>
        <tr>
            <th>Kode Dokumen</th>
            <th>Judul Dokumen</th>
        </tr>
        </thead>

        <tbody>
        @foreach($data->sis_audit_tahap1_details as $detail)
            <tr>
                <td style="padding-left: 10px">{{$detail->aud_thp1_det_thp1_nomor}}</td>
                <td>{{$detail->aud_thp1_det_peryataan}}</td>
                <td>{{$detail->aud_thp1_det_kode_dok}}</td>
                <td>{{$detail->aud_thp1_det_judul_dok}}</td>
                <td class="text-center">{{ucwords($detail->aud_thp1_det_hasil_tinjauan)}}</td>
                <td>{{$detail->aud_thp1_det_keterangan}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <br>
    <span style="font-size: 10px">Kesimpulan: Dokumen yang disusun oleh {{strtoupper($data->sis_permohonan->mohon_cust_nama)}} <strong>{{$data->aud_thp1_status}}</strong> kecukupan minimal terhadap Standar {!! $data->aud_thp1_standart_acuan !!}.</span>
    <br>
    <span style="font-size: 10px">Rekomendasi: {!! $data->aud_thp1_pernyataan_auditor !!}</span>
</section>
<script type="text/php">
    if (isset($pdf)) {
        // FTM
        $pdf->page_script('
            $x = 60;
            $y = 560;
            $text = "F-TM-1";
            $font = $fontMetrics->get_font("helvetica", "italic");
            $size = 7;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        ');

        // FTM
        $pdf->page_script('
            $x = 400;
            $y = 560;
            $text = "Rev. 00";
            $font = $fontMetrics->get_font("helvetica");
            $size = 7;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        ');

        // Halaman (ID)
        $pdf->page_script('
            $x = 700;
            $y = 570;
            $text = "Halaman: {$PAGE_NUM} dari {$PAGE_COUNT}";
            $font = $fontMetrics->get_font("helvetica", "italic");
            $size = 7;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        ');

        $pdf->page_script('
            $pdf->line(25,560,820,560,array(0,0,0),1);
        ');
    }


</script>
</body>
</html>
