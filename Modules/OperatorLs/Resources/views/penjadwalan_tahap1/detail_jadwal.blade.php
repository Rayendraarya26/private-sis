@extends("layouts.layout_app")

@section('title', 'Penyusunan Komite')

@section('content')
    <div class="dt-content">
		<div class="col-xl-12">
			<a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"><i class="fad fa-arrow-left"></i> Kembali</a>
			<div class="row">
				<div class="col-xl-6">
					<div class="card">
					  <div class="card-body p-0">									
						<table class="table">
							<tbody>
								<tr><td>No Jadwal</td><td>: {{$dataJadwal->aud_thp1_id}}</td></tr>
								<tr><td>Tanggal Jadwal</td><td>: {{$dataJadwal->aud_thp1_tanggal_mulai}} s/d {{$dataJadwal->aud_thp1_tanggal_selesai}}</td></tr>
								<tr><td>Nama Perusahaan</td><td>: {{$dataJadwal->cust_nama}}</td></tr>
								<tr><td>Alamat Perusahaan</td><td>: {{$dataJadwal->cust_alamat}}</td></tr>
								<tr><td>No. Permohonan</td><td>: {{$dataJadwal->mohon_id}}</td></tr>
								<tr><td>No. Billing</td><td>: {{$dataJadwal->bill_nomor_billing}}</td></tr>
								<tr><td>Sertifikasi</td><td>: {{$dataJadwal->sert_nama}}</td></tr>
								<tr><td>Komoditas</td><td>: {{$dataJadwal->komodt_nama}}</td></tr>
								<tr><td>Kode NACE</td><td>: {{$dataJadwal->mohon_kmditi_nace}}</td></tr>
								<tr><td>EA Code</td><td>: {{$dataJadwal->mohon_kmditi_ea}}</td></tr>
								<tr><td>Ruang Lingkup</td><td>: {{$dataJadwal->mohon_kmditi_ruang_lingkup}}</td></tr>
							</tbody>
						</table>
					  </div>
					</div>
				</div>
				<div class="col-xl-12">	
					<div class="dt-card">
						<div class="dt-card__header">
							<div class="dt-card__heading">
								<h3 class="dt-card__title">Data Tim Audit</h3>
							</div>
						</div>
						<div class="dt-card__body">
							<div id="ttData" style="width:100%; min-width: 310px"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection


@push("javascript")
    <script>
        $(function () {
            let dg = $('#ttData').datagrid({
				method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid-jadwal-tim") }}&aud_thp1_id={{$dataJadwal->aud_thp1_id}}`,
                rownumbers: true,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
				pagination: false,
                clientPaging: false,
				onError: function (index, row) {
					$.messager.alert('Informasi', 'Data tidak valid', 'warning');
					$('#ttData').datagrid({url: `{{ url("$url/ajax?action=datagrid-jadwal-tim") }}&aud_thp1_id={{$dataJadwal->aud_thp1_id}}`});
				},
                columns: [[
                    {field: 'peg_nip', title: 'NIP', width: 120, sortable: true},
					{field: 'peg_nama', title: 'Nama', width: 250, sortable: true},
					{field: 'thp1_tim_kode', title: 'Kode', width: 100, sortable: true},
					{field: 'thp1_tim_posisi', title: 'Posisi', width: 150, sortable: true},
					{field: 'thp1_tim_kesanggupan', title: 'Kesanggupan ?', width: 150, sortable: true,},
					{field: 'thp1_tim_kesanggupan_tgl', title: 'Tgl. Kesanggupan', width: 150, sortable: true,},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'thp1_tim_kesanggupan', type: 'label'},
                    {field: 'thp1_tim_kesanggupan_tgl', type: 'label'},
                    {field: 'thp1_tim_posisi', type: 'textbox'},
                    {field: 'thp1_tim_kode', type: 'textbox'},
                ]);
        });
    </script>
@endpush
