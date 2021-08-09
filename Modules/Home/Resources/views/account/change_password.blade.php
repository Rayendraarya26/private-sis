@extends('layouts.layout_app')

@section('title', "Perbarui Kata Sandi")

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
                <form method="post" action="{{route('change_password')}}"
                      onsubmit="$('#btn-submit').attr('disabled', 'true')">
                @csrf
                <!-- Form Group -->
                    <div class="form-group form-row">
                        <label class="col-xl-3 col-form-label text-sm-right" for="current_password">
                            Kata sandi sekarang
                        </label>

                        <div class="col-xl-9">
                            <input type="password" name="current_password" class="form-control" id="current_password"
                                   placeholder="Masukkan kata sandi sekarang...">

                            @error('current_password')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                    <!-- /form group -->

                    <!-- Form Group -->
                    <div class="form-group form-row">
                        <label class="col-xl-3 col-form-label text-sm-right" for="new_password">Kata sandi baru</label>

                        <div class="col-xl-9">
                            <input type="password" class="form-control" id="new_password" name="new_password"
                                   placeholder="Masukkan kata sandi baru..." minlength="4">
                            @error('new_password')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                    <!-- /form group -->

                    <!-- Form Group -->
                    <div class="form-group form-row">
                        <label class="col-xl-3 col-form-label text-sm-right" for="new_password_confirmation">Ulangi Kata
                            sandi baru</label>

                        <div class="col-xl-9">
                            <input type="password" class="form-control" id="new_password_confirmation" minlength="4"
                                   placeholder="Masukkan ulang kata sandi baru..." name="new_password_confirmation">
                            @error('new_password_confirmation')
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
