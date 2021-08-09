<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistem Informasi Sertifikasi Balai Besar Kulit dan Karet">
    <meta name="keywords" content="{{env('APP_NAME')}}">
    <!-- /meta tags -->
    <title>@yield('title') | {{env('APP_NAME')}}</title>

    <!-- Site favicon -->
    <link rel="shortcut icon" href="{{asset('images/icon/favicon-32x32-manifest-31222.png')}}" type="image/x-icon">
    <!-- /site favicon -->

    <!-- Font Icon Styles -->
    <link rel="stylesheet" href="{{asset('node_modules/flag-icon-css/css/flag-icon.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/gaxon-icon/style.css')}}">
    <!-- /font icon Styles -->

    <!-- Perfect Scrollbar stylesheet -->
    <link rel="stylesheet" href="{{asset('node_modules/perfect-scrollbar/css/perfect-scrollbar.css')}}">
    <!-- /perfect scrollbar stylesheet -->

    <!-- Load Styles -->

    <link rel="stylesheet" href="{{asset('assets/css/lite-style-1.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fontawesome/css/all.min.css')}}">
    <!-- /load styles -->

    @stack("css")
</head>
<body class="dt-sidebar--fixed dt-header--fixed">

<!-- Loader -->
<div class="dt-loader-container">
    <div class="dt-loader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"></circle>
        </svg>
    </div>
</div>
<!-- /loader -->

<!-- Root -->
<div class="dt-root">
    <!-- Login Container -->
    <div class="dt-login--container dt-app-login--container">
        <!-- Login Content -->
        @yield('content')
        <!-- /login content -->
    </div>
    <!-- /login container -->
</div>
<!-- /root -->

<!-- Optional JavaScript -->
<script src="{{asset('node_modules/jquery/dist/jquery.min.js')}}"></script>
<script src="{{asset('node_modules/moment/moment.js')}}"></script>
<script src="{{asset('node_modules/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
<!-- Perfect Scrollbar jQuery -->
<script src="{{asset('node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js')}}"></script>
<!-- /perfect scrollbar jQuery -->

<!-- masonry script -->
<script src="{{asset('node_modules/masonry-layout/dist/masonry.pkgd.min.js')}}"></script>
<script src="{{asset('node_modules/sweetalert2/dist/sweetalert2.js')}}"></script>

<!-- Custom JavaScript -->
<script src="{{asset('assets/js/script.js')}}"></script>
<script src="{{asset('assets/fontawesome/js/all.min.js')}}"></script>

{{--<script src="https://kit.fontawesome.com/68c3e4b5b2.js"></script>--}}
@stack('javascript')
</body>
</html>
