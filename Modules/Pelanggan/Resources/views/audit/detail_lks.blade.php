@extends("layouts.layout_app")

@section('title', 'Detail Audit')

@push('css')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css"/>
@endpush

@section('content')
    <div class="dt-content" id="perbaikanPage">
        <div class="row">
            <div class="col-md-12">
                <a class="btn btn-sm btn-default"
                   href="{{ url("$url/temuan-lks/".$data->sis_jadwal_audit->sis_jadwal->jadw_id) }}"
                   style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali
                </a>
                <div class="dt-card">
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            {!! implode('', $errors->all('<li>:message</li>')) !!}
                        </div>
                    @endif
                    @if(session('message'))
                        <div class="alert alert-success" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="dt-card__header" style="text-align: center">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Laporan Ketidaksesuaian dan Laporan Verifikasi</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table">
                                    <tr>
                                        <td style="width: 50px">1</td>
                                        <td>Jenis Kegiatan</td>
                                        <td>: {{$data->sis_jadwal_audit->jadw_audit_kegiatan}}</td>
                                    </tr>

                                    <tr>
                                        <td rowspan="3">2</td>
                                        <td>Nama Perusahaan</td>
                                        <td>: {{$data->sis_jadwal_audit->sis_permohonan->mohon_cust_nama}}</td>
                                    </tr>
                                    <tr>
                                        <td>No. Referensi</td>
                                        <td>: {{$data->sis_jadwal_audit->jadw_audit_nomor_referensi}}</td>
                                    </tr>
                                    <tr>
                                        <td>Alamat</td>
                                        <td>: {{$data->sis_jadwal_audit->sis_permohonan->mohon_cust_alamat}}</td>
                                    </tr>

                                    <tr>
                                        <td>3</td>
                                        <td>Tanggal Asesmen</td>
                                        <td>
                                            : {{ $data->sis_jadwal_audit->sis_jadwal->jadw_tanggal_mulai->isoFormat("LL") }}
                                            s/d {{ $data->sis_jadwal_audit->sis_jadwal->jadw_tanggal_selesai->isoFormat("LL") }}</td>
                                    </tr>

                                    <tr>
                                        <td>4</td>
                                        <td>Tim Asesmen</td>
                                        <td>:
                                            <ol>
                                                @foreach($data->sis_jadwal_audit->sis_jadwal->sis_jadwal_tims as $tim)
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
                                        <td>: {{$data->sis_jadwal_audit->jadw_audit_standart_acuan}}</td>
                                    </tr>

                                    <tr>
                                        <td>6</td>
                                        <td>Rekomendasi</td>
                                        <td>:</td>
                                    </tr>
                                </table>

                                <ol class="ol-rekomendasi">
                                    <li>
                                        <b>Inisial Auditor</b>: <br>
                                        {{ $data->sis_jadwal_audit->jadw_tim_kode }}
                                    </li>
                                    <li>
                                        <b>Uraian Ketidaksesuaian</b>: <br>
                                        {!! $data->lks_uraian_ketidaksesuaian !!}
                                    </li>
                                    <li>
                                        <b>*Tindakan Perbaikan</b>
                                        <div style="padding: 10px 0 0 10px">
                                            <h4>Analisis Penyebab</h4>
                                            {!! $data->lks_perbaikan_analisa !!}
                                        </div>
                                        <div style="padding: 10px 0 0 10px">
                                            <h4>Koreksi</h4>
                                            {!! $data->lks_perbaikan_koreksi !!}
                                        </div>
                                        <div style="padding: 10px 0 0 10px">
                                            <h4>Tindakan Korektif</h4>
                                            {!! $data->lks_perbaikan_tindakan !!}
                                        </div>
                                    </li>
                                    <li>
                                        <b>Bukti Tindakan Perbaikan</b>
                                        <ol>
                                            @foreach($data->sis_audit_lks_files as $file)
                                                <li>
                                                    <a target="_blank" href="{{asset($file->lks_filepath)}}">
                                                        Download Berkas</a>
                                                </li>
                                            @endforeach
                                        </ol>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
