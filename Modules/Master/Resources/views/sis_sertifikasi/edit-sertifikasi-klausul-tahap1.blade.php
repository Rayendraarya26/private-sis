@extends('layouts.layout_app')

@section('title', 'Edit Klausul Tahap I Sertifikasi')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default"
                   href="{{url("$url/detail?tipe=detail-klausul-tahap1&sert_id=$data->sert_id")}}"
                   style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali ke Klausul Tahap I Sertifikasi
                </a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Edit Klausul Tahap I Sertifikasi "{{$data_sertifikat->sert_nama}}"</h3>
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
									
									<input type="hidden" name="tipe" value="update-sertifikasi-klausul-tahap1">
									<input type="hidden" name="sert_id" value="{{$data->sert_id}}">
									<input type="hidden" name="klausul_thp1_id" value="{{$data->klausul_thp1_id}}">
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="klausul_thp1_nomor">No *</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="klausul_thp1_nomor" name="klausul_thp1_nomor" class="form-control" value="{{old('klausul_thp1_nomor') ?? $data->klausul_thp1_nomor}}"/>
                                        </div>
                                    </div>
									
									
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="klausul_thp1_peryataan">Pernyataan *</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" name="klausul_thp1_peryataan" id="klausul_thp1_peryataan">{{old('klausul_thp1_peryataan') ?? $data->klausul_thp1_peryataan}}</textarea>
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
