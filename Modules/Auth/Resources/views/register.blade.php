@extends("layouts.layout_auth")

@section('title', "Register")

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
                <h1 class="dt-login__title">Register SIS</h1>
                <!-- /login title -->
                <p class="f-16">Form pendaftaran Sistem Informasi Sertifikat Balai Besar Kulit, Karet dan Plastik</p>
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

                @error('status')
                <div class="alert alert-danger">
                    {{$message}}
                </div>
            @enderror
            <!-- Form -->
                <form action="{{route('auth.processRegister')}}" method="post"
                      onsubmit="$('#btn-submit').attr('disabled', 'true')">
                    @csrf

                    {{--Fullname--}}
                    <div class="input-group input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm">
                                <i class="fal fa-user"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" aria-label="Small"
                               aria-describedby="inputGroup-sizing-sm" name="fullname"
                               value="{{old('fullname')}}" required
                               placeholder="Masukkan nama Lengkap...">
                    </div>
                    @error('fullname')
                    <span class="text-danger">{{$message}}</span>
                    @enderror

                    {{--Email--}}
                    <div class="input-group input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm">
                                <i class="fal fa-envelope"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" aria-label="Small"
                               aria-describedby="inputGroup-sizing-sm" name="email"
                               value="{{old('email')}}" required
                               placeholder="Masukkan surel (email)...">
                    </div>
                    @error('email')
                    <span class="text-danger">{{$message}}</span>
                    @enderror

                    {{--Password--}}
                    <div class="input-group
                    input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm">
                                <i class="fal fa-lock-alt"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control" aria-label="password" required
                               placeholder="Masukkan kata sandi (password)..." name="password">
                    </div>
                    @error('password')
                    <span class="text-danger">{{$message}}</span>
                    @enderror

                    {{--Konfirmasi Password--}}
                    <div class="input-group input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm">
                                <i class="fal fa-lock-alt"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control" aria-label="password_confirmation" required
                               placeholder="Masukkan ulang kata sandi..." name="password_confirmation">
                    </div>
                    @error('password_confirmation')
                    <span class="text-danger">{{$message}}</span>
                @enderror

                <!-- /form group -->

                    <!-- Form Group -->
                    <div class="form-group">
                        <button type="submit" id="btn-submit" class="btn btn-primary btn-block text-uppercase">Daftar
                        </button>
                    </div>
                    <!-- /form group -->

                    <div class="pb-10"></div>
                    <hr>
                    <span class="text-light-gray"> Sudah punya akun ?
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
