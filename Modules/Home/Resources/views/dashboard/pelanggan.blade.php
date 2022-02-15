@extends("layouts.layout_app")

@section('title', 'Dashboard')

@section('content')

<div class="dt-content mt-5">
	<div class="row">
	    <div class="col-md-12 col-12 mb-1">
	  		<h2 class="font-weight-medium">
	  			Selamat datang, {{auth()->user()->user_fullname}}
	  		</h2>
	    </div>
		<div class="col-md-2 col-12 mb-1">
			<div class="dt-card dt-card__full-height">
			  	<div class="dt-card__body">
				  	<h5>Total Permohonan</h5>
			  		<h1><b>{{ $certifications->count() }}</b></h1>
			  	</div>
			</div>
		</div>
		<div class="col-md-2 col-12 mb-1">
			<div class="dt-card dt-card__full-height">
			  	<div class="dt-card__body">
				  	<h5>Permohonan Proses</h5>
			  		<h1><b>{{ $certifications_process->count() }}</b></h1>
			  	</div>
			</div>
		</div>
		<div class="col-md-2 col-12 mb-1">
			<div class="dt-card dt-card__full-height">
			  	<div class="dt-card__body">
				  	<h5>Permohonan Diterima</h5>
			  		<h1><b>{{ $certifications_approved->count() }}</b></h1>
			  	</div>
			</div>
		</div>
		<div class="col-md-2 col-12 mb-1">
			<div class="dt-card dt-card__full-height">
			  	<div class="dt-card__body">
				  	<h5>Permohonan Direvisi</h5>
			  		<h1><b>{{ $certifications_revision->count() }}</b></h1>
			  	</div>
			</div>
		</div>
		<div class="col-md-2 col-12 mb-1">
			<div class="dt-card dt-card__full-height">
			  	<div class="dt-card__body">
				  	<h5>Permohonan Ditolak</h5>
			  		<h1><b>{{ $certifications_rejected->count() }}</b></h1>
			  	</div>
			</div>
		</div>
		<div class="col-md-2 col-12 mb-1">
			<div class="dt-card dt-card__full-height">
			  	<div class="dt-card__body">
				  	<h5>Permohonan Perbaikan Revisi</h5>
			  		<h1><b>{{ $certifications_fix->count() }}</b></h1>
			  	</div>
			</div>
		</div>
	    <div class="col-md-12 col-12 mb-1">
			<div class="dt-card dt-card__full-height">
			  	<div class="dt-card__body">
			  		<div class="w-100">
				    	<table cellpadding="4" cellspacing="6" class="table table-striped">
				    		<thead>
				    			<tr>
				    				<th width="20">#</th>
				    				<th>Pemohon</th>
				    				<th>Jenis</th>
				    				<th>Pembayaran</th>
				    				<th>Status Permohonan</th>
				    			</tr>
				    		</thead>
				    		<tbody>
				    			@foreach($certifications->get() as $key => $row)
					    			<tr>
					    				<td>{{ $key + 1 }}</td>
					    				<td>{{ $row->mohon_cust_nama }}</td>
					    				<td>
					    					<ul>
						    					@foreach($row?->sis_permohonan_details as $d)
						    						<li>
						    							{{ $d?->master_sertifikasi?->sert_nama }}
						    							<br>
						    							@if($d->mohon_det_jenis_status === 'lama')
						    								<b><i>(Re-sertifikasi)</i></b>
						    							@endif
						    							@if($d->mohon_det_jenis_status === 'baru')
						    								<b><i>(Permohonan Baru)</i></b>
						    							@endif
						    						</li>
						    					@endforeach
					    					</ul>
					    				</td>
					    				<td>Rp. {{ number_format($row->mohon_harga_permohonan) }}</td>
					    				<td>
					    					<?php
					    					switch ($row->mohon_approved_status)
					    					{
				                                case 'on-progress':
				                                    echo "Proses";
				                                break;
				                                case 'rejected':
				                                    echo "Ditolak";
				                                break;
				                                case 'accepted':
				                                    echo "Disetujui";
				                                break;
				                                case 'revisi':
				                                    echo "Revisi";
				                                break;
				                                case 'fix':
				                                    echo "Perbaikan Revisi";
				                                break;
					    					}
					    					?>
					    				</td>
					    			</tr>
				    			@endforeach
				    		</tbody>
				    	</table>
			  		</div>
			  	</div>
			</div>
	    </div>
	</div>
</div>

@endsection