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
	    $('#tahun').on('change', function(){
	    	$.map(charts, function(chart){
	    		chart.destroy();
	    	})
	    	charts = [];
			getPlanAudit();
	    });

		getPlanAudit();
	});

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
        					<td>${r?.sis_permohonan?.sis_pelanggan?.cust_nama ?? '-'}</td>
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
