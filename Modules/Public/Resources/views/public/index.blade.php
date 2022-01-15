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

@extends('layouts.layout_public')

@section('title', "Home")

@push('css')
<style>
	.navbar-collapse.in{
		background-color: #000000;
		opacity: 0.7;
	}
</style>
@endpush

@section('content')

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

@endsection