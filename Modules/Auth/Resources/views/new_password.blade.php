@extends("layouts.layout_auth")

@section('title', "Login")

@push('css')
    <style>
        .img-logo {
            width: 100%;
            height: auto;
        }

        @media screen and (max-width: 995px) {
            .img-logo {
                width: 100px;
                height: auto;
            }
        }
    </style>
@endpush

@section('content')
    <div class="dt-login__content-wrapper">
        <!-- Login Background Section -->
        <div class="dt-login__bg-section"
             style="background: linear-gradient(190deg, #00416a, #799f0c, #ffe000) !important;"
        >

            <div class="dt-login__bg-content" style="padding: 0 15px 0 15px">
                <!-- Login Title -->
                <h1 class="dt-login__title">Perbarui Kata Sandi</h1>
                <!-- /login title -->
                <p class="f-16">Pastikan kata sandi kamu mengandung kombinasi karakter dan angka</p>
            </div>
            <!-- Brand logo -->
            <div class="dt-login__logo" style="padding: 0 15px 0 15px">
                <div class="gx-app-logo">
                    <img class="img-logo" alt="logo" draggable="false"
                         src="{{asset('images/logos/sis_logo.png')}}">
                </div>
            </div>

            <!-- /brand logo -->
        </div>
        <!-- /login background section -->

        <!-- Login Content Section -->
        <div class="dt-login__content">
            <!-- Login Content Inner -->
            <div class="dt-login__content-inner">
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
                <div class="alert alert-info">
                    Mengubah kata sandi pada akun {{$data->user_email}}
                </div>
                <form action="{{route('auth.new_password')}}" method="post"
                      onsubmit="$('#btn-submit').attr('disabled', 'true')">
                    @csrf
                    <input type="hidden" value="{{$data->user_email}}" name="email">
                    {{--Email--}}
                <!-- Form Group -->
                    <div class="input-group input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm">
                                <i class="icon icon-email"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control" aria-label="Small" name="password"
                               value="{{old('password')}}"
                               placeholder="Masukkan kata sandi baru" required>
                    </div>
                    @error('password')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                <!-- /form group -->

                    {{--Email Konfirmasi--}}
                <!-- Form Group -->
                    <div class="input-group input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm">
                                <i class="icon icon-email"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control" aria-label="Small" name="password_confirmation"
                               value="{{old('password_confirmation')}}"
                               placeholder="Masukkan ulang kata sandi baru" required>
                    </div>
                    @error('password_confirmation')
                    <span class="text-danger">{{$message}}</span>
                @enderror
                <!-- /form group -->

                    <!-- Form Group -->
                    <div class="form-group">
                        <button type="submit" id="btn-submit" class="btn btn-primary btn-block text-uppercase">Update
                        </button>
                    </div>
                    <!-- /form group -->

                    <div class="pb-10"></div>
                    <div class="pb-10"></div>
                    <div class="pb-10"></div>
                    <div class="pb-10"></div>
                    <hr>
                    <span class="text-light-gray"> Klik disini untuk melakukan
                        <a href="{{route('auth.login')}}">Login</a>
                    </span>
                </form>
                <!-- /form -->
            </div>
            <!-- /login content inner -->
        </div>
        <!-- /login content section -->

    </div>
@endsection
