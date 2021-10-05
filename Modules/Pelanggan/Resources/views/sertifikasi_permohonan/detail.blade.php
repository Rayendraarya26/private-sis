@extends("layouts.layout_app")

@section('title', 'Detail Permohonan Sertifikasi ')

@section('content')
    <div class="dt-content">
        <div class="row">

            @for($i = 0; $i < 4; $i++)
                <div class="col-xl-6">
                    <!-- Card -->
                    <div class="dt-card dt-card__full-height">

                        <!-- Card Header -->
                        <div class="dt-card__header">

                            <!-- Card Heading -->
                            <div class="dt-card__heading">
                                <h3 class="dt-card__title">Your Portfolio Balance</h3>
                            </div>
                            <!-- /card heading -->

                        </div>
                        <!-- /card header -->

                        <!-- Card Body -->
                        <div class="dt-card__body">
                            <!-- Grid -->
                            <div class="row no-gutters">
                                <!-- Grid Item -->
                                <div class="col-sm-7 pr-sm-2 mb-6 mb-sm-0">
                                    <h2 class="display-2 font-weight-medium mb-3">
                                        $179,626
                                        <span class="d-inline-block f-14 text-success">64% <i class="icon icon-menu-up"></i></span>
                                    </h2>

                                    <span class="d-inline-block text-light-gray mb-6">Overall balance</span>

                                    <p class="card-text">
                                        <a href="javascript:void(0)" class="btn btn-primary mr-2">Deposit</a>
                                        <a href="javascript:void(0)" class="btn text-white bg-cyan">Withdraw</a>
                                    </p>

                                    <a href="javascript:void(0)" class="d-inline-block">
                                        <i class="icon icon-add-circle mr-2"></i>Add New Wallet
                                    </a>
                                </div>
                                <!-- /grid item -->
                                <!-- Grid Item -->
                                <div class="col-sm-5">
                                    <h5 class="mb-4">Portfolio Distribution</h5>
                                    <ul class="dt-indicator">
                                        <li class="dt-indicator-item">
                                            <h5 class="dt-indicator-title f-12">BTC <span
                                                    class="d-inline-block border-left text-light-gray pl-2 ml-1">8.74</span></h5>
                                            <div class="dt-indicator-item__info" data-fill="78" data-max="100" data-percent="true">
                                                <div class="dt-indicator-item__fill bg-primary"></div>
                                                <span class="dt-indicator-item__count ml-3">0</span>
                                            </div>
                                        </li>
                                        <li class="dt-indicator-item">
                                            <h5 class="dt-indicator-title f-12">RPL <span
                                                    class="d-inline-block border-left text-light-gray pl-2 ml-1">1.23</span></h5>
                                            <div class="dt-indicator-item__info" data-fill="52" data-max="100" data-percent="true">
                                                <div class="dt-indicator-item__fill bg-success"></div>
                                                <span class="dt-indicator-item__count ml-3">0</span>
                                            </div>
                                        </li>
                                        <li class="dt-indicator-item">
                                            <h5 class="dt-indicator-title f-12">LTE <span
                                                    class="d-inline-block border-left text-light-gray pl-2 ml-1">0.71</span></h5>
                                            <div class="dt-indicator-item__info" data-fill="18" data-max="100" data-percent="true">
                                                <div class="dt-indicator-item__fill bg-warning"></div>
                                                <span class="dt-indicator-item__count ml-3">0</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <!-- /grid item -->
                            </div>
                            <!-- /grid -->
                        </div>
                        <!-- /card body -->

                    </div>
                    <!-- /card -->
                </div>
            @endfor


        </div>
    </div>
@endsection
