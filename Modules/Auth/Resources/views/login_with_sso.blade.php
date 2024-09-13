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
                <div style="margin-top:25px">
                    <div class="form-group">
                        <a href="{{url('/auth/sso/login')}}" id="btn-submit"
                           class="btn btn-primary btn-block text-uppercase">
                            Login via SSO
                        </a>
                    </div>
                    <div style="text-align: center; padding-top: 5px; padding-bottom: 30px">
                        Jika anda belum memiliki akun SSO BBKKP
                    </div>
                    <div class="form-group">
                        <a href="{{url(config('app.sso.server') . '/account/register')}}" id="btn-submit"
                           class="btn btn-primary btn-block text-uppercase">
                            Register SSO
                        </a>
                    </div>
                </div>


                <div class="pb-5"></div>
                <hr>
                <span class="text-light-gray">
                        Semua pendaftaran terpusat pada laman <b>{{ config('app.sso.server') }}</b>
                    </span>

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
