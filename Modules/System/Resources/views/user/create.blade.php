@extends('layouts.layout_app')

@section('title', 'Tambah Kelola User')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default" href="{{ url("$module") }}" style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali
                </a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Tambah User</h3>
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
                                <form method="post" action="{{url("$module")}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="nama_guru">Username*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan username ..." type="text"
                                                   name="username" id="username" value="{{old('username')}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="no_telp">Email*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan email..." type="email"
                                                   name="email" id="email" value="{{old('email')}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="no_telp">Kata sandi*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan kata sandi..."
                                                   type="password" name="password" id="password"
                                                   value="{{old('password')}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="no_telp">Konfirmasi Password*</label>
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

                                    <hr>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="no_telp">Group</label>
                                        <div class="col-sm-8">
                                            <div class="row">
                                                @foreach($groups as $group)
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox"
                                                                   name="group[]"
                                                                   value="{{$group->group_id}}">
                                                            {{$group->group_name}}
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio"
                                                                   name="group_default"
                                                                   value="{{$group->group_id}}">
                                                            default
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
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
