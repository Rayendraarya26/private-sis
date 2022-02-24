<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Notulen Rapat</title>
    <style>
        .text-center {
            text-align: center;
            justify-content: center;
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
        <img src="{{public_path('/images/logos/sis_ls_bbkkp.png')}}" alt="Logo"
             style="max-width: 120px; margin-top: -15px">
    </div>
    <div class="headers_two">
        <div class="text-center">
            <div style="font-weight: bold">
                NOTULEN RAPAT
            </div>
        </div>
    </div>
</div>

<div>
    <div>
        <table>
            <tr>
                <td>Hari, tanggal</td>
                <td>:</td>
                <td>{{ $data->aud_thp1_tanggal_selesai->isoFormat("LL") }}</td>
            </tr>
            <tr>
                <td>Tempat</td>
                <td>:</td>
                <td>{{ $data->sis_permohonan->mohon_cust_nama }}</td>
            </tr>
            <tr>
                <td>Pimpinan</td>
                <td>:</td>
                <td>{{ $ketua->master_pegawai->peg_nama }}</td>
            </tr>
            <tr>
                <td>Jumlah Peserta <br><i>(Rekaman Kehadiran Terlampir)</i></td>
                <td>:</td>
                <td>... Orang</td>
            </tr>
            <tr>
                <td>Materi</td>
                <td>:</td>
                <td>Penyampaian Hasil Audit / Rapat Penutupan Audit Tahap
                    I {{$data->sis_permohonan_detail->master_sertifikasi->sert_nama}}</td>
            </tr>
        </table>
    </div>
    <div style="padding: 30px 0 10px 0">
        <hr>
    </div>
    <div>
        <p><span style="font-weight: bold; font-size: 16px">HASIL</span><br><span><i>(Jika tidak mencukupi dapat menggunakan lembaran lain)</i></span>
        </p>
        <br>
        {!!  $data->aud_thp1_notulen  !!}
    </div>

    <div style="padding-top: 40px">
        <table style="padding-left: 100px">
            <tr>
                <td>
                    <table>
                        <tbody>
                        <tr>
                            <td style="text-align: center">
                                Mengetahui
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 100px">

                            </td>
                        </tr>
                        <tr style="text-align: center">
                            <td>{{ $data->aud_thp1_pengesahan_client_nama }}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td style="padding-left: 180px"></td>
                <td>
                    <table>
                        <tbody>
                        <tr>
                            <td style="text-align: center">
                                Yogyakarta, {{ \Carbon\Carbon::now()->isoFormat("LL") }}
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 100px">
                                @if(!empty($ketua->master_pegawai->peg_ttd_base64))
                                    <img src="{{ $ketua->master_pegawai->peg_ttd_base64 }}" alt="ttd ketua"
                                         style="max-height: 100px;">
                                @elseif(!empty($ketua->master_pegawai->peg_ttd_file))
                                    <img src="{{public_path($ketua->master_pegawai->peg_ttd_file)}}" alt="ttd ketua"
                                         style="max-height: 100px;">
                                @endif
                            </td>
                        </tr>
                        <tr style="text-align: center">
                            <td>{{ $ketua->master_pegawai->peg_nama }}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
