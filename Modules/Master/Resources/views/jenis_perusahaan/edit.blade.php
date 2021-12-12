@extends('layouts.layout_app')

@section('title', 'Edit Jenis Perusahaan')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default"
                   href="{{url("$url")}}"
                   style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali
                </a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Edit Jenis Perusahaan</h3>
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

                            <!--
                                    ada 3 cara:
                                    action(): mengarah ke controller
                                    url(): mengarah ke lokasi url
                                    route(): mengarah ke nama route
                                -->
                                <form method="post" action="{{action("$module@update")}}">
                                    <!-- Security CSRF TOKEN -->
                                    @csrf
                                    <input type="hidden" name="jenis_perusahaan_id" value="{{$data->jenis_perusahaan_id}}">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="jenis_perusahaan_nama">Nama Jenis Perusahaan*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan nama jenis perusahaan ..."
                                                   type="text" name="jenis_perusahaan_nama" id="jenis_perusahaan_nama"
                                                   value="{{old('jenis_perusahaan_nama') ?? $data->jenis_perusahaan_nama}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="jenis_perusahaan_color">Warna*</label>
                                        <div class="col-sm-2">
                                            <input class="form-control" type="color" name="jenis_perusahaan_color" id="jenis_perusahaan_color" value="{{old('jenis_perusahaan_color') ?? $data->jenis_perusahaan_color}}">
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
