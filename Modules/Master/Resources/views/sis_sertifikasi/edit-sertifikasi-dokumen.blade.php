@extends('layouts.layout_app')

@section('title', 'Update Dokumen Sertifikasi')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default"
                   href="{{url("$url/detail?tipe=detail-dokumen&sert_id=$data->sert_id")}}"
                   style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali ke Dokumen Sertifikasi
                </a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Update Dokumen Sertifikasi "{{$data_sertifikat->sert_nama}}"</h3>
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
							
                                <form method="post" action="{{action("$module@update")}}">
                                    <!-- Security CSRF TOKEN -->
                                    @csrf
									<input type="hidden" name="tipe" value="update-sertifikasi-dokumen">
									<input type="hidden" name="sert_id" value="{{$data->sert_id}}">
									<input type="hidden" name="sert_dok_id" value="{{$data->sert_dok_id}}">
									
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="jenis_dok_perusahaan_text">Jenis Dokumen</label>
                                        <div class="col-sm-8">
											<label class="col-form-label" for="jenis_dok_perusahaan_text">"{{$data_dok->jenis_dok_perusahaan_text}}"</label>
                                        </div>
                                    </div>
									
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="sert_dok_required">Dokumen Harus Diisi? *</label>
                                        <div class="col-sm-8">
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="sert_dok_required" id="sert_dok_required" value="ya" {{$data->sert_dok_required == "ya" ? "checked" :""}} >
												<label class="form-check-label" for="sert_dok_required">Ya</label>
											</div>
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="sert_dok_required" id="sert_dok_required" value="tidak" {{$data->sert_dok_required == "tidak" ? "checked" :""}}>
												<label class="form-check-label" for="sert_dok_required">Tidak</label>
											</div>
                                        </div>
                                    </div>
									
									
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="sert_dok_keterangan">Keterangan</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control"
                                                   type="text" name="sert_dok_keterangan" id="sert_dok_keterangan"
                                                   value="">{{old('sert_dok_keterangan') ?? $data->sert_dok_keterangan}}</textarea>
                                        </div>
                                    </div>
									
                                    <div class="form-buttons-w">
                                        <button class="btn btn-success" type="submit">
                                            <i class="fas fa-save"></i> Simpan
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
