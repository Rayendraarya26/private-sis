@extends('layouts.layout_app')

@section('title', 'Ubah User')

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
                            <h3 class="dt-card__title">Edit User</h3>
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
                                <form method="post" action="{{url("$url/$id")}}" enctype="multipart/form-data">
                                    @csrf
                                    @method("PUT")
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="fullname">Fullname*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan fullname ..." type="text"
                                                   name="fullname" id="fullname"
                                                   value="{{empty(old('fullname')) ? $data->user_fullname : old('fullname')}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="email">Email*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan email..." type="email"
                                                   name="email" id="email"
                                                   value="{{empty(old('email')) ? $data->user_email : old('email')}}">
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
                                        <div class="col-sm-4">
                                            <input class="form-control" type="file" name="foto" id="foto"
                                                   accept="image/*">
                                        </div>
                                        <div class="col-sm-4">
                                            @if(!empty($data->user_picture))
                                                <div style="text-align: center; justify-content: center">
                                                    <img src="{{$data->user_picture}}" style="width: 200px" alt="foto">
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="no_telp">Group</label>
                                        <div class="col-sm-8">
                                            <div class="row">
                                                @foreach($groups as $group)
                                                    <div class="col-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox"
                                                                   name="group[]"
                                                                   value="{{$group->group_id}}" {{array_search($group->group_id, array_column($selected_group, "ug_group_id")) !== false ? 'checked' : ''}}>
                                                            {{$group->group_name}}
                                                        </label>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio"
                                                                   name="group_default" value="{{$group->group_id}}"
                                                                {{$group->group_id == $default_group ? "checked" :""}}>
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
