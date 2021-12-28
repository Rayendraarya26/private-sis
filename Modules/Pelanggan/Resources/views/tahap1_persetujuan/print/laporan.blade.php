@extends("layouts.layout_blank")

@section('title', 'Laporan Hasil Audit Tahap 1')
@push("css")
    <!-- HTML -->
    <style>
        body {
            font-family: helvetica;
            margin: 10px;
        }

        @page {
            margin: 40px;
        }

        body {
            font-size: 11pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000000;
        }

        thead {
            vertical-align: middle !important;
            background: #ECF0F5;
        }

        th, td {
            padding: 5px !important;
        }

        th.data {
            border: 1px solid #000000;
            border-collapse: collapse;
        }

        td.data {
            border: 1px solid #000000;
            border-collapse: collapse;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
            padding: 10px;
        }

        .left {
            text-align: left;
            padding: 10px;
        }

        table {
            page-break-inside: auto
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto
        }
    </style>
@endpush

@section('content')
    <div id="content">
        <h3 class="center" style="margin:0px;">Laporan Hasil Audit Tahap 1</h3>
    </div>
    <table>
        <thead>
        <tr>
            <th class="left data" colspan="3">II. Umum</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Tahap Kegiatan</td>
            <td>:</td>
            <td>Audit Tahap 1 @if($restAudit->mohon_det_jenis_status == 'baru') Sertifikasi Awal @else Sertifikasi
                Ulang @endif @if($restAudit->sert_is_product == 'ya'){{$restAudit->sni}} @else {{$restAudit->sert_sni}} @endif</td>
        </tr>
        <tr>
            <td>Tanggal Pelaksanaan Audit</td>
            <td>:</td>
            <td>{{date('d M Y', strtotime($restAudit->aud_thp1_tanggal_mulai))}}
                s/d {{date('d M Y', strtotime($restAudit->aud_thp1_tanggal_selesai))}}</td>
        </tr>
        <tr>
            <td>Nama Perusahaan</td>
            <td>:</td>
            <td>{{$restAudit->cust_nama}}</td>
        </tr>
        <tr>
            <td>Nama Referensi</td>
            <td>:</td>
            <td>{{$restAudit->mohon_det_no_referensi}}</td>
        </tr>
        <tr>
            <td>Jumlah Karyawan</td>
            <td>:</td>
            <td>{{$restAudit->mohon_cust_jumlah_operasional}}</td>
        </tr>
        <tr>
            <td>Komoditas</td>
            <td>:</td>
            <td>{{$restAudit->komodt_nama}}</td>
        </tr>
        <tr>
            <td>Kapasitas Produksi</td>
            <td>:</td>
            <td>{{$restAudit->produksi}} / Tahun</td>
        </tr>
        <tr>
            <td>Alamat Perusahaan</td>
            <td>:</td>
            <td>{{$restAudit->cust_alamat}}</td>
        </tr>
        <tr>
            <td>Tujuan</td>
            <td>:</td>
            <td>{{$restAudit->aud_thp1_tujuan}}</td>
        </tr>
        <tr>
            <td>Jenis</td>
            <td>:</td>
            <td>{{ucfirst($restAudit->aud_thp1_jenis)}}</td>
        </tr>
        </tbody>
    </table>
    <table style="border-top:0px;">
        <thead>
        <tr>
            <th class="left data" style="border-top:0px;">III. Susunan Tim Audit</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                @foreach($dataTim as $tim)
                    {{ucfirst($tim->thp1_tim_posisi)}} : {{$tim->peg_nama}}
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>
    <table style="border-top:0px;">
        <thead>
        <tr>
            <th class="left data" style="border-top:0px;">IV. Jumlah Temuan LKS</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Jumlah Temuan Audit Tahap I : {{$jmlTemuan}}</td>
        </tr>
        <tr>
            <td>Uraian Temuan Dapat Dilihat Pada LKS(Hasil)</td>
        </tr>
        </tbody>
    </table>
    <table style="border-top:0px;">
        <thead>
        <tr>
            <th class="left data" style="border-top:0px;">V. Audit kecukupan informasi terdokumentasi</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{!! $restAudit->aud_thp1_kolom_v !!}</td>
        </tr>
        </tbody>
    </table>
    <table style="border-top:0px;">
        <thead>
        <tr>
            <th class="left data" style="border-top:0px;">VI. Kondisi Lapangan</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{!! $restAudit->aud_thp1_kolom_vi !!}</td>
        </tr>
        </tbody>
    </table>
    <table style="border-top:0px;">
        <thead>
        <tr>
            <th class="left data" style="border-top:0px;">VII. Status dan pemahaman persyaratan standar</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{!! $restAudit->aud_thp1_kolom_vii !!}</td>
        </tr>
        </tbody>
    </table>
    <table style="border-top:0px;">
        <thead>
        <tr>
            <th class="left data" style="border-top:0px;">VIII. Informasi yang diperlukan yang berkenaan dengan (lingkup
                sistem manajemen K3, proses dan lokasi perusahaan, identifikasi bahaya dan risiko dan
                perundang-undangan/peraturan K3, dari operasi perusahaan dan risiko) tersedia.
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{!! $restAudit->aud_thp1_kolom_viii !!}</td>
        </tr>
        </tbody>
    </table>
    <table style="border-top:0px;">
        <thead>
        <tr>
            <th class="left data" style="border-top:0px;">IX. Sumber daya yang tersedia</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{!! $restAudit->aud_thp1_kolom_ix !!}</td>
        </tr>
        </tbody>
    </table>
    <table style="border-top:0px;">
        <thead>
        <tr>
            <th class="left data" style="border-top:0px;">X. Konfirmasi program audit sertifikasi tahap 2</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{!! $restAudit->aud_thp1_kolom_x !!}</td>
        </tr>
        </tbody>
    </table>
    <table style="border-top:0px;">
        <thead>
        <tr>
            <th class="left data" style="border-top:0px;">XI. Informasi pelaksanaan audit internal dan kaji ulang
                manajemen
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{!! $restAudit->aud_thp1_kolom_xi !!}</td>
        </tr>
        </tbody>
    </table>
    <table style="border-top:0px;">
        <thead>
        <tr>
            <th class="left data" style="border-top:0px;">XII. Kesimpulan</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{!! $restAudit->aud_thp1_kolom_xii !!}</td>
        </tr>
        </tbody>
    </table>
@endsection
