@extends('layouts.layout_app')

@section('title', "Perbarui Profile")

@section('content')
    <!-- Site Content -->
    <div class="dt-content">
        <!-- Card -->
        <div class="dt-card">
            <!-- Card Body -->
            <div class="dt-card__body">
                @if(session('message'))
                    <div class="alert alert-success" role="alert">
                        {{ session('message') }}
                    </div>
                @endif

                @error('message')
                <div class="alert alert-danger">
                    {{$message}}
                </div>
            @enderror
            <!-- Form -->
                <form method="post" action="{{route('update_profile')}}"
                      onsubmit="$('#btn-submit').attr('disabled', 'true')">
                @csrf
                <!-- Form Group -->
                    <div class="form-group form-row">
                        <label class="col-xl-3 col-form-label text-sm-right" for="fullname">
                            Nama Lengkap
                        </label>

                        <div class="col-xl-9">
                            <input type="text" name="fullname" class="form-control" id="fullname"
                                   placeholder="Masukkan nama lengkap..."
                                   value="{{auth()->user()->user_fullname}}">

                            @error('fullname')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                    <!-- /form group -->

                    <!-- Form Group -->
                    <div class="form-group form-row">
                        <div class="col-xl-9 offset-xl-3">
                            <button type="submit" class="btn btn-primary text-uppercase" id="btn-submit">
                                <i class="fas fa-save"></i> Perbarui
                            </button>
                        </div>
                    </div>
                    <!-- /form group -->
                </form>
                <!-- /form -->

            </div>
            <!-- /card body -->

        </div>
        <!-- /card -->
    </div>
@endsection
