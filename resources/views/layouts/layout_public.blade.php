<?php

use App\Models\BbkkpSis\PublicProfilPerusahaan;
use App\Models\BbkkpSis\PublicSop;
use App\Models\BbkkpSis\PublicSocialMedia;

$company     = PublicProfilPerusahaan::first();
$sop_rows    = PublicSop::all();
$socmed_rows = PublicSocialMedia::all();
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

    <link href="{{ asset('assets/landing/css/bootstrap.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/landing/css/landing-page.css') }}" rel="stylesheet"/>

    <!--     Fonts and icons     -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400,300' rel='stylesheet' type='text/css'>
    <link href="{{ asset('assets/landing/css/pe-icon-7-stroke.css') }}" rel="stylesheet"/>

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
                        <img height="80" width="auto" style="margin-bottom: 10px; margin-left: 10px;"
                             src="{{$company?->profil_app_icon ?? asset('images/logos/sis_logo.png')}}"
                             alt="Logo {{env('app_name')}}">
                    </div>
                </div>
            </a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="example">
            <ul class="nav navbar-nav navbar-right">
                @if($sop_rows->count() > 0)
                    <li>
                        <a href="#sop">
                            <i class="fa fa-book"></i>&nbsp;&nbsp;SOP Sertifikasi
                        </a>
                    </li>
                @endif
                <li>
                    <a href="{{ url('track/certificate') }}">
                        <i class="fa fa-file-alt"></i>&nbsp;&nbsp;Verifikasi Sertifikat
                    </a>
                </li>
                @if(auth()->check())
                    <li>
                        <a href="{{ url('dashboard') }}">
                            <i class="fa fa-sign-in"></i>&nbsp;&nbsp;Dashboard
                        </a>
                    </li>
                @else
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
    @yield('content')
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
                            <a target="_blank" rel="noopener noreferrer" style="font-size: 24px; margin-left: 8px;"
                               href="{{$row?->socmed_link}}">
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
