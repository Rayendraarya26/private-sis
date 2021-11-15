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
                            <h3 class="dt-card__title">LAPORAN KETIDAKSESUAIAN dan LAPORAN VERIFIKASI</h3>
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
                                </table>


                            </div>
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
                                    <td>{{$data->sis_jadwal_tim->jadw_tim_kode}}</td>
                                    <td>
                                        {!! $data->lks_uraian_ketidaksesuaian !!}
                                        <br>
                                        Kategori ketidaksesuaian: {{ucwords($data->lks_kategori_ketidaksesuaian)}} <br>
                                        Klausul ketidak sesuaian: {!! $data->lks_klausul_ketidaksesuaian !!}
                                    </td>
                                    <td>
                                        {!! $data->lks_perbaikan_analisa !!}
                                        {!! $data->lks_perbaikan_koreksi !!}
                                        {!! $data->lks_perbaikan_tindakan !!}
                                    </td>
                                    <td>
                                        {!! $data->lks_bukti_tindakan_perbaikan !!}

                                        @foreach($data->sis_audit_lks_files as $file)
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
            </div>
        </div>
    </div>
@endsection
