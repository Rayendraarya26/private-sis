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
				  		<h3 class="dt-card__title text-center">PNBP</h3>
						<h4 class="mt-2 display-5 font-weight-medium text-center" id="pnbp-total"></h4>
					</div>
			  	</div>
			  	<div class="dt-card__body d-flex justify-content-center align-items-center">
			  		<div class="w-100">
						<canvas id="pnbp-doughnut" data-fill="50" height="300" width="300"></canvas>
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
		<div class="col-md-4 col-12 mb-1">
			<div class="dt-card dt-card__full-height">
			  	<div class="dt-card__header">
					<div class="dt-card__heading">
				  		<h3 class="dt-card__title text-center" id="pnbp-tahunan-title">Statistik PNBP Tahunan</h3>
						<h4 class="mt-2 display-5 font-weight-medium text-center" id="pnbp-tahunan-total"></h4>
					</div>
			  	</div>
			  	<div class="dt-card__body d-flex justify-content-center align-items-center">
			  		<div class="w-100">
						<canvas id="pnbp-tahunan-doughnut" data-fill="50" height="300" width="300"></canvas>
			  		</div>
			  	</div>
			</div>
		</div>
		<div class="col-md-8 col-12 mb-1">
			<div class="dt-card dt-card__full-height">
			  	<div class="dt-card__header">
					<div class="dt-card__heading">
				  		<h3 class="dt-card__title" id="chart-earning-title">Grafik PNBP Per Bulan</h3>
					</div>
			  	</div>
			  	<div class="dt-card__body">
			  		<canvas id="chart-earning"></canvas>
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
	    	getPnbpTahunan();
		    getGrafikPnbp();
	    });

	    getPnbp();
	    getPnbpTahunan();
	    getGrafikPnbp();
	});

	function getPnbp()
	{
        $('#pnbp-total').empty();
        $('#pnbp-doughnut').empty();
		$.get(`{{url("/dashboard/ajax?type=summary-pnbp")}}`)
        .then(({results, total}) => {
        	$('#pnbp-total').html(`Total Rp. ${(total).toString().formatUang('.')}`);
        	new Chart(document.getElementById('pnbp-doughnut'), {
		      	type: 'doughnut',
		      	data: {
		      		labels: $.map(results, function(row){
			      		return row.status;
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
				                return `Rp. ${(results[tooltip.index]?.total ?? 0).toString().formatUang('.')}`;
				            }
				        }
				    }
		      	}
		    });
		});
	}

	function getPnbpTahunan()
	{
		const tahun = $('#tahun').val();
        $('#pnbp-tahunan-title').html(`Statistik PNBP ${tahun}`);
        $('#pnbp-tahunan-total').empty();
        $('#pnbp-tahunan-doughnut').empty();
		$.get(`{{url("/dashboard/ajax?type=summary-pnbp")}}&year=${tahun}`)
        .then(({results, total}) => {
        	$('#pnbp-tahunan-total').html(`Total Rp. ${(total).toString().formatUang('.')}`);
        	let chart = new Chart(document.getElementById('pnbp-tahunan-doughnut'), {
		      	type: 'doughnut',
		      	data: {
		      		labels: $.map(results, function(row){
			      		return row.status;
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
				                return `Rp. ${(results[tooltip.index]?.total ?? 0).toString().formatUang('.')}`;
				            }
				        }
				    }
		      	}
		    });

		    charts.push(chart);
		});
	}

	function getGrafikPnbp()
	{
		const tahun = $('#tahun').val();
        $('#chart-earning-title').html(`Grafik PNBP Per Bulan, Tahun ${tahun}`);
		$("#chart-earning").empty();
		$.get(`{{url("/dashboard/ajax?type=grafik-pnbp")}}&year=${tahun}`)
        .then(({labels, datasets}) => {
            let ctx = document.getElementById('chart-earning').getContext('2d');
        	let chart = new Chart(ctx, {
			   	type: 'bar',
			   	data: {
			      	labels: labels,
			      	datasets: datasets
			   	},
			   	options: {
                    responsive: true,
                    tooltips: {
                        mode: 'point',
                        intersect: false,
                        callbacks: {
				            label: function(tooltip, data) {
				                return `Rp. ${(tooltip.value ?? 0).toString().formatUang('.')}`;
				            }
				        }
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: false
                    },
                    scales: {
                        xAxes: [{
                            display: true,
                            stacked: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Bulan'
                            }
                        }],
                        yAxes: [{
                            display: true,
                            stacked: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Total'
                            }
                        }]
                    }
			   	}
			});

		    charts.push(chart);
		});
	}
</script>
@endpush
