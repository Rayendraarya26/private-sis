@extends('layouts.layout_app')

@section('title', "Perbarui Profile")

@section('content')
<div class="dt-content">
	<a class="btn btn-sm btn-default" href="{{ url("/account/profile") }}" style="margin-bottom: 20px">
	    <i class="fad fa-arrow-left"></i> Kembali
	</a>
    <div class="dt-card">
        <div class="dt-card__header">
            <div class="dt-card__heading">
                <h3 class="dt-card__title">Perbarui Profile</h3>
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
                    <form method="post" action="{{url("/account/update/profile")}}" enctype="multipart/form-data">
                        @csrf
                        @method("PUT")
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3"
                                   for="fullname">Fullname*</label>
                            <div class="col-sm-8">
                                <input class="form-control" placeholder="Masukkan fullname ..." type="text"
                                       name="fullname" id="fullname"
                                       value="{{empty(old('fullname')) ? auth()->user()->user_fullname : old('fullname')}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="email">Email*</label>
                            <div class="col-sm-8">
                                <input class="form-control" placeholder="Masukkan email..." type="email"
                                       name="email" id="email"
                                       value="{{empty(old('email')) ? auth()->user()->user_email : old('email')}}">
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
                                @if(auth()->user()->user_picture)
                                    <div style="text-align: center; justify-content: center">
                                        <img src="{{auth()->user()->user_picture}}" style="width: 200px" alt="foto">
                                    </div>
                                @endif
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
@endsection

@push("javascript")
<script>
</script>
@endpush