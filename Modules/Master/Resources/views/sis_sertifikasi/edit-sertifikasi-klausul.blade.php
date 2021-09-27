@extends('layouts.layout_app')

@section('title', 'Edit Klausul Sertifikasi')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default"
                   href="{{url("$url/detail?tipe=detail-klausul&sert_id=$data->sert_id")}}"
                   style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali ke Klausul Sertifikasi
                </a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Edit Klausul Sertifikasi "{{$data_sertifikat->sert_nama}}"</h3>
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
									
									<input type="hidden" name="tipe" value="update-sertifikasi-klausul">
									<input type="hidden" name="sert_id" value="{{$data->sert_id}}">
									<input type="hidden" name="sert_klau_id" value="{{$data->sert_klau_id}}">
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="sert_klau_nomor">No *</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="sert_klau_nomor" name="sert_klau_nomor" class="form-control" value="{{old('sert_klau_nomor') ?? $data->sert_klau_nomor}}"/>
                                        </div>
                                    </div>
									
									
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="sert_klau_peryataan">Pernyataan *</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" name="sert_klau_peryataan" id="sert_klau_peryataan">{{old('sert_klau_peryataan') ?? $data->sert_klau_peryataan}}</textarea>
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
