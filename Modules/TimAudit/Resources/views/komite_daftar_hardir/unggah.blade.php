@extends('layouts.layout_app')

@section('title', 'Unggah Daftar Hadir dan Notulen')

@section('content')
    <div class="dt-content">
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

                <div class="row">
                    <div class="col-md-8">
                        <div class="dt-card">
                            <div class="dt-card__header">
                                <div class="dt-card__heading">
                                    <h3 class="dt-card__title" style="text-align: center">
                                        JADWAL AUDIT
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
                                                            {{$tim->master_pegawai->peg_nama}} | {{$tim->jadw_tim_kode}}
                                                            <b>({{ucwords($tim->jadw_tim_posisi)}})</b>
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
										
										<tr>
                                            <td>4</td>
                                            <td>Tim Komite</td>
                                            <td>:
                                                <ol>
                                                    @foreach($data->sis_audit_tim_komites as $tim)
                                                        <li>
                                                            {{$tim->master_pegawai->peg_nama}}
                                                            <b>({{ucwords($tim->komite_posisi)}})</b>
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dt-card">
                            <div class="dt-card__header">
                                <div class="dt-card__heading">
                                    <h3 class="dt-card__title" style="text-align: center">
                                        UNGGAH NOTULEN RAPAT DAN KEHADIRAN
                                    </h3>
                                </div>
                            </div>
                            <div class="dt-card__body">
                                <div class="col-lg-12">
                                    <form method="post"
                                          action="{{action("$module@update", $data->jadw_id)}}"
                                          enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3" for="jadw_file_kehadiran_komite">
                                                Kehadiran*
                                                <br>
                                                <small>(pdf/excel)</small>
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="custom-file">
                                                    <input type="file" name="jadw_file_kehadiran_komite" class="custom-file-input" id="jadw_file_kehadiran_komite" accept="application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel">
                                                    <label class="custom-file-label" for="jadw_file_kehadiran_komite">Unggah file...</label>
													<input type="hidden" name="jadw_id" value="{{$jadwal_id}}">
                                                </div>
                                                @if(!empty($data->jadw_file_kehadiran_komite))
                                                    <hr/>
                                                        <a href="{{asset($data->jadw_file_kehadiran_komite)}}" target="_blank">
                                                            <i class="fad fa-download"></i> Download Kehadiran Existing
                                                        </a>
                                                @endif
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-outline-primary btn-block">
                                            <i class="fas fa-paper-plane"></i> Submit
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
