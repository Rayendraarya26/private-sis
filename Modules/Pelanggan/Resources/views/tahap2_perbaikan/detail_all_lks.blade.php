@extends('layouts.layout_app')

@section('title', 'Detail Temuan LKS')

@section('content')
    <div class="dt-content" id="temuanPage">
        <div class="row">
            <div class="col-md-12">
                @if(session('message'))
                    <div class="alert alert-primary alert-dismissible fade show" role="alert">
                        {!! session('message') !!}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                @endif
                @error('message')
                <div class="alert alert-danger">
                    {{$message}}
                </div>
                @enderror
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title" style="text-align: center">
                                LAPORAN KETIDAKSESUAIAN dan LAPORAN VERIFIKASI
                            </h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="col-lg-12">
                            <table class="table">
                                <tr>
                                    <td style="width: 50px">1</td>
                                    <td>Jenis Kegiatan</td>
                                    <td>:
                                        @foreach($dataJadwal->sis_jadwal_audits as $audit)
                                            {{$audit->jadw_audit_kegiatan . (!$loop->last ? ' - ' : '.')}}
                                        @endforeach
                                    </td>
                                </tr>

                                <tr>
                                    <td rowspan="3">2</td>
                                    <td>Nama Perusahaan</td>
                                    <td>: {{$dataJadwal->sis_pelanggan->cust_nama}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>No. Referensi</td>
                                    <td>:
                                        @foreach($dataJadwal->sis_jadwal_audits as $audit)
                                            @if($audit->jadw_audit_nomor_referensi != "")
                                                {{$audit->jadw_audit_nomor_referensi . (!$loop->last ? ' ; ' : '.')}}
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>: {{$dataJadwal->sis_pelanggan->cust_alamat}}
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td>Tanggal Asesmen</td>
                                    <td>
                                        : {{ $dataJadwal->jadw_tanggal_mulai->isoFormat("LL") }}
                                        s/d {{ $dataJadwal->jadw_tanggal_selesai->isoFormat("LL") }}</td>
                                </tr>

                                <tr>
                                    <td>4</td>
                                    <td>Tim Asesmen</td>
                                    <td>:
                                        <ol>
                                            @foreach($dataJadwal->sis_jadwal_tims as $tim)
                                                <li>
                                                    {{$tim->master_pegawai->peg_nama}}
                                                    ({{ucwords($tim->jadw_tim_posisi)}})
                                                </li>
                                            @endforeach
                                        </ol>
                                    </td>
                                </tr>

                                <tr>
                                    <td>5</td>
                                    <td>Standar Acuan</td>
                                    <td>:
                                        @foreach($dataJadwal->sis_jadwal_audits as $audit)
                                            @if($audit->jadw_audit_standart_acuan != "")
                                                {{$audit->jadw_audit_standart_acuan . (!$loop->last ? ' ; ' : '.')}}
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title" style="text-align: center">
                                Temuan LKS
                            </h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="col-md-12">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th style="text-align: center;">Auditor</th>
                                    <th style="text-align: center;">Uraian Ketidaksesuaian</th>
                                    <th style="text-align: center;">Tindakan Perbaikan <br>
                                        <i>(Disertai analisis penyebab, Koreksi, dan Tindakan Koreksi)</i>
                                    </th>
                                    <th style="text-align: center;">Bagian <br>(Pendamping)</th>
                                    <th style="text-align: center;">Bukti Tindakan Perbaikan</th>
                                    <th style="text-align: center;">Hasil dan Tanggal <br>Verifikasi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($dataLks as $lks)
                                    <?php
                                    $hasilVerif = "";
                                    $verifKe    = 1;
                                    foreach ($lks->sis_audit_lks_revisis as $revisi) {
                                        if ($revisi->lks_revisi_oleh == "auditor") {
                                            $hasilVerif .= sprintf("<div style='text-align: center'>Verifikasi %d <br> %s</div>", $verifKe, $revisi->created_at->isoFormat("LL"));
                                            $hasilVerif .= sprintf("<br> %s <br><br>", $revisi->lks_revisi_catatan);
                                            $verifKe++;
                                        }
                                    }

                                    if ($lks->lks_sudah_ditutup == "ya") {
                                        $hasilVerif .= sprintf("<div style='text-align: center'>Verifikasi %d <br> %s </div>", $verifKe, $lks->lks_tanggal_ditutup->isoFormat("LL"));
                                        $hasilVerif .= sprintf("<br> %s <br><br> <b>LKS %d DITUTUP</b>", $lks->lks_catatan_ditutup, $lks->lks_nomor);
                                    }
                                    ?>
                                    <tr>
                                        <td>{{$lks->lks_nomor}}</td>
                                        <td>{{$lks->sis_jadwal_tim->jadw_tim_kode}}</td>
                                        <td>
                                            {!! $lks->lks_uraian_ketidaksesuaian !!}
                                            <br>
                                            <b>Kategori
                                                ketidaksesuaian</b>: {{ucwords($lks->lks_kategori_ketidaksesuaian)}}
                                            <br>
                                            <b>Klausul ketidak sesuaian</b>: {!! $lks->lks_klausul_ketidaksesuaian !!}
                                            <br>
                                            <b>Tgl Max Revisi</b>:
                                            {{ $lks->lks_expired_date_perbaikan->isoFormat("LL") }}
                                        </td>
                                        <td>
                                            <b style="font-size: 12px">Analisis Penyebab: </b>
                                            <br>
                                            {!! $lks->lks_perbaikan_analisa !!}
                                            <br><br>
                                            <b style="font-size: 12px">Koreksi: </b>
                                            {!! $lks->lks_perbaikan_koreksi !!}
                                            <br><br>
                                            <b style="font-size: 12px">Tindakan Korektif: </b>
                                            {!! $lks->lks_perbaikan_tindakan !!}
                                        </td>
                                        <td>
                                        {!! $lks->lks_bagian_pendamping !!}
                                        </td>
                                        <td>
                                            {!! $lks->lks_bukti_tindakan_perbaikan !!}

                                            @foreach($lks->sis_audit_lks_files as $file)
                                                <br>
                                                <a href="{{asset($file->lks_filepath)}}">
                                                    <i class="fad fa-download"></i> Berkas {{$loop->iteration}}
                                                </a>
                                            @endforeach
                                        </td>
                                        <td>{!! $hasilVerif !!}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <a href="{{url("$url")}}" class="btn btn-info btn-outline-info">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
