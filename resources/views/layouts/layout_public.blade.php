<?php
    use App\Models\BbkkpSis\PublicProfilPerusahaan;
    use App\Models\BbkkpSis\PublicLembaga;
    use App\Models\BbkkpSis\PublicSop;
    use App\Models\BbkkpSis\PublicSocialMedia;

    $company       = PublicProfilPerusahaan::first();
    $lembaga_rows  = PublicLembaga::all();
    $sop_rows      = PublicSop::all();
    $socmed_rows   = PublicSocialMedia::all();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Sistem Informasi Sertifikasi Balai Besar Kulit dan Karet">
    <meta name="keywords" content="{{env('APP_NAME')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{env('APP_NAME')}}</title>

    <!-- Site favicon -->
    <link rel="shortcut icon" href="{{ asset('images/icon/favicon-16x16-manifest-31222.png') }}">
    <!-- /site favicon -->

    <link href="{{ asset('assets/landing/css/bootstrap.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/landing/css/landing-page.css') }}" rel="stylesheet"/>

    <!--     Fonts and icons     -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400,300' rel='stylesheet' type='text/css'>
    <link href="{{ asset('assets/landing/css/pe-icon-7-stroke.css') }}" rel="stylesheet" />

    <style>
        /*.table-responsive {*/
        /*    overflow-x: hidden !important;*/
        /*}*/

        .dt-side-nav__header {
            padding-top: 0 !important;
        }

        /*.dt-side-nav__item.open>a{*/
        /*    color: #fa8c16;*/
        /*}*/

        @media screen and (max-width: 991px) {
            .dt-side-nav__header {
                padding-top: 20px !important;
            }
        }

        .custom-cooltipz {
            --cooltipz-font-size: 10px;
        }
    </style>

    @stack('css')
</head>

