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
                <h1 class="dt-login__title">Akun Belum Aktif</h1>
                <!-- /login title -->
                <p class="f-16">Kirim ulang kode verifikasi untuk mengaktifkan akun anda</p>
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
                <form action="{{route('auth.resend_validation')}}" method="post"
                      onsubmit="$('#btn-submit').attr('disabled', 'true')">
                @csrf


                <!-- Form Group -->
                    <div class="form-group">
                        <button type="submit" id="btn-submit" class="btn btn-primary btn-block text-uppercase">
                            Kirim Ulang Link Verifikasi
                        </button>
                    </div>
                    <!-- /form group -->


                    <div style="text-align: center">
                        <span>atau klik disini untuk keluar</span>
                    </div>
                    <div class="pb-5"></div>
                    <a href="{{route('auth.logout')}}" class="btn btn-danger btn-block text-uppercase">Logout</a>
                </form>
                <!-- /form -->
            </div>
            <!-- /login content inner -->
        </div>
        <!-- /login content section -->

    </div>
@endsection
