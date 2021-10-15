@extends('layouts.layout_app')

@section('title', 'Tambah Pelanggan')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default" href="{{ url("$url") }}" style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali
                </a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Tambah Pelanggan</h3>
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
                                <form method="post" action="{{action("$module@store")}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="fullname">Fullname*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan nama lengkap ..."
                                                   type="text"
                                                   name="fullname" id="fullname" value="{{old('fullname')}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="email">Email*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan email..." type="email"
                                                   name="email" id="email" value="{{old('email')}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="password">Kata sandi*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan kata sandi..."
                                                   type="password" name="password" id="password"
                                                   value="{{old('password')}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="password_confirmation">Konfirmasi Password*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control"
                                                   placeholder="Masukkan ulang kata sandi..."
                                                   type="password" name="password_confirmation"
                                                   id="password_confirmation"
                                                   value="{{old('password_confirmation')}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="tgl_lahir">
                                            Foto
                                            <small>(jpg/jpeg/png)</small>
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="file" name="foto" id="foto"
                                                   accept="image/*">
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
