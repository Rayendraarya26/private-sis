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

    <!-- Font Icon Styles -->
    <link rel="stylesheet" href="{{ asset('/node_modules/flag-icon-css/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendors/gaxon-icon/style.css') }}">
    <!-- /font icon Styles -->

    <!-- Perfect Scrollbar stylesheet -->
    <link rel="stylesheet" href="{{ asset('/node_modules/perfect-scrollbar/css/perfect-scrollbar.css') }}">
    <!-- /perfect scrollbar stylesheet -->

    <!-- Load Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/lite-style-1.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/plugins/easyui/themes/material/easyui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/cooltipz/cooltipz.min.css') }}">

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


<body class="dt-sidebar--fixed dt-header--fixed">
<!-- Loader -->
<div class="dt-loader-container">
    <div class="dt-loader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10">
            </circle>
        </svg>
    </div>
</div>
<!-- /loader -->

<!-- Root -->
<div class="dt-root">

    <!-- Header -->
    <header class="dt-header">
        <!-- Header container -->
        <div class="dt-header__container">
            <!-- Brand -->
            <div class="dt-brand">

                <!-- Brand tool -->
                <div class="dt-brand__tool" data-toggle="main-sidebar">
                    <i class="icon icon-xl icon-menu-fold d-none d-lg-inline-block"></i>
                    <i class="icon icon-xl icon-menu d-lg-none"></i>
                </div>
                <!-- /brand tool -->

                <!-- Brand logo -->
                <span class="dt-brand__logo">
                        <a class="dt-brand__logo-link" href="{{ route('dashboard') }}">
                            <img class="dt-brand__logo-img d-none d-lg-inline-block"
                                 src="{{asset('images/logos/sis_logo.png')}}"
                                 alt="Logo {{env('app_name')}}">
                            <img class="dt-brand__logo-symbol d-lg-none"
                                 src="{{asset('images/logos/sis_logo.png')}}"
                                 alt="Logo {{env('app_name')}}" style="width: 70px">
                        </a>
                    </span>
                <!-- /brand logo -->

            </div>
            <!-- /brand -->

            <!-- Header toolbar-->
            <div class="dt-header__toolbar">
                <!-- Search box -->
                <form class="search-box d-none d-lg-block">
                    <div class="pt-4"></div>
                    <h2>@yield('title')</h2>
                </form>
                <!-- /search box -->

                <!-- Header Menu Wrapper -->
                <div class="dt-nav-wrapper">
                    <ul class="dt-nav">
                        @php
                            $notif = \App\Models\BbkkpSis\SysUserNotif::where("notif_user_id", auth()->id())->orderBy('notif_is_read', 'desc')->orderBy('notif_created_at', 'desc')->take(10)->get();
                            $total = \App\Models\BbkkpSis\SysUserNotif::where("notif_user_id", auth()->id())->where("notif_is_read", 'no')->select(\Illuminate\Support\Facades\DB::RAW("count(*) total"))->first();
                        @endphp
                        <li class="dt-nav__item dt-notification dropdown">
                            <!-- Dropdown Link -->
                            <a href="#" class="dt-nav__link dropdown-toggle no-arrow" data-toggle="dropdown"
                               aria-haspopup="true" aria-expanded="false">
                                <i class="icon icon-notification icon-fw {{$total->total > 0 ? 'dt-icon-alert' : ''}}"></i>
                            </a>
                            <!-- /dropdown link -->

                            <!-- Dropdown Option -->
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-media">
                                <!-- Dropdown Menu Header -->
                                <div class="dropdown-menu-header">
                                    <h4 class="title">Notifikasi @if($total->total) {{$total->total}}) @endif</h4>

                                    @if($total->total)
                                        <div class="ml-auto action-area">
                                            <a href="{{url('notification/mark-all-as-read')}}">Baca Semua</a>
                                        </div>
                                    @endif
                                </div>
                                <!-- /dropdown menu header -->

                                <!-- Dropdown Menu Body -->
                                <div class="dropdown-menu-body ps-custom-scrollbar">
                                    <div class="h-auto">
                                    @foreach($notif as $n)
                                        <!-- Media -->
                                            <a href="{{url('notification/open/'.$n->notif_id)}}" class="media">
                                                @if($n->notif_is_read == "yes")
                                                    <i class="fal fa-bell mr-3 fa-2x" style='color: grey'></i>
                                                @else
                                                    <i class="fas fa-bell mr-3 fa-2x"></i>
                                            @endif

                                            <!-- avatar -->

                                                <!-- Media Body -->
                                                <span class="media-body">
                                                <span class="message">
                                                    <span class="user-name">{{$n->notif_title}}</span>
                                                    <br>
                                                    {{$n->notif_content}}
                                                </span>
                                                <span class="meta-date">{{$n->notif_created_at->diffForHumans()}}</span>
                                            </span>
                                                <!-- /media body -->
                                            </a>
                                            <!-- /media -->
                                        @endforeach
                                    </div>

                                </div>
                                <!-- /dropdown menu body -->

                                <!-- Dropdown Menu Footer -->
                                <div class="dropdown-menu-footer">
                                    <a href="{{route('notification')}}" class="card-link"> Selengkapnya
                                        <i class="fal fa-arrow-right"></i>
                                    </a>
                                </div>
                                <!-- /dropdown menu footer -->
                            </div>
                            <!-- /dropdown option -->
                        </li>
                    </ul>
                    <!-- /header menu -->

                    <!-- Header Menu -->
                    <ul class="dt-nav">
                        <li class="dt-nav__item dropdown">

                            <!-- Dropdown Link -->
                            <a href="#" class="dt-nav__link dropdown-toggle no-arrow dt-avatar-wrapper"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="dt-avatar size-40" src="{{ auth()->user()->getImage() }}"
                                     alt="{{ auth()->user()->user_fullname }}">
                            </a>
                            <!-- /dropdown link -->

                            <!-- Dropdown Option -->
                            <div class="dropdown-menu dropdown-menu-right" style="width: 200px">
                                <div
                                    class="dt-avatar-wrapper flex-nowrap p-6 mt--5 bg-gradient-purple text-white rounded-top">
                                    <img class="dt-avatar" src="{{ auth()->user()->getImage() }}" alt="Domnic Harris">
                                    <span class="dt-avatar-info">
                                          <span class="dt-avatar-name">{{ auth()->user()->user_fullname }}</span>
                                          <span class="f-12">{{ ucwords(session('group_selected_name')) }}</span>
                                        </span>
                                </div>
                                @if(count(session('group_available')) > 1)
                                    <a class="dropdown-item" href="javascript:void(0)"
                                       onclick="$('#modalSwitchRole').modal('show')">
                                        <i class="far fa-exchange-alt"></i> Switch Role
                                    </a>
                                @endif
                                <a class="dropdown-item" href="{{route('profile')}}">
                                    <i class="icon far fa-user"></i> Profile
                                </a>
                                <a class="dropdown-item" href="{{ route('auth.logout') }}">
                                    <i class="icon far fa-sign-out-alt"></i> Logout
                                </a>
                            </div>

                            <!-- /dropdown option -->
                        </li>
                    </ul>
                    <!-- /header menu -->
                </div>
                <!-- Header Menu Wrapper -->
            </div>
            <!-- /header toolbar -->
        </div>
        <!-- /header container -->
    </header>
    <!-- /header -->

    <!-- Site Main -->
    <main class="dt-main">
        <!-- Sidebar -->
        <aside id="main-sidebar" class="dt-sidebar">
            <div class="dt-sidebar__container">

                <!-- Sidebar Notification -->
            {{--                <div class="dt-sidebar__notification  d-none d-lg-block">--}}
            {{--                    <!-- Dropdown -->--}}
            {{--                    <div class="dropdown mb-6" id="user-menu-dropdown">--}}

            {{--                        <!-- Dropdown Link -->--}}
            {{--                        <a href="#" class="dropdown-toggle dt-avatar-wrapper text-body" data-toggle="dropdown"--}}
            {{--                           aria-haspopup="true" aria-expanded="false">--}}
            {{--                            <img class="dt-avatar" src="{{ auth()->user()->getImage() }}" alt="Domnic Harris">--}}
            {{--                            <span class="dt-avatar-info"><span class="dt-avatar-name">--}}
            {{--                                        {{ auth()->user()->user_fullname }}--}}
            {{--                                    </span></span>--}}
            {{--                        </a>--}}
            {{--                        <!-- /dropdown link -->--}}

            {{--                        <!-- Dropdown Option -->--}}
            {{--                        <div class="dropdown-menu dropdown-menu-right">--}}
            {{--                            <div--}}
            {{--                                class="dt-avatar-wrapper flex-nowrap p-6 mt--5 bg-gradient-purple text-white rounded-top">--}}
            {{--                                <img class="dt-avatar" src="{{ auth()->user()->getImage() }}"--}}
            {{--                                     alt="Domnic Harris">--}}
            {{--                                <span class="dt-avatar-info">--}}
            {{--                                        <span class="dt-avatar-name">{{ auth()->user()->user_fullname }}</span>--}}
            {{--                                        <span class="f-12">{{ ucwords(session('group_selected_name')) }}</span>--}}
            {{--                                    </span>--}}
            {{--                            </div>--}}
            {{--                            @if(count(session('group_available')) > 1)--}}
            {{--                                <a class="dropdown-item" href="javascript:void(0)"--}}
            {{--                                   onclick="$('#modalSwitchRole').modal('show')">--}}
            {{--                                    <i class="fas fa-exchange-alt"></i> Switch Role--}}
            {{--                                </a>--}}
            {{--                            @endif--}}
            {{--                            <a class="dropdown-item" href="javascript:void(0)">--}}
            {{--                                <i class="icon fas fa-user"></i> Profile--}}
            {{--                            </a>--}}
            {{--                            <a class="dropdown-item" href="{{ route('auth.logout') }}">--}}
            {{--                                <i class="icon fas fa-sign-out-alt"></i> Logout--}}
            {{--                            </a>--}}
            {{--                        </div>--}}
            {{--                        <!-- /dropdown option -->--}}
            {{--                    </div>--}}
            {{--                    <!-- /dropdown -->--}}
            {{--                </div>--}}
            <!-- /sidebar notification -->

                <!-- Sidebar Navigation -->
                <ul class="dt-side-nav">


                    <!-- Menu Header -->
                    <li class="dt-side-nav__item dt-side-nav__header">
                        <span class="dt-side-nav__text">Menu</span>
                    </li>
                    <!-- /menu header -->

                    <!-- Menu Item -->
                    <li class="dt-side-nav__item">
                        <a href="{{ route('dashboard') }}" class="dt-side-nav__link" title="Dashboard">
                            <i class="icon fas fa-tachometer-alt"></i>
                            <span class="dt-side-nav__text">Dashboard</span> </a>
                    </li>
                    <!-- /menu item -->


                    <!-- Menu Item -->
                    @foreach(session('menu') as $menu)
                        <li class="dt-side-nav__item">
                            <a href="{{ count($menu->children) ? 'javascript:void(0)' : action($menu->action_controller) }}"
                               class="dt-side-nav__link {{count($menu->children) ? 'dt-side-nav__arrow' :''}}"
                               title="{{$menu->menu_name}}">
                                <i class="icon {{$menu->menu_icon}}"></i>
                                <span class="dt-side-nav__text">{{$menu->menu_name}}</span>
                            </a>

                            @if(count($menu->children) > 0)
                                <ul class="dt-side-nav__sub-menu">
                                    @include('layouts.component.renderMenu', ['children' => $menu->children])
                                </ul>
                            @endif

                        </li>
                    @endforeach
                </ul>
                <!-- /sidebar navigation -->

            </div>
        </aside>
        <!-- /sidebar -->
        <!-- Site Content Wrapper -->
        <div class="dt-content-wrapper">
            <div class="d-lg-none">
                <div class="dt-content" style="margin-bottom: -15px">
                    <div style="text-align: center">
                        <h2>@yield('title')</h2>
                    </div>
                </div>
            </div>
        @yield('content')

        <!-- Footer -->
            <footer class="dt-footer">
                © Hak Cipta Balai Besar Kulit dan Karet {{ date('Y') }}
            </footer>
            <!-- /footer -->
        </div>
        <!-- /site content wrapper -->

        <!-- Theme Chooser -->
    {{--        <div class="dt-customizer-toggle">--}}
    {{--            <a href="javascript:void(0)" data-toggle="customizer"> <i class="icon icon-spin icon-setting"></i> </a>--}}
    {{--        </div>--}}
    <!-- /theme chooser -->

        <!-- Customizer Sidebar -->
        <aside class="dt-customizer dt-drawer position-right">
            <div class="dt-customizer__inner">

                <!-- Customizer Header -->
                <div class="dt-customizer__header">

                    <!-- Customizer Title -->
                    <div class="dt-customizer__title">
                        <h3 class="mb-0">Theme Settings</h3>
                    </div>
                    <!-- /customizer title -->

                    <!-- Close Button -->
                    <button type="button" class="close" data-toggle="customizer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <!-- /close button -->

                </div>
                <!-- /customizer header -->

                <!-- Customizer Body -->
                <div class="dt-customizer__body ps-custom-scrollbar">
                    <!-- Customizer Body Inner  -->
                    <div class="dt-customizer__body-inner">

                        <!-- Section -->
                        <section id="theme-chooser">
                            <h6 class="text-uppercase">Theme</h6>

                            <!-- Button Group -->
                            <div class="dt-customizer__btn-group btn-group btn-group-toggle btn-group mb-1"
                                 data-toggle="buttons"><label class="btn btn-outline-light"><input
                                        class="theme-option" type="radio" name="options" id="theme-option-lite"
                                        value="lite">Lite</label>
                                <label class="btn btn-outline-light"><input class="theme-option" type="radio"
                                                                            name="options" id="theme-option-semidark"
                                                                            value="semidark">Semi Dark</label>
                                <label class="btn btn-outline-light"><input class="theme-option" type="radio"
                                                                            name="options" id="theme-option-dark"
                                                                            value="dark">Dark</label>
                            </div>
                            <!-- /button group -->

                        </section>
                        <!-- /section -->

                        <!-- Section -->
                        <section id="theme-style-chooser">
                            <h6 class="text-uppercase">Colors</h6>

                            <!-- List -->
                            <ul class="dt-list dt-list-sm dt-color-options">
                                <li class="dt-list__item">
                                    <span class="dt-color-option" data-style="style-1"></span>
                                </li>
                                <li class="dt-list__item">
                                    <span class="dt-color-option" data-style="style-2"></span>
                                </li>
                                <li class="dt-list__item">
                                    <span class="dt-color-option" data-style="style-3"></span>
                                </li>
                                <li class="dt-list__item">
                                    <span class="dt-color-option" data-style="style-4"></span>
                                </li>
                                <li class="dt-list__item">
                                    <span class="dt-color-option" data-style="style-5"></span>
                                </li>
                                <li class="dt-list__item">
                                    <span class="dt-color-option" data-style="style-6"></span>
                                </li>
                                <li class="dt-list__item">
                                    <span class="dt-color-option" data-style="style-7"></span>
                                </li>
                                <li class="dt-list__item">
                                    <span class="dt-color-option" data-style="style-8"></span>
                                </li>
                                <li class="dt-list__item">
                                    <span class="dt-color-option" data-style="style-9"></span>
                                </li>
                                <li class="dt-list__item">
                                    <span class="dt-color-option" data-style="style-10"></span>
                                </li>
                            </ul>
                            <!-- /list -->

                        </section>
                        <!-- /section -->

                        <!-- Section -->
                        <section id="layout-chooser">
                            <h6 class="text-uppercase">Layout</h6>

                            <!-- List -->
                            <ul class="dt-list dt-list-sm">
                                <li class="dt-list__item">
                                    <div class="choose-option">
                                        <a href="javascript:void(0)" class="choose-option__icon"
                                           data-layout="framed">
                                            <img src="{{ asset('assets/images/layouts/framed.png') }}"
                                                 alt="Framed">
                                        </a>
                                    </div>
                                </li>
                                <li class="dt-list__item">
                                    <div class="choose-option">
                                        <a href="javascript:void(0)" class="choose-option__icon"
                                           data-layout="full-width">
                                            <img src="{{ asset('assets/images/layouts/full-width.png') }}"
                                                 alt="Full Width">
                                        </a>
                                    </div>
                                </li>
                                <li class="dt-list__item">
                                    <div class="choose-option">
                                        <a href="javascript:void(0)" class="choose-option__icon"
                                           data-layout="boxed">
                                            <img src="{{ asset('assets/images/layouts/boxed.png') }}" alt="Boxed">
                                        </a>
                                    </div>
                                </li>
                            </ul>
                            <!-- /list -->

                        </section>
                        <!-- /section -->

                    </div>
                    <!-- /customizer body inner -->
                </div>
                <!-- /customizer body -->

            </div>
        </aside>
        <!-- /customizer sidebar -->
    </main>


