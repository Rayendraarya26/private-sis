<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HASIL rekomen-lks DOKUMEN</title>
    <style>
        .text-center {
            text-align: center;
            justify-content: center;
        }


        section, span, table, tr, th, td, #rekomen-lks {
            font-size: 12px;
        }

        #rekomen-lks {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #rekomen-lks td, #rekomen-lks th {
            border: 1px solid black;
        }

        #rekomen-lks tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #rekomen-lks tr:hover {
            background-color: #ddd;
        }

        #rekomen-lks th {
            padding-top: 5px;
            padding-bottom: 5px;
            background-color: #FBD4B4;
            color: black;
        }

        #rekomen-lks td {
            padding: 0 10px 0 10px
        }

        header {
            position: fixed;
            right: 0px;
            /*background-color: lightblue;*/
            height: 110px;
        }
    </style>
</head>
<body style="margin-top: 50px">
<header>
    <div style="float: left">
    <img src="{{public_path('/images/logos/sis_ls_bbkkp.png')}}" alt="Logo"
         style="max-width: 150px; margin-top: -15px">
    </div>
</header>

<div class="text-center" >
    <div style="font-weight: bold; font-size: 16px">
        LAPORAN KETIDAKSESUAIAN dan LAPORAN VERIFIKASI
    </div>
</div>

<section style="margin-top: 0px">
    <table>
        <tr>
            <td>1.</td>
            <td>Jenis Kegiatan</td>
            <td>:</td>
            <td>@foreach($dataJadwal->sis_jadwal_audits as $audit)
                    {{$audit->jadw_audit_kegiatan . (!$loop->last ? ' - ' : '.')}}
                @endforeach</td>
        </tr>
        <tr>
            <td>2.</td>
            <td>Nama Perusahaan</td>
            <td>:</td>
            <td>{{$dataJadwal->sis_pelanggan->cust_nama}}</td>
        </tr>
        <tr>
            <td>3.</td>
            <td>No. Referensi</td>
            <td>:</td>
            <td>@foreach($dataJadwal->sis_jadwal_audits as $audit)
                    @if($audit->jadw_audit_nomor_referensi != "")
                        {{$audit->jadw_audit_nomor_referensi . (!$loop->last ? ' ; ' : '.')}}
                    @endif
                @endforeach</td>
        </tr>
        <tr>
            <td>4.</td>
            <td>Alamat</td>
            <td>:</td>
            <td>{{$dataJadwal->sis_pelanggan->cust_alamat}}</td>
        </tr>
        <tr>
            <td>5.</td>
            <td>Tanggal Asesmen</td>
            <td>:</td>
            <td>{{ $dataJadwal->jadw_tanggal_mulai->isoFormat("LL") }}
                s/d {{ $dataJadwal->jadw_tanggal_selesai->isoFormat("LL") }}</td>
        </tr>
        <tr>
            <td>6.</td>
            <td>Tim Asesmen</td>
            <td>:</td>
            <td>
                <table>
                    @foreach($dataJadwal->sis_jadwal_tims as $tim)
                        <tr>
                            <td>{{$loop->iteration}}.</td>
                            <td>{{$tim->master_pegawai->peg_nama}} ({{ucwords($tim->jadw_tim_posisi)}})</td>
                        </tr>
                    @endforeach
                </table>
        </tr>
        <tr>
            <td>7.</td>
            <td>Standar Acuan</td>
            <td>:</td>
            <td>@foreach($dataJadwal->sis_jadwal_audits as $audit)
                    @if($audit->jadw_audit_standart_acuan != "")
                        {{$audit->jadw_audit_standart_acuan . (!$loop->last ? ' ; ' : '.')}}
                    @endif
                @endforeach</td>
        </tr>
        <tr>
            <td>8.</td>
            <td>Rekomendasi</td>
            <td>:</td>
            <td>{!! $dataJadwal->sis_audit_lap_ringkas?->lap_ringkas_rekomendasi !!}</td>
        </tr>
    </table>
    <br>

    <table id="rekomen-lks">
        <thead>
        <tr>
            <th style="width: 10%">No. <br>(Inisial Auditor)</th>
            <th>Uraian Ketidaksesuaian</th>
{{--            <th>Tindakan Perbaikan <br>--}}
{{--                <i>(Disertai analisis penyebab, Koreksi, dan Tindakan Koreksi)</i>--}}
{{--            </th>--}}
{{--            <th>Bukti Tindakan Perbaikan</th>--}}
{{--            <th>Bagian <br>(Pendamping)</th>--}}
{{--            <th>Hasil dan Tanggal <br> Verifikasi</th>--}}
        </tr>
        </thead>
        <tbody>
        @foreach($dataLks as $lks)
            <tr>
                <td style="text-align: center">{{$loop->iteration}} <br> ({{$lks->sis_jadwal_tim->jadw_tim_kode}})</td>
                <td style="padding: 5px">
{{--                    {!! $lks->lks_uraian_ketidaksesuaian !!}--}}
                    {{ strip_tags($lks->lks_uraian_ketidaksesuaian) }}
                    <br>
                    <br>
                    Kategori ketidaksesuaian: {{ucwords($lks->lks_kategori_ketidaksesuaian)}}
                    <br>
                    <br>
{{--                    Klausul ketidak sesuaian: {!! $lks->lks_klausul_ketidaksesuaian !!}--}}
                    Klausul ketidak sesuaian: {{ strip_tags($lks->lks_klausul_ketidaksesuaian) }}
                </td>

