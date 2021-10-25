@extends('layouts.layout_app')

@section('title', 'Log Revisi')
@push('css')
    <style>
        body {
            margin-top: 20px;
        }

        .timeline {
            border-left: 3px solid #727cf5;
            border-bottom-right-radius: 4px;
            border-top-right-radius: 4px;
            background: rgba(114, 124, 245, 0.09);
            margin: 0 auto;
            letter-spacing: 0.2px;
            position: relative;
            line-height: 1.4em;
            font-size: 1.03em;
            padding: 50px;
            list-style: none;
            text-align: left;
            max-width: 40%;
        }

        @media (max-width: 767px) {
            .timeline {
                max-width: 98%;
                padding: 25px;
            }
        }

        .timeline h1 {
            font-weight: 300;
            font-size: 1.4em;
        }

        .timeline h2,
        .timeline h3 {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .timeline .event {
            border-bottom: 1px dashed #e8ebf1;
            padding-bottom: 25px;
            margin-bottom: 25px;
            position: relative;
        }

        @media (max-width: 767px) {
            .timeline .event {
                padding-top: 30px;
            }
        }

        .timeline .event:last-of-type {
            padding-bottom: 0;
            margin-bottom: 0;
            border: none;
        }

        .timeline .event:before,
        .timeline .event:after {
            position: absolute;
            display: block;
            top: 0;
        }

        .timeline .event:before {
            left: -300px;
            content: attr(data-date);
            text-align: right;
            font-weight: 100;
            font-size: 0.9em;
            min-width: 120px;
        }

        @media (max-width: 767px) {
            .timeline .event:before {
                left: 0px;
                text-align: left;
            }
        }

        .timeline .event:after {
            -webkit-box-shadow: 0 0 0 3px #727cf5;
            box-shadow: 0 0 0 3px #727cf5;
            left: -55.8px;
            background: #fff;
            border-radius: 50%;
            height: 9px;
            width: 9px;
            content: "";
            top: 5px;
        }

        @media (max-width: 767px) {
            .timeline .event:after {
                left: -31.8px;
            }
        }

        .timeline-revisi {
            border-left: 3px solid #ff6f00;
            border-bottom-right-radius: 4px;
            border-top-right-radius: 4px;
            background: rgba(255, 160, 64, 0.9);
            margin: 0 auto;
            letter-spacing: 0.2px;
            position: relative;
            line-height: 1.4em;
            font-size: 1.03em;
            padding: 50px;
            list-style: none;
            text-align: left;
            max-width: 40%;
        }

        @media (max-width: 767px) {
            .timeline-revisi {
                max-width: 98%;
                padding: 25px;
            }
        }

        .timeline-revisi h1 {
            font-weight: 300;
            font-size: 1.4em;
        }

        .timeline-revisi h2,
        .timeline-revisi h3 {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .timeline-revisi .event {
            border-bottom: 1px dashed #e8ebf1;
            padding-bottom: 25px;
            margin-bottom: 25px;
            position: relative;
        }

        @media (max-width: 767px) {
            .timeline-revisi .event {
                padding-top: 30px;
            }
        }

        .timeline-revisi .event:last-of-type {
            padding-bottom: 0;
            margin-bottom: 0;
            border: none;
        }

        .timeline-revisi .event:before,
        .timeline-revisi .event:after {
            position: absolute;
            display: block;
            top: 0;
        }

        .timeline-revisi .event:before {
            left: -300px;
            content: attr(data-date);
            text-align: right;
            font-weight: 100;
            font-size: 0.9em;
            min-width: 120px;
        }

        @media (max-width: 767px) {
            .timeline-revisi .event:before {
                left: 0px;
                text-align: left;
            }
        }

        .timeline-revisi .event:after {
            -webkit-box-shadow: 0 0 0 3px #ff6f00;
            box-shadow: 0 0 0 3px #ff6f00;
            left: -55.8px;
            background: #fff;
            border-radius: 50%;
            height: 9px;
            width: 9px;
            content: "";
            top: 5px;
        }

        @media (max-width: 767px) {
            .timeline-revisi .event:after {
                left: -31.8px;
            }
        }

    </style>
@endpush
@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default"
                   href="{{url("$url")}}"
                   style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali
                </a>
                <div class="dt-card">
                    <div class="dt-card__body">
                        <div class="row">
                            <div class="col-lg-12">
								<div id="content">
									@foreach($dataLog as $status)
										<ul class="{{$status->jlog_tipe == "informasi" ? 'timeline' : 'timeline-revisi'}}">
											<li class="event"
												data-date="{{$status->created_at->isoFormat('LLLL')}}">
												<h3>{{$status->jlog_judul}}</h3>
												<p>{{$status->jlog_pesan}}</p>
											</li>
										</ul>
									@endforeach
								</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection