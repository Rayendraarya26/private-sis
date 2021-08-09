@extends('layouts.layout_app')

@section('title', "Profile")

@section('content')
    <!-- Site Content -->
    <div class="dt-content">

        <!-- Profile -->
        <div class="profile">

            <!-- Profile Banner -->
            <div class="profile__banner">

                <!-- Profile Banner Top -->
                <div class="profile__banner-top">
                    <!-- Avatar Wrapper -->
                    <div class="dt-avatar-wrapper">
                        <!-- Avatar -->
                        <img class="dt-avatar dt-avatar__shadow size-90 mr-sm-4"
                             src="{{auth()->user()->getImage()}}" alt="Dinesh Suthar">
                        <!-- /avatar -->

                        <!-- Info -->
                        <div class="dt-avatar-info">
                            <span class="dt-avatar-name display-4 mb-2 font-weight-light">
                                {{auth()->user()->user_fullname}}
                            </span>
                            <span class="f-16">{{ucwords(session('group_selected_name'))}}</span>
                        </div>
                        <!-- /info -->
                    </div>
                    <!-- /avatar wrapper -->
                </div>
                <!-- /profile banner top -->

                <!-- Profile Banner Bottom -->
                <div class="profile__banner-bottom">


                    <!-- Dropdown -->
                    <div class="dropdown pl-3 mt-2 ml-auto">

                        <!-- Dropdown Button -->
                        <a href="#" class="dropdown-toggle no-arrow text-white" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            <i class="icon icon-setting icon-xl mr-2"></i><span class="d-none d-sm-inline-block">Pengaturan</span>
                        </a>
                        <!-- /dropdown button -->

                        <!-- Dropdown Menu -->
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{route('update_profile')}}">
                                <i class="far fa-user"></i>
                                Perbarui profile
                            </a>
                            <a class="dropdown-item" href="{{route('change_password')}}">
                                <i class="far fa-unlock-alt"></i>
                                Perbarui kata sandi
                            </a>
                            {{--                            <a class="dropdown-item" href="javascript:void(0)">Something else here</a>--}}
                            {{--                            <div class="dropdown-divider"></div>--}}
                            {{--                            <a class="dropdown-item" href="javascript:void(0)">Separated link</a>--}}
                        </div>
                        <!-- /dropdown menu -->

                    </div>
                    <!-- /dropdown -->

                </div>
                <!-- /profile banner bottom -->

            </div>
            <!-- /profile banner -->

            <!-- Profile Content -->
            <div class="profile-content">

                <!-- Grid -->
                <div class="row">

                    <!-- Grid Item -->
                    <div class="col-xl-4 order-xl-2">
                        <!-- Grid -->
                        <div class="row">
                            <!-- Grid Item -->
                            <div class="col-xl-12 col-md-12 col-12 order-xl-1">

                                <!-- Card -->
                                <div class="dt-card dt-card__full-height">

                                    <!-- Card Header -->
                                    <div class="dt-card__header">

                                        <!-- Card Heading -->
                                        <div class="dt-card__heading">
                                            <h3 class="dt-card__title">Kontak</h3>
                                        </div>
                                        <!-- /card heading -->

                                    </div>
                                    <!-- /card header -->

                                    <!-- Card Body -->
                                    <div class="dt-card__body">
                                        <!-- Media -->
                                        <div class="media mb-5">

                                            <i class="icon icon-email icon-xl mr-5"></i>

                                            <!-- Media Body -->
                                            <div class="media-body">
                                                <span class="d-block text-light-gray f-12 mb-1">Mail</span>
                                                <a href="mailto:{{auth()->user()->user_email}}">
                                                    {{auth()->user()->user_email}}
                                                </a>
                                            </div>
                                            <!-- /media body -->

                                        </div>
                                        <!-- /media -->

                                        <!-- Media -->
                                        <div class="media">

                                            <i class="icon icon-phone icon-xl mr-5"></i>

                                            <!-- Media Body -->
                                            <div class="media-body">
                                                <span class="d-block text-light-gray f-12 mb-1">Hanphone</span>
                                                <span class="h5">-</span>
                                            </div>
                                            <!-- /media body -->

                                        </div>
                                        <!-- /media -->
                                    </div>
                                    <!-- /card body -->

                                </div>
                                <!-- /card -->

                            </div>
                            <!-- /grid item -->
                        </div>
                        <!-- /grid -->
                    </div>
                    <!-- /grid item -->

                    <!-- Grid Item -->
                    <div class="col-xl-8 order-xl-1">
                        <!-- Card -->
                        <div class="card">

                            <!-- Card Header -->
                            <div class="card-header card-nav bg-transparent d-flex justify-content-between">
                                <h2 class="mb--20">Tentang</h2>

                                <ul class="card-header-links nav nav-underline" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#tab-pane1" role="tab"
                                           aria-controls="tab-pane1"
                                           aria-selected="true">Overview</a>
                                    </li>
                                </ul>

                            </div>
                            <!-- /card header -->

                            <!-- Card Body -->
                            <div class="card-body pb-2">

                                <!-- Tab Content-->
                                <div class="tab-content mt-5">

                                    <!-- Tab panel -->
                                    <div id="tab-pane1" class="tab-pane active">

                                        <!-- List -->
                                        <ul class="dt-list dt-list-one-third">
                                            <!-- List Item -->
                                            <li class="dt-list__item">
                                                <!-- Media -->
                                                <div class="media">

                                                    <i class="far fa-building fa-2x mr-5 align-self-center text-warning"></i>

                                                    <!-- Media Body -->
                                                    <div class="media-body">
                                                        <span class="d-block text-light-gray f-12 mb-1">
                                                            Perusahaan
                                                        </span>
                                                        <h5 class="mb-0">-</h5>
                                                    </div>
                                                    <!-- /media body -->

                                                </div>
                                                <!-- /media -->
                                            </li>
                                            <!-- /list item -->

                                            <!-- List Item -->
                                            <li class="dt-list__item">
                                                <!-- Media -->
                                                <div class="media">

                                                    <i class="far fa-birthday-cake fa-2x mr-5 align-self-center text-warning"></i>

                                                    <!-- Media Body -->
                                                    <div class="media-body">
                                                        <span
                                                            class="d-block text-light-gray f-12 mb-1">Tanggal Lahir</span>
                                                        <h5 class="mb-0">-</h5>
                                                    </div>
                                                    <!-- /media body -->

                                                </div>
                                                <!-- /media -->
                                            </li>
                                            <!-- /list item -->

                                            <!-- List Item -->
                                            <li class="dt-list__item">
                                                <!-- Media -->
                                                <div class="media">

                                                    {{--                                                    <i class="icon icon-graduation icon-4x mr-5 align-self-center text-warning"></i>--}}
                                                    <i class="far fa-clock fa-2x mr-5 align-self-center text-warning"></i>

                                                    <!-- Media Body -->
                                                    <div class="media-body">
                                                        <span
                                                            class="d-block text-light-gray f-12 mb-1">Last Login</span>
                                                        <h5 class="mb-0">{{auth()->user()->user_last_login->format("d M Y, h:i:s")}}</h5>
                                                    </div>
                                                    <!-- /media body -->

                                                </div>
                                                <!-- /media -->
                                            </li>
                                            <!-- /list item -->

                                            <!-- List Item -->
                                            <li class="dt-list__item">
                                                <!-- Media -->
                                                <div class="media">

                                                    <i class="far fa-flag fa-2x mr-5 align-self-center text-warning"></i>

                                                    <!-- Media Body -->
                                                    <div class="media-body">
                                                        <span
                                                            class="d-block text-light-gray f-12 mb-1">Negara</span>
                                                        <h5 class="mb-0">Indonesia</h5>
                                                    </div>
                                                    <!-- /media body -->

                                                </div>
                                                <!-- /media -->
                                            </li>
                                            <!-- /list item -->
                                        </ul>
                                        <!-- /list -->

                                    </div>
                                    <!-- /tab panel -->
                                </div>
                                <!-- /tab content-->

                            </div>
                            <!-- /card body -->

                        </div>
                        <!-- /card -->
                    </div>
                    <!-- /grid item -->

                </div>
                <!-- /grid -->

            </div>
            <!-- /profile content -->

        </div>
        <!-- /profile -->

    </div>
@endsection
