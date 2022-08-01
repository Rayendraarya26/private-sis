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
	    <div class="col-md-4 col-12 mb-1">
			<div class="dt-card dt-card__full-height">
			  	<div class="dt-card__header">
					<div class="dt-card__heading">
				  		<h3 class="dt-card__title text-center">Pelanggan</h3>
						<h4 class="mt-2 display-5 font-weight-medium text-center">Total {{$total_pelanggan}} pelanggan</h4>
					</div>
			  	</div>
			  	<div class="dt-card__body d-flex justify-content-center align-items-center">
					<canvas id="jenis-pelanggan" data-fill="50" height="300" width="300"></canvas>
			  	</div>
			</div>
	    </div>
	    <div class="col-md-12 col-12 mb-1">
	    	<div class="row">
	    		<div class="col-md-3 col-12">
					<div class="dt-card text-white bg-primary">
					  	<div class="dt-card__body p-4">
							<div class="media">
						  		<i class="icon icon-tasks icon-4x mr-2 align-self-center"></i>
						  		<div class="media-body">
									<h4 class="mb-1 h1 font-weight-semibold text-white">{{$total_sertifikat}}
									</h4>
									<p class="mb-0">Total sertifikat</p>
						  		</div>
							</div>
					  	</div>
					</div>
	    		</div>
	    		<div class="col-md-3 col-12">
					<div class="dt-card text-white bg-success">
					  	<div class="dt-card__body p-4">
							<div class="media">
						  		<i class="icon icon-tasks icon-4x mr-2 align-self-center"></i>
						  		<div class="media-body">
									<h4 class="mb-1 h1 font-weight-semibold text-white">{{$total_sertifikat_active}}
									</h4>
									<p class="mb-0">Sertifikat aktif</p>
						  		</div>
							</div>
					  	</div>
					</div>
	    		</div>
	    		<div class="col-md-3 col-12">
					<div class="dt-card text-white bg-warning">
					  	<div class="dt-card__body p-4">
							<div class="media">
						  		<i class="icon icon-tasks icon-4x mr-2 align-self-center"></i>
						  		<div class="media-body">
									<h4 class="mb-1 h1 font-weight-semibold text-white">{{$total_sertifikat_expired}}
									</h4>
									<p class="mb-0">Sertifikat kadaluwarsa</p>
						  		</div>
							</div>
					  	</div>
					</div>
	    		</div>
	    		<div class="col-md-3 col-12">
					<div class="dt-card text-white bg-danger">
					  	<div class="dt-card__body p-4">
							<div class="media">
						  		<i class="icon icon-tasks icon-4x mr-2 align-self-center"></i>
						  		<div class="media-body">
									<h4 class="mb-1 h1 font-weight-semibold text-white">{{$total_sertifikat_banned}}
									</h4>
									<p class="mb-0">Sertifikat dibekukan</p>
						  		</div>
							</div>
					  	</div>
					</div>
	    		</div>
	    	</div>
	    </div>
		<div class="col-12">
			<hr>
		</div>
		<div class="col-md-9 col-6">
			<h3 class="text-primary">Statistik Tahunan</h3>
		</div>
		<div class="col-md-3 col-6 mb-1 text-right">
			<div class="form-group row">
                <label class="col-form-label col-5" for="tahun">Tahun</label>
                <div class="col-7">
					<select name="tahun" id="tahun" class="form-control">
						<?php foreach (range(date('Y') - 1, date('Y') + 2) as $year) : ?>
							<option value="<?= $year; ?>" <?= $year == date('Y') ? 'selected' : '' ?>>
								<?= $year; ?>
							</option>
						<?php endforeach; ?>
					</select>
                </div>
            </div>
		</div>
		<div class="col-md-6 col-12 mb-1">
			<div class="dt-card dt-card__full-height">
			  	<div class="dt-card__header">
					<div class="dt-card__heading">
				  		<h3 class="dt-card__title text-center" id="sertifikat-terbit-title">Sertifikat Terbit</h3>
						<h4 class="mt-2 display-5 font-weight-medium text-center" id="sertifikat-terbit-total"></h4>
					</div>
			  	</div>
			  	<div class="dt-card__body d-flex justify-content-center align-items-center">
			  		<div class="w-100">
						<canvas id="sertifikat-terbit-doughnut" data-fill="50" height="300" width="300"></canvas>
			  		</div>
			  	</div>
			</div>
		</div>
		<div class="col-md-12 col-12 mb-1">
			<div class="dt-card dt-card__full-height">
			  	<div class="dt-card__header">
					<div class="dt-card__heading">
				  		<h3 class="dt-card__title text-center" id="performance-auditors-title">Performance Auditor</h3>
					</div>
			  	</div>
			  	<div class="dt-card__body d-flex justify-content-center align-items-center">
				    <!-- Preview Email Modal -->
				    <div class="modal fade" id="performance-auditor-modal" role="dialog" aria-labelledby="model-4"
				         aria-hidden="true">
				        <div class="modal-dialog modal-lg" role="document">
				            <div class="modal-content">
				                <div class="modal-header">
				                    <h3 class="modal-title" id="auditor-title">Detail</h3>
				                </div>
				                <div class="modal-body" id="auditor-content"></div>
				            </div>
				        </div>
				    </div>
				    <!-- /modal -->
			  		<div class="w-100">
			  			<table class="table table-striped" style="border: 2px solid #eaeaea; border-radius: 6px;">
			  				<thead>
			  					<tr>
			  						<th width="5%">#</th>
			  						<th width="30%">Nama</th>
			  						<th>Statistik</th>
			  					</tr>
			  				</thead>
			  				<tbody id="performance-auditors"></tbody>
			  			</table>
			  		</div>
			  	</div>
			</div>
		</div>
		<div class="col-md-12 col-12 mb-1">
			<div class="dt-card dt-card__full-height">
			  	<div class="dt-card__header">
					<div class="dt-card__heading">
				  		<h3 class="dt-card__title text-center" id="plan-audit-title">Rencana Audit</h3>
					</div>
			  	</div>
			  	<div class="dt-card__body d-flex justify-content-center align-items-center">
			  		<div class="w-100">
			  			<table class="table table-striped" style="border: 2px solid #eaeaea; border-radius: 6px;">
			  				<thead>
			  					<tr>
			  						<th width="3%">#</th>
			  						<th width="20%">Nama Perusahaan</th>
			  						<th width="15%">Jenis Sertifikasi</th>
			  						<th width="15%">Tanggal</th>
			  						<th width="15%">Tim</th>
			  					</tr>
			  				</thead>
			  				<tbody id="plan-audit-table"></tbody>
			  			</table>
			  		</div>
			  	</div>
			</div>
		</div>
    </div>