{{--                <td>--}}
{{--                    {!! $lks->lks_perbaikan_analisa !!}--}}
{{--                    {!! $lks->lks_perbaikan_koreksi !!}--}}
{{--                    {!! $lks->lks_perbaikan_tindakan !!}--}}
{{--                </td>--}}
{{--                <td>{!! $lks->lks_bagian_pendamping !!}</td>--}}
{{--                <td>--}}
{{--                    {!! $lks->lks_bukti_tindakan_perbaikan !!}--}}

{{--                    @foreach($lks->sis_audit_lks_files as $file)--}}
{{--                        <br>--}}
{{--                        <a href="{{asset($file->lks_filepath)}}">--}}
{{--                            <i class="fad fa-download"></i> Berkas {{$loop->iteration}}--}}
{{--                        </a>--}}
{{--                    @endforeach--}}
{{--                </td>--}}
{{--                <td></td>--}}
            </tr>
        @endforeach
        </tbody>
    </table>

    <br>
    <div style="padding-left: 200px">
        <table>
            <tr>
                <td>
                    <table style="WIDTH: 200px">
                        <tbody>
                        <tr>
                            <td style="font-size: 11pt; text-align: center">
                                <strong>Diketahui oleh,</strong>
                                <br>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 100px"></td>
                        </tr>
                        <tr>
                            <td style="font-size: 11pt; text-align: center">
                                <b><u>{{$dataJadwal->jadw_setujui_nama}}</u></b>
                            </td>
                        </tr>
                        <tr>
                            <td style="FONT-SIZE: 11pt; text-align: center">
                                <b>{{$dataJadwal->jadw_setujui_jabatan}}</b>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td style="padding-left: 220px"></td>
                <td>
                    <table style="WIDTH: 300px">
                        <tbody>
                        <tr>
                            <td style="font-size: 11pt; text-align: center">
                                <strong>Yogyakarta, {{$dataJadwal->jadw_tanggal_selesai->isoFormat('LL')}}</strong>
                                <br>
                                <strong>Dilaporkan oleh,</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">
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
                            <td style="font-size: 11pt; text-align: center">
                                <b><u>{{$dataKetua->master_pegawai->peg_nama}}</u></b>
                            </td>
                        </tr>
                        <tr>
                            <td style="FONT-SIZE: 11pt; text-align: center">
                                <b>Ketua Tim</b>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>

            <tr>
                <td></td>
                <td></td>
                <td>
                    <table style="WIDTH: 300px; padding-top: 50px">
                        <tbody>
                        <tr>
                            <td style="font-size: 11pt; text-align: center">
                                <strong>Yogyakarta, {{\Carbon\Carbon::now()->isoFormat('LL')}}</strong>
                                <br>
                                <strong>Diverifikasi oleh,</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">
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
                            <td style="font-size: 11pt; text-align: center">
                                <b><u>{{$dataKetua->master_pegawai->peg_nama}}</u></b>
                            </td>
                        </tr>
                        <tr>
                            <td style="FONT-SIZE: 11pt; text-align: center">
                                <b>Ketua Tim</b>
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
            $y = 570;
            $text = "F-TA-9";
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
            $x = 400;
            $y = 570;
            $text = "Rev. 3";
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
