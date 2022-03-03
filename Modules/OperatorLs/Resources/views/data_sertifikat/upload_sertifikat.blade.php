@extends('layouts.layout_app')

@section('title', 'Upload Sertifikat')

@section('content')
	<div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali
                </a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Data Sertifikasi "#{{$data_sertifikat->master_sertifikasi->sert_nama}}"</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="row">
                            <div class="col-lg-12">
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
								<form action="{{action("$module@saveSertifikat")}}" method="POST" enctype="multipart/form-data">
                                    <!-- Security CSRF TOKEN -->
                                    @csrf
									<input type="hidden" name="cust_sert_id" value="{{$data_sertifikat->cust_sert_id}}">
									<div class="form-group row">
										<label class="col-form-label col-sm-3" for="">Nama Perusahaan</label>
										<div class="col-sm-8">
											<input type="hidden" id="cust_id" name="cust_id" class="form-control" value="{{$data_sertifikat->cust_id}}"/>
											<input type="hidden" id="sert_nama" name="sert_nama" class="form-control" value="{{$data_sertifikat->master_sertifikasi->sert_nama}}"/>
											<label class="" >{{$data_sertifikat->sis_pelanggan->cust_nama}}</label>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-form-label col-sm-3" for="">Jenis Sertifikat</label>
										<div class="col-sm-8">
											<label class="" >{{$data_sertifikat->master_sertifikasi->sert_nama}}</label>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-form-label col-sm-3" for="">Komoditas</label>
										<div class="col-sm-8">
											<label class="" >{{$data_sertifikat->master_komoditi->komodt_nama}}</label>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-form-label col-sm-3" for="">Tipe</label>
										<div class="col-sm-8">
											<label class="" >{{$data_sertifikat->cust_sert_tipe}}</label>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-form-label col-sm-3" for="">Merk</label>
										<div class="col-sm-8">
											<label class="" >{{$data_sertifikat->cust_sert_merk}}</label>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-form-label col-sm-3" for="">Tanggal Terbit</label>
										<div class="col-sm-8">
											<label class="" >{{$data_sertifikat->cust_sert_tgl_sertifikat_awal->format("d M Y")}}</label>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-form-label col-sm-3" for="">Tanggal Kadaluarsa</label>
										<div class="col-sm-8">
											<label class="" >{{$data_sertifikat->cust_sert_expired_date->format("d M Y")}}</label>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-form-label col-sm-3" for="cust_sert_filepath">File Sertifikat *</label>
										<div class="col-sm-8">
											<input class="form-control" accept="application/pdf" type="file" name="cust_sert_filepath">
											<input type="hidden" id="cust_sert_filepath_lama" name="cust_sert_filepath_lama" class="form-control" value="{{$data_sertifikat->cust_sert_filepath_lama}}"/>
											<small id="" class="form-text">Note: Upload file sertifikat yang sudah ditanda tangani;</small>
											@if($data_sertifikat->cust_sert_filepath != '')
												<hr/>
												<a target="_blank" href="{{url($data_sertifikat->cust_sert_filepath)}}"><span class="fad fa-download"></span> File Kajian Sertifikat lama</a>
											@endif
										</div>
									</div>
                                    <div class="form-buttons-w">
                                        <button class="btn btn-sm btn-success " type="submit">
                                            <i class="fas fa-upload"></i> Upload
                                        </button>
                                    </div>
                                </form>
								
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push("javascript")
	<script>
		$(document).ready(function () {
			
        });
    </script>
@endpush
