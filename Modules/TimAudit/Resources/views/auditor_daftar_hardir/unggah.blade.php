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
                                          action="{{action("$module@storeUnggah", $data->jadw_id)}}"
                                          enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3" for="jadw_file_kehadiran">
                                                Kehadiran*
                                                <br>
                                                <small>(pdf/excel)</small>
                                            </label>
                                            <div class="col-sm-8">
												<input type="file" name="jadw_file_kehadiran" id="jadw_file_kehadiran" accept="application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel">
                                                @if(!empty($data->jadw_file_kehadiran))
                                                    <small>
                                                        <a href="{{asset($data->jadw_file_kehadiran)}}" target="_blank">
                                                            <i class="fad fa-download"></i> Download Kehadiran
                                                        </a>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3" for="jadw_file_notulen_rapat">
                                                Notulen Rapat*
                                                <br>
                                                <small>(pdf/excel)</small>
                                            </label>
                                            <div class="col-sm-8">
                                                    <input type="file" name="jadw_file_notulen_rapat" id="jadw_file_notulen_rapat" accept="application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel">
                                                @if(!empty($data->jadw_file_notulen_rapat))
                                                    <small>
                                                        <a href="{{asset($data->jadw_file_notulen_rapat)}}"
                                                           target="_blank">
                                                            <i class="fad fa-download"></i> Download Notulen
                                                        </a>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-form-label col-sm-3" for="jadw_tanggal_rapat_akhir">
                                                Tanggal Rapat*
                                            </label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="jadw_tanggal_rapat_akhir" name="jadw_tanggal_rapat_akhir" style="max-width:300px;">
                                                <input type="hidden" name="cust_nama" value="{{$data->sis_pelanggan->cust_nama}}">
                                                <input type="hidden" name="cust_email" value="{{$data->sis_pelanggan->cust_email}}">
                                                <input type="hidden" name="cust_id" value="{{$data->sis_pelanggan->cust_id}}">
                                                <input type="hidden" name="user_id" value="{{$data->sis_pelanggan->user_id}}">
                                                <input type="hidden" name="jadw_id" value="{{$data->jadw_id}}">
                                            </div>
                                        </div>
										
                                        <button type="submit" class="btn btn-outline-primary btn-block">
                                            <i class="fas fa-paper-plane"></i> Ajukan temuan ke Pelanggan?
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


@push('javascript')
    <script>
        // Vue Step One
		
		function myformatter(date){
            var y = date.getFullYear();
            var m = date.getMonth()+1;
            var d = date.getDate();
            return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
        }
        function myparser(s){
            if (!s) return new Date();
            var ss = (s.split('-'));
            var y = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var d = parseInt(ss[2],10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                return new Date(y,m-1,d);
            } else {
                return new Date();
            }
        }
		
        $(document).ready(function () {
            $('#jadw_tanggal_rapat_akhir').datebox({
							required:true,
							editable: false,
							formatter:myformatter,
							parser:myparser,
							value:`@if(!empty($data->jadw_file_notulen_rapat)) {{$data->jadw_file_notulen_rapat}} @endif`,
							onSelect: async function(date){
								var data_date = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
							}
						});;
        })
    </script>
@endpush
