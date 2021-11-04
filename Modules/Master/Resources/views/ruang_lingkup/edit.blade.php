@extends('layouts.layout_app')

@section('title', 'Edit Ruang Lingkup')

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
                            <h3 class="dt-card__title">Edit Ruang Lingkup</h3>
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
                                    <input type="hidden" name="ruang_ling_id" value="{{$data->ruang_ling_id}}">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="ruang_ling_nama">Nama Ruang Lingkup*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan nama Ruang Lingkup ..."
                                                   type="text" name="ruang_ling_nama" id="ruang_ling_nama"
                                                   value="{{old('ruang_ling_nama') ?? $data->ruang_ling_nama}}">
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
