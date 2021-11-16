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
                                        @foreach($data->sis_jadwal_audits as $audit)
                                            {{$audit->jadw_audit_kegiatan . (!$loop->last ? ' - ' : '.')}}
                                        @endforeach
                                    </td>
                                </tr>

                                <tr>
                                    <td rowspan="3">2</td>
                                    <td>Nama Perusahaan</td>
                                    <td>: {{$data->sis_pelanggan->cust_nama}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>No. Referensi</td>
                                    <td>:
                                        @foreach($data->sis_jadwal_audits as $audit)
                                            @if($audit->jadw_audit_nomor_referensi != "")
                                                {{$audit->jadw_audit_nomor_referensi . (!$loop->last ? ' ; ' : '.')}}
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>: {{$data->sis_pelanggan->cust_alamat}}
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td>Tanggal Asesmen</td>
                                    <td>
                                        : {{ $data->jadw_tanggal_mulai->isoFormat("LL") }}
                                        s/d {{ $data->jadw_tanggal_selesai->isoFormat("LL") }}</td>
                                </tr>

                                <tr>
                                    <td>4</td>
                                    <td>Tim Asesmen</td>
                                    <td>:
                                        <ol>
                                            @foreach($data->sis_jadwal_tims as $tim)
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
                                        @foreach($data->sis_jadwal_audits as $audit)
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
                                Rekomendasi LKS
                            </h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="col-md-12">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Auditor</th>
                                    <th>Uraian Ketidaksesuaian</th>
                                    <th>Tindakan Perbaikan <br>
                                        <i>(Disertai analisis penyebab, Koreksi, dan Tindakan Koreksi)</i>
                                    </th>
                                    <th>Bukti Tindakan Perbaikan</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{$lks->sis_jadwal_tim->jadw_tim_kode}}</td>
                                    <td>
                                        {!! $lks->lks_uraian_ketidaksesuaian !!}
                                        <br>
                                        Kategori ketidaksesuaian: {{ucwords($lks->lks_kategori_ketidaksesuaian)}} <br>
                                        Klausul ketidak sesuaian: {!! $lks->lks_klausul_ketidaksesuaian !!}
                                    </td>
                                    <td>
                                        {!! $lks->lks_perbaikan_analisa !!}
                                        {!! $lks->lks_perbaikan_koreksi !!}
                                        {!! $lks->lks_perbaikan_tindakan !!}
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
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                    @if(!in_array($lks->lks_status, ['memadai', 'tidak-memadai']))
                        <div style="padding-bottom: 50px">
                            <div style="float: left">
                                <a href="{{url("$url/temuan/$data->jadw_id/edit/$lks->lks_id")}}"
                                   class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Revisi
                                </a>
                            </div>
                            <div style="float: right">
                                <button data-toggle="modal" data-target="#verifikasi-modal" class="btn btn-success">
                                    <i class="fas fa-check"></i> Verifikasi
                                </button>
                            </div>
                        </div>
                    @endif
            </div>
        </div>
    </div>

    @include("timaudit::auditor_lks._part.verifikasi_modal", ['module' => $module, 'jadwalID' => $data->jadw_id, 'lksID' => $lks->lks_id])
@endsection
