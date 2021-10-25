@extends('layouts.layout_app')

@section('title', 'Upload Sertifikasi Hasil Uji')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
				@if(session('message'))
					<div class="alert alert-primary alert-dismissible fade show" role="alert">
						{!! session('message') !!}
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
				@endif
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Data Upload Sertifikasi Hasil Uji Audit</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("javascript")
    
@endpush
