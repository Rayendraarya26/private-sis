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
								<tr><td>Jenis Jadwal</td><td>: {{$dataJadwal->jadw_jenis}}</td></tr>
								<tr><td>Tanggal Jadwal</td><td>: {{$dataJadwal->jadw_tanggal_mulai}} s/d {{$dataJadwal->jadw_tanggal_selesai}}</td></tr>
								<tr><td>Nama Perusahaan</td><td>: {{$dataJadwal->cust_nama}}</td></tr>
								<tr><td>Alamat Perusahaan</td><td>: {{$dataJadwal->cust_alamat}}</td></tr>
								<tr><td>No. Referensi</td><td>: {{$dataJadwal->jadw_audit_nomor_referensi}}</td></tr>
								<tr><td>Kode NACE</td><td>: {{$dataJadwal->jadw_audit_kode_nace}}</td></tr>
								<tr><td>EA Code</td><td>: {{$dataJadwal->jadw_audit_kode_ea}}</td></tr>
								<tr><td>Komoditas</td><td>: {{$dataJadwal->komodt_nama}}</td></tr>
							</tbody>
						</table>
					  </div>
					</div>
				</div>
				<div class="col-xl-6">
					<div class="card">
					  <div class="card-body p-0">
							<table class="table">
								<tbody>
									<tr><td>Ruang Lingkup</td><td>: {{$dataJadwal->jadw_audit_ruang_lingkup}}</td></tr>
									<tr><td>Standar Acuan</td><td>: {{$dataJadwal->jadw_audit_standart_acuan}}</td></tr>
									<tr><td>Kegiatan</td><td>: {{$dataJadwal->jadw_audit_kegiatan}}</td></tr>
									<tr><td>Tujuan Audit</td><td>: {{$dataJadwal->jadw_audit_tujuan_audit}}</td></tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="col-xl-12">	
					<div class="dt-card">
						<div class="dt-card__header">
							<div class="dt-card__heading">
								<h3 class="dt-card__title">Data Tim</h3>
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
                url: `{{ url("$url/ajax?action=datagrid-jadwal-tim") }}&jadw_id={{$dataJadwal->jadw_id}}`,
                rownumbers: true,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
				pagination: false,
                clientPaging: false,
				onError: function (index, row) {
					$.messager.alert('Informasi', 'Data tidak valid', 'warning');
					$('#ttData').datagrid({url: `{{ url("$url/ajax?action=datagrid-jadwal-tim") }}&jadw_id={{$dataJadwal->jadw_id}}`});
				},
                frozenColumns: [[
                    {field: 'peg_nip', title: 'NIP', width: 130, sortable: true,},
                ]],
                columns: [[
                    {field: 'peg_nama', title: 'Nama', width: 300, sortable: true,},
                    {field: 'jadw_tim_kode', title: 'Kode', width: 100, sortable: true},
					{field: 'jadw_tim_posisi', title: 'Posisi', width: 100, sortable: true,},
					{field: 'jadw_tim_kesanggupan', title: 'Kesanggupan ?', width: 150, sortable: true,},
					{field: 'jadw_tim_kesanggupan_tgl', title: 'Tgl. Kesanggupan', width: 150, sortable: true,},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'jadw_tim_kesanggupan', type: 'label'},
                    {field: 'jadw_tim_kesanggupan_tgl', type: 'label'},
                    {field: 'jadw_tim_posisi', type: 'textbox'},
                    {field: 'jadw_tim_kode', type: 'textbox'},
                ]);
        });
    </script>
@endpush
