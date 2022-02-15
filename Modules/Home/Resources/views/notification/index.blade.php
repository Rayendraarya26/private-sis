@extends('layouts.layout_app')

@section('title', "Notifikasi")

@section('content')
    <!-- Site Content -->
    <div class="dt-content">
        <!-- Card -->
        <div class="dt-card">

            <!-- Card Header -->
            <div class="dt-card__header">

                <!-- Card Heading -->
                <div class="dt-card__heading">
                    <h3 class="dt-card__title">Semua Notifikasi</h3>
                </div>
                <!-- /card heading -->

            </div>
            <!-- /card header -->

            <!-- Card Body -->
            <div class="dt-card__body px-0">
                <!-- Widget -->
                <div class="dt-widget dt-widget-hover">
                @foreach($dataNotif as $n)

                    <!-- Widget Item -->
                        <div class="dt-widget__item">
                            <!-- Widget Image -->
                            <div class="dt-widget__img">
                                <!-- Avatar -->
                                @if($n->notif_is_read == "yes")
                                    <i class="fal fa-bell mr-3 fa-2x"></i>
                                @else
                                    <i class="fas fa-bell mr-3 fa-2x text-dark-blue"></i>
                            @endif
                            <!-- /avatar -->
                            </div>
                            <!-- /widget image -->

                            <!-- Widget Info -->
                            <div class="dt-widget__info">
                                <a href="{{url('/notification/open/' . $n->notif_id)}}" class="dt-widget__title">
                                    <b>{{$n->notif_title}}</b>
                                    <br>
                                    {!! $n->notif_content !!}
                                    <br>
                                    {{$n->notif_created_at->diffForHumans()}}
                                </a>
                            </div>
                            <!-- /widget info -->
                        </div>
                        <!-- /widgets item -->
                    @endforeach
                </div>
                <!-- /widget -->


                <div class="d-flex justify-content-center">
                    {!! $dataNotif->links() !!}
                </div>

            </div>
            <!-- /card body -->

        </div>
        <!-- /card -->
    </div>
@endsection