<body class="landing-page landing-page1">
    <nav class="navbar navbar-transparent navbar-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button id="menu-toggle" type="button" class="navbar-toggle" data-toggle="collapse" data-target="#example">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar bar1"></span>
                <span class="icon-bar bar2"></span>
                <span class="icon-bar bar3"></span>
                </button>
                <a href="#">
                    <div class="logo-container">
                        <div class="">
                            <img height="80" width="auto" style="margin-bottom: 10px; margin-left: 10px;" src="{{$company?->profil_app_icon ?? asset('images/logos/sis_logo.png')}}" alt="Logo {{env('app_name')}}">
                        </div>
                    </div>
                </a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="example" >
                <ul class="nav navbar-nav navbar-right">
                    @if($sop_rows->count() > 0)
                        <li>
                            <a href="#sop">
                                <i class="fa fa-book"></i>&nbsp;&nbsp;SOP Sertifikasi
                            </a>
                        </li>
                    @endif
                    @if(auth()->check())
                        <li>
                            <a href="{{ url('dashboard') }}">
                                <i class="fa fa-sign-in"></i>&nbsp;&nbsp;Dashboard
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ url('auth/register') }}">
                                <i class="fa fa-user-plus"></i>&nbsp;&nbsp;Daftar
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('auth/login') }}">
                                <i class="fa fa-sign-in"></i>&nbsp;&nbsp;Login
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
    </nav>
    <div class="wrapper" id="app">
        <div class="parallax filter-gradient orange" data-color="orange" style="height: 100vh;">
            @if($company?->profil_background_image)
                <div class="parallax-background">
                    <img src="{{$company?->profil_background_image}}" class="parallax-background-image">
                </div>
            @endif
            <div class= "container">
                <div class="row">
                    <div class="col-md-3 col-xs-12">
                        <div class="parallax-image">
                            <!-- <img src="assets/img/semarang_474.png" style="width: 20vw;"/> -->
                            <img src="{{$company?->profil_app_icon ?? asset('images/logos/sis_logo.png')}}" draggable="false" style="width: 18vw; background: url('{{$company?->profil_app_icon ?? asset('images/logos/sis_logo.png')}}') center bottom no-repeat; background-size: cover; background-position-x: 1px;"/>
                        </div>
                    </div>
                    <div class="col-md-8 col-md-offset-1">
                        <div class="description" style="margin-top: 150px;">
                            <h2>
                                {{$company?->profil_fullname_app}}&nbsp;
                                {{$company?->profil_shortname_app ? '('.$company?->profil_shortname_app.')' : ''}}
                            </h2>
                            <h5>
                                {{$company?->profil_fullname_perusahaan}}&nbsp;
                                {{$company?->profil_shortname_perusahaan ? '('.$company?->profil_shortname_perusahaan.')' : ''}}
                            </h5>
                            <br>
                            @if($lembaga_rows->count() > 0)
                            <h5>BBKKP memiliki {{$lembaga_rows->count()}} lembaga sertifikasi, yaitu:</h5>
                                <h5>
                                    <ol>
                                        @foreach($lembaga_rows as $row)
                                            <li>
                                                <a href="#lembaga_{{$row?->lem_id}}" style="color: white;">
                                                    {{$row?->lem_name}}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ol>
                                </h5>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php($last_section_bg_cls = 'section-gray')

        @if($lembaga_rows->count() > 0)
            @foreach($lembaga_rows as $i => $row)
                <section id="lembaga_{{$row?->lem_id}}" class="section {{$last_section_bg_cls}} section-clients">
                    <div class="container">
                        <h4 class="header-text" style="font-weight: 500;">{{$row?->lem_name}}</h4>
                        <p>{{$row?->lem_desc}}</p>
                        {!! $row?->lem_content !!}

                        @if($row?->lem_external_link)
                            <div style="margin-top: 16px;" class="w-100 text-right">
                                <a href="{{$row?->lem_external_link}}" rel="noopener noreferrer" target="_blank">
                                    <h6>Lihat selengkapnya&nbsp;<i class="fa fa-arrow-right"></i></h6>
                                </a>
                            </div>
                        @endif
                    </div>
                </section>
                @php($last_section_bg_cls = $i % 2 ? 'section-gray' : '')
            @endforeach
        @endif

        @if($company?->profil_ketidakperpihakan_file)
            <div class="section {{$last_section_bg_cls}} section-clients">
                <div class="container text-center">
                    <h5>Pernyataan Ketidakberpihakan Lembaga Sertifikasi BBKKP</h5>
                    <a href="{{$company?->profil_ketidakperpihakan_file}}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                        <i class="fa fa-download"></i>&nbsp;Download
                    </a>
                </div>
            </div>
            @php($last_section_bg_cls = $last_section_bg_cls == '' ? 'section-gray' : '')
        @endif

        @if($sop_rows->count() > 0)
            <section id="sop" class="section {{$last_section_bg_cls}} section-presentation">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="description text-left">
                                <h4 class="header-text" style="font-weight: 500;">
                                    <i class="fa fa-book"></i>&nbsp;&nbsp;SOP Sertifikasi
                                </h4>
                                <hr>
                                @foreach($sop_rows as $row)
                                    <h6>{{$row?->sop_name}}</h6>
                                    @if($row?->sop_desc)
                                        <p style="margin-top: 0;">
                                            {{$row?->sop_desc}}
                                        </p>
                                    @endif
                                    @if($row?->sop_image)
                                        <a href="#gambar_sop_{{$row?->sop_id}}">Lihat gambar 1.{{$row?->sop_id}}</a>
                                    @endif
                                    <hr>
                                @endforeach
                                <div class="row">
                                    @foreach($sop_rows as $row)
                                        @if($row?->sop_image)
                                            <div class="col-md-4 text-center">
                                                <section id="gambar_11">
                                                    <span class="text-primary">Gambar 1.{{$row?->sop_id}}</span>
                                                    <br>
                                                    <br>
                                                    <div class="w-100 text-center">
                                                        <a target="_blank" rel="noopener noreferrer" href="{{$row?->sop_image}}">
                                                            <img alt="" src="{{$row?->sop_image}}" draggable="false" style="width: 20vh;"/>
                                                        </a>
                                                    </div>
                                                </section>
                                                <hr>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <div class="section section-clients" style="padding-top: 20px; padding-bottom: 20px;">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <div>
                            <i class="fa fa-building"></i>&nbsp;
                            {{$company?->profil_alamat_perusahaan}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <ul style="list-style-type: none;">
                            @if($company?->profil_email_perusahaan)
                                <li style="margin-bottom: 8px;">
                                    <i class="fa fa-envelope"></i>&nbsp;
                                    {{$company?->profil_email_perusahaan}}
                                </li>
                            @endif
                            @if($company?->profil_telp_perusahaan)
                                <li style="margin-bottom: 8px;">
                                    <i class="fa fa-phone"></i>&nbsp;
                                    {{$company?->profil_telp_perusahaan}}
                                </li>
                            @endif
                            @if($company?->profil_fax_perusahaan)
                                <li style="margin-bottom: 8px;">
                                    <i class="fa fa-print"></i>&nbsp;
                                    {{$company?->profil_fax_perusahaan}}
                                </li>
                            @endif
                            @if($company?->profil_whatsapp_perusahaan)
                                <li style="margin-bottom: 8px;">
                                    <i class="fab fa-whatsapp"></i>&nbsp;
                                    {{$company?->profil_whatsapp_perusahaan}}
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-sm-12">
                        <hr>
                    </div>
                    <div class="col-sm-8">
                        © Hak Cipta {{$company?->profil_fullname_perusahaan}} {{ date('Y') }}
                    </div>
                    @if($socmed_rows->count() > 0)
                        <div class="col-sm-4 text-right">
                            @foreach($socmed_rows as $row)
                                <a target="_blank" rel="noopener noreferrer" style="font-size: 24px; margin-left: 8px;" href="{{$row?->socmed_link}}">
                                    <i class="{{$row?->socmed_icon_cls}}"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</body>

<script src="{{ asset('assets/landing/js/jquery-1.10.2.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/landing/js/jquery-ui-1.10.4.custom.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/landing/js/bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/landing/js/awesome-landing-page.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/landing/js/jquery-3.3.1.min.js') }}" type="text/javascript"></script>
@stack('javascript')

</html>