</div>

@endsection

@push("javascript")
    <script src="{{ asset('/node_modules/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('/node_modules/ammap3/ammap/ammap.js') }}"></script>
    <script src="{{ asset('/node_modules/ammap3/ammap/maps/js/continentsLow.js') }}"></script>
    <script src="{{ asset('/node_modules/ammap3/ammap/themes/light.js') }}"></script>

    <script src="{{ asset('/node_modules/amcharts3/amcharts/amcharts.js') }}"></script>
    <script src="{{ asset('/node_modules/amcharts3/amcharts/gauge.js') }}"></script>
    <script>
        let color  = Chart.helpers.color;
        let charts = [];

        $(function () {
            let jenis_pelanggan_data = <?= json_encode($company_types->toArray()) ?>;

            new Chart(document.getElementById('jenis-pelanggan'), {
                type: 'doughnut',
	      	data: {
	      		labels: $.map(jenis_pelanggan_data, function(row){
	      			return row.jenis_perusahaan_nama;
		      	}),
		      	datasets: [
		        	{
		          		data: $.map(jenis_pelanggan_data, function(row){
				      		return row.sis_pelanggans_count;
				      	}),
		          		backgroundColor: $.map(jenis_pelanggan_data, function(row){
				      		return row.jenis_perusahaan_color;
				      	}),
		          		hoverBackgroundColor: $.map(jenis_pelanggan_data, function(row){
				      		return row.jenis_perusahaan_color;
				      	})
		        	}
		      	],
	      	},
	      	options: {
	        	cutoutPercentage: 80,
	        	responsive: true,
	        	legend: {
	          		display: true
	        	},
	        	tooltips: {
			        callbacks: {
			            label: function(tooltip, data) {
			                return `${(jenis_pelanggan_data[tooltip.index]?.sis_pelanggans_count ?? 0).toString().formatUang('.')}`;
			            }
			        }
			    }
	      	}
	    });

	    $('#tahun').on('change', function(){
	    	$.map(charts, function(chart){
	    		chart.destroy();
	    	})
	    	charts = [];
			getSertifikatTerbit();
			getPerformanceAuditor();
			getPlanAudit();
	    });

		getSertifikatTerbit();
		getPerformanceAuditor();
		getPlanAudit();
	});

	function getSertifikatTerbit()
	{
		const tahun = $('#tahun').val();
        $('#sertifikat-terbit-title').html(`Sertifikat Terbit ${tahun}`);
        $('#sertifikat-terbit-total').empty();
        $('#sertifikat-terbit-doughnut').empty();
		$.get(`{{url("/dashboard/ajax?type=pie-sertifikat")}}&year=${tahun}`)
        .then(({results, total}) => {
        	$('#sertifikat-terbit-total').html(`Total ${(total).toString().formatUang('.')} sertifikat`);
        	let chart = new Chart(document.getElementById('sertifikat-terbit-doughnut'), {
		      	type: 'doughnut',
		      	data: {
		      		labels: $.map(results, function(row){
			      		return row.sert_nama;
			      	}),
			      	datasets: [
			        	{
			          		data: $.map(results, function(row){
					      		return row.total;
					      	}),
			          		backgroundColor: $.map(results, function(row){
					      		return color(row.color).alpha(0.8).rgbString();
					      	}),
			          		hoverBackgroundColor: $.map(results, function(row){
					      		return color(row.color).alpha(0.8).rgbString();
					      	})
			        	}
			      	]
		      	},
		      	options: {
		        	cutoutPercentage: 80,
		        	responsive: true,
		        	legend: {
		          		display: true
		        	},
		        	tooltips: {
				        callbacks: {
				            label: function(tooltip, data) {
				                return results[tooltip.index]?.sert_nama.substring(0, 16) +'... : '+ (results[tooltip.index]?.total ?? 0).toString().formatUang('.') + ' sertifikat';
				            }
				        }
				    }
		      	}
		    });

		    charts.push(chart);
		});
	}

	function getPerformanceAuditor()
	{	
		const tahun = $('#tahun').val();
		$('#performance-auditors-title').html('Performance Auditor '+tahun);
		$('#performance-auditors').html('<tr><td colspan="3">Mohon tunggu</td></tr>');
		$.get(`{{url("/dashboard/ajax?type=performance-auditor")}}&year=${tahun}`)
        .then(({results}) => {
			let rows = '';
        	if (results)
        	{
        		results.map((r, i) => {
					rows += `
					<tr>
						<td>${i + 1}</td>
						<td>${r?.master_pegawai?.peg_nama}</td>
						<td>
							<button data-toggle="modal" data-target="#performance-auditor-modal"
							onclick="getDetailPerformanceAuditor(${r.peg_id}, 'ketua', '${r?.master_pegawai?.peg_nama}')"
							class="btn ${ r.total_ketua > 0 ? 'btn-primary' : 'btn-warning' }">
								Ketua: ${ r.total_ketua } kali
							</button>
							<button data-toggle="modal" data-target="#performance-auditor-modal"
							onclick="getDetailPerformanceAuditor(${r.peg_id}, 'auditor', '${r?.master_pegawai?.peg_nama}')"
							class="btn ${ r.total_auditor > 0 ? 'btn-primary' : 'btn-warning' }">
								Auditor: ${ r.total_auditor } kali
							</button>
						</td>
					</tr>`;
        		})

        		$('#performance-auditors').html(rows);
        	}
		});
	}

	function getDetailPerformanceAuditor(peg_id, type, nama)
	{
		const tahun = $('#tahun').val();
		$('#auditor-title').html('Detail Performa Auditor: '+nama);
		$('#auditor-content').html('Mohon tunggu...');
		$.get(`{{url("/dashboard/ajax?type=performance-detail")}}&peg=${peg_id}&pos=${type}&year=${tahun}`)
        .then(({results}) => {
        	let tables = '';

        	results.map((r) => {
        		let audits = '<ul style="padding-left: 16px;">';

        		if (r?.sis_jadwal?.sis_jadwal_audits)
        		{
        			r.sis_jadwal.sis_jadwal_audits.map((a) => {
        				audits += `<li>
        					Kegiatan: ${a.jadw_audit_kegiatan}
        					<br>
        					Jenis: ${a.jadw_audit_jenis.toUpperCase()}
        					<br>
        					Perusahaan: ${a?.sis_permohonan?.sis_pelanggan?.cust_nama ?? '-'}
        					<br>
        					Alamat Perusahaan: ${a?.sis_permohonan?.sis_pelanggan?.cust_alamat ?? '-'}
        					<br>
        					<hr>
        				</li>`;
        			})
        		}

        		audits += '</ul>';

        		tables += `
	        		<table cellpadding="2" cellspacing="2" class="table table-striped" style="border: 2px solid #eaeaea; border-radius: 6px;">
						<tbody>
							<tr>
								<td width="20%">Posisi</td>
								<td width="2%">:</td>
								<td>${r.jadw_tim_posisi.toUpperCase()}</td>
							</tr>
							<tr>
								<td width="20%">Kesanggupan</td>
								<td width="2%">:</td>
								<td>${r.jadw_tim_kesanggupan.toUpperCase()}</td>
							</tr>
							<tr>
								<td width="20%">Jadwal</td>
								<td width="2%">:</td>
								<td>
									${dateFormat(r.sis_jadwal?.jadw_tanggal_mulai)}
									s/d
									${dateFormat(r.sis_jadwal?.jadw_tanggal_selesai)}
								</td>
							</tr>
							<tr>
								<td width="20%">Jadwal Audit</td>
								<td width="2%">:</td>
								<td>${audits}</td>
							</tr>
						</tbody>
					</table>
					<br>`;
        	});

			$('#auditor-content').html(tables);
		});
	}

	function dateFormat(date)
	{
		const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
	    const dateObj = new Date(date);
	    const month = monthNames[dateObj.getMonth()];
	    const day = String(dateObj.getDate()).padStart(2, '0');
	    const year = dateObj.getFullYear();
	    return `${day} ${month} ${year}`;
	}

	function getPlanAudit()
	{
		const tahun = $('#tahun').val();
		$('#plan-audit-title').html('Rencana Audit '+tahun);
		$('#plan-audit-table').html('<tr><td colspan="5">Mohon tunggu</td></tr>');
		$.get(`{{url("/dashboard/ajax?type=plan-audit")}}&year=${tahun}`)
        .then(({results}) => {
        	let rows = '';
        	if (results)
        	{
        		results.map((r, i) =>
        		{
        			let teams = '<ul style="padding-left: 16px;">';
        			if (r?.sis_jadwal?.sis_jadwal_tims)
        			{
        				r?.sis_jadwal?.sis_jadwal_tims.map((t) => {
        					teams += `<li style="color: ${t.jadw_tim_kesanggupan == 'ya' ? 'green' : 'red'}">
        						${t?.master_pegawai?.peg_nama ?? '-'}
        					</li>`;
        				})
        			}
        			teams += '</ul>';

        			rows += `
        				<tr>
        					<td>${i + 1}</td>
        					<td>${r?.sis_jadwal?.sis_pelanggan?.cust_nama ?? '-'}</td>
        					<td>
        						<b>(${r.jadw_audit_jenis.toUpperCase()})</b>
        						<br>${r?.master_sertifikasi?.sert_nama ?? '-'}
        					</td>
        					<td>
								${dateFormat(r.sis_jadwal?.jadw_tanggal_mulai)}
								s/d<br>
								${dateFormat(r.sis_jadwal?.jadw_tanggal_selesai)}
        					</td>
        					<td>${teams}</td>
        				</tr>
        			`;
        		});

				$('#plan-audit-table').html(rows);
			}
		});
	}
</script>
@endpush