@if(count(session('group_available')) > 1)
    <!-- Modal Switch Role-->
        <div class="modal fade" id="modalSwitchRole" tabindex="-1" role="dialog"
             aria-labelledby="modalSwitchRole" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">

                <!-- Modal Content -->
                <div class="modal-content">
                    <form action="{{route('auth.switch_role')}}" method="post">
                    @csrf
                    <!-- Modal Header -->
                        <div class="modal-header">
                            <h3 class="modal-title" id="modalSwitchRoleTitle">
                                Switch Role ({{ucwords(session('group_selected_name'))}})
                            </h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <!-- /modal header -->

                        <!-- Modal Body -->
                        <div class="modal-body">
                            <label for="modal_group_id">Pilih Role</label>


                            <select name="modal_group_id" id="modal_group_id" class="form form-control">
                                @foreach(session('group_available') as $group)
                                    <option
                                        value="{{$group['group_id']}}" {{$group['group_id'] == session('group_selected') ? 'selected' : ''}}>
                                        {{ucwords($group['group_name'])}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- /modal body -->

                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btn-sm">UBAH</button>
                        </div>
                        <!-- /modal footer -->
                    </form>
                </div>
                <!-- /modal content -->
            </div>
        </div>
    @endif
</div>
<!-- /root -->

<script src="{{ asset('/node_modules/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('/node_modules/moment/moment.js') }}"></script>
<script src="{{ asset('/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<!-- Perfect Scrollbar jQuery -->
<script src="{{ asset('/node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
<!-- /perfect scrollbar jQuery -->

<!-- masonry script -->
<script src="{{ asset('/node_modules/masonry-layout/dist/masonry.pkgd.min.js') }}"></script>
<script src="{{ asset('/node_modules/sweetalert2/dist/sweetalert2.js') }}"></script>

<script src="{{ asset('/node_modules/chart.js/dist/Chart.min.js') }}"></script>

<!-- Resources -->
<script src="{{ asset('/node_modules/ammap3/ammap/ammap.js') }}"></script>
<script src="{{ asset('/node_modules/ammap3/ammap/maps/js/continentsLow.js') }}"></script>
<script src="{{ asset('/node_modules/ammap3/ammap/themes/light.js') }}"></script>

<script src="{{ asset('/node_modules/amcharts3/amcharts/amcharts.js') }}"></script>
<script src="{{ asset('/node_modules/amcharts3/amcharts/gauge.js') }}"></script>

<!-- Custom JavaScript -->
<script src="{{ asset('assets/js/script.js') }}"></script>
<script src="{{ asset('assets/js/vue.min.js') }}"></script>
<script src="{{ asset('assets/js/dexie.min.js') }}"></script>

<script src="{{ asset('assets/fontawesome/js/all.min.js') }}"></script>

<script src="{{ asset('assets/plugins/easyui/jquery.easyui.min.js') }}"></script>
<script src="{{ asset('assets/plugins/easyui/datagrid-filter.js') }}"></script>
<script src="{{ asset('assets/plugins/easyui/datagrid-export.js') }}"></script>
<script src="{{ asset('assets/plugins/easyui/jquery.edatagrid.js') }}"></script>

<script>
    const toast = swal.mixin({
        toast: true,
        position: 'center',
        showConfirmButton: false,
        timer: 3000
    });

    const toastCenter = swal.mixin({
        toast: true,
        position: 'center',
        showConfirmButton: false,
        timer: 3000
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

<script src="https://www.gstatic.com/firebasejs/8.9.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.8.1/firebase-messaging.js"></script>

<script>
    // membuat string kapital diawal kata
    String.prototype.ucwords = function () {
        if (this === '') return "";
        let str = this.toLowerCase();
        return str.replace(/(^([a-zA-Z{M}]))|([ -][a-zA-Z{M}])/g,
            function ($1) {
                return $1.toUpperCase();
            });
    };

    // memformat uang
    String.prototype.formatUang = function (delimiter) {
        if (this === '') return "";
        let str = this.toString();
        delimiter = delimiter || " "
        return str.replace(/\B(?=(\d{3})+(?!\d))/g, delimiter);
    };

    function syncToken(token) {
        $.post(`{{url('notification/ajax/sync-token')}}`, {token})
            .then(response => {
                console.log(response)
            });
    }

    async function initIDB() {
        window.idb = new Dexie("bbkkp_sis");
        window.idb.version(3).stores({
            pelanggan_permohonan: "++id, &name, value",
            pelanggan_permohonan_komoditas: "++id, komoditi_id, komoditi_nama, sni, merk, tipe, ukuran",
        });
    }

    $(function () {
        initIDB()

        // Your web app's Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyB5p-phArvIO4HS9sZ9978zFvaU82TUlCI",
            authDomain: "balaikulit-yogya.firebaseapp.com",
            projectId: "balaikulit-yogya",
            storageBucket: "balaikulit-yogya.appspot.com",
            messagingSenderId: "54843566382",
            appId: "1:54843566382:web:76eb5779a911d71d6d72bf"
        };
        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);

        window.FIREBASE_MESSAGING = firebase.messaging();

        if ('serviceWorker' in navigator && 'PushManager' in window) {
            navigator.serviceWorker.register('<?= url("firebase-messaging-sw.js") ?>')
                .then(function (swReg) {
                    // console.log('Service Worker is registered', swReg);
                    // console.log('ServiceWorker registration successful with scope: ', swReg.scope);
                    FIREBASE_MESSAGING.useServiceWorker(swReg);
                })
                .catch(function (error) {
                    console.error('Service Worker Error', error);
                });
        }

        // meminta perizinan allow pop up
        FIREBASE_MESSAGING.requestPermission()
            .then(() => {
                FIREBASE_MESSAGING.getToken().then(token => {
                    syncToken(token)
                })

                FIREBASE_MESSAGING.onMessage(payload => {
                    // console.log(payload)
                    alert("new notif");
                });
            })
            .catch((err) => {
                console.log(err);
                console.log("error getting permission :(");
            });
    });
</script>

@stack('javascript')
</body>

</html>
