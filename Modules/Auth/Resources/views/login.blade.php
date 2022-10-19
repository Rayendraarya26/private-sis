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
                <h1 class="dt-login__title">Login SIS</h1>
                <!-- /login title -->
                <p class="f-16">Balai Besar Kulit, Karet dan Plastik.</p>
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
                <form action="{{route('auth.processLogin')}}" method="post"
                      onsubmit="$('#btn-submit').attr('disabled', 'true')">
                    @csrf
                    {{--Email--}}
                    <!-- Form Group -->
                    <div class="input-group input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm">
                                <i class="fal fa-envelope"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" aria-label="Small" name="email" value="{{old('email')}}"
                               placeholder="Masukkan surel (email)" required>
                    </div>
                    @error('email')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                    <!-- /form group -->

                    <!-- Form Group -->
                    {{--Password--}}
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm">
                                <i class="fal fa-lock-alt"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control" aria-label="password"
                               placeholder="Masukkan kata sandi (password)" id="password"
                               name="password" required>
                        <div class="input-group-append" style="cursor:pointer;">
                            <span class="input-group-text" id="eyeOpen" onclick="passShow()">
                                <i class="fal fa-eye-slash"></i>
                            </span>
                            <span class="input-group-text" id="eyeClose" onclick="passHide()" style="display: none">
                                <i class="fal fa-eye"></i>
                            </span>
                        </div>


                    </div>
                    @error('password')
                    <span class="text-danger">{{$message}}</span>
                    @enderror

                    <!-- /form group -->

                    <!-- Form Group -->
                    <div class="form-group">
                        <button type="submit" id="btn-submit" class="btn btn-primary btn-block text-uppercase">Login
                        </button>
                    </div>
                    <!-- /form group -->
                    @if(\Illuminate\Support\Facades\Route::has("auth.google"))
                        <div style="text-align: center; font-weight: bold;font-size: 16px">
                            atau
                        </div>
                        <div class="form-group pt-4">
                            <a href="{{route('auth.google')}}" class="btn btn-outline-primary btn-block">
                                <i class="fab fa-google"></i> Login dengan Google
                            </a>
                        </div>
                    @endif

                    <div class="pb-2" style="float: right">
                        <a href="{{route('auth.forget_password')}}" class="text-light-gray">Lupa kata sandi ?</a>
                    </div>

                    <div class="pb-10"></div>
                    <div class="pb-10"></div>
                    <hr>
                    <span class="text-light-gray"> Klik disini untuk melakukan
                        <a href="{{route('auth.register')}}">Registrasi</a>
                    </span>
                </form>
                <!-- /form -->
            </div>
            <!-- /login content inner -->
        </div>
        <!-- /login content section -->

    </div>
@endsection

@push('javascript')
    <script>
        function passShow() {
            $("#password").attr('type', 'text')
            $("#eyeOpen").attr('style', 'display:none;')
            $("#eyeClose").attr('style', 'display:;')
            console.log('show password');
        }

        function passHide() {
            $("#password").attr('type', 'password')
            $("#eyeOpen").attr('style', 'display:;;')
            $("#eyeClose").attr('style', 'display:none')
            console.log('hide password');
        }
    </script>
@endpush
