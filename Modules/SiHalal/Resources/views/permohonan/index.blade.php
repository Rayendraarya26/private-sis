@extends('layouts.layout_app')

@section('title', 'Manajemen Permohonan SiHalal')

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
                            <h3 class="dt-card__title">Data Permohonan SiHalal</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div id="ttData" style="width:100%; min-width: 310px"></div>
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
                url: `{{ url("$url/ajax?action=datagrid-permohonan") }}`,
                rownumbers: true,
                nowrap: false,
                singleSelect: false,
                remoteSort: false,
                remoteFilter: false,
                multiSort: true,
                pagination: false,
				rowStyler:function(index,row){
					if (row.jadw_setujui_temuan == 'revisi'){
						return 'background-color:#fff4b3;color:red;font-weight:normal;';
					}
				},
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "<br/><br/>",
                        width: 80,
                        align: 'center',
                        formatter: function (val, row) {
                            /* if (row.jadw_setujui_temuan == 'revisi') {
                                return `<a href="{{url("$url/unggah")}}/${row.jadw_id}" class="btn btn-xs btn-warning"><i class="fas fa-upload"></i> Revisi</a>`
                            } else {
                                return `<a href="{{url("$url/unggah")}}/${row.jadw_id}" class="btn btn-xs btn-success"><i class="fas fa-upload"></i> Ajukan</a>`
                            } */
                        },
                    },
                ]],
                columns: [[
					{field: 'nama_status_reg', title: 'REG<br>STATUS', width: 120, sortable: true},
                    {field: 'id_reg', title: 'ID<br/>REG', width: 120, sortable: true},
					{field: 'no_urut_ndpu', title: 'No.<br/>Urut<br/>NDPU', width: 120, sortable: true},
					{field: 'no_ndpu', title: 'No.<br/>NDPU', width: 120, sortable: true},
					{field: 'no_daftar', title: 'Nomor<br/>Pendaftaran', width: 150, sortable: true},
					{field: 'nama_pu', title: 'Pelaku Usaha', width: 200, sortable: true},
					{field: 'nama_pu_alt', title: 'Bidang Usaha', width: 200, sortable: false},
					{field: 'tgl_daftar', title: 'Tanggal<br/>Pendaftaran', width: 200, sortable: true},
					{field: 'nama_jenis_daftar', title: 'Jenis Pendaftaran', width: 200, sortable: false},
					{field: 'nama_jenis_produk', title: 'Jenis Produk', width: 200, sortable: false},
					{field: 'jml_produk', title: 'Jumlah<br/>Produk', width: 100, sortable: false},
					{field: 'nama_jenis_usaha', title: 'Jenis<br/>Usaha', width: 150, sortable: false},
					{field: 'jenis_daftar', hidden: true},
					{field: 'jenis_produk', hidden: true},
                   /*  {field: 'cust_nama', title: 'Nama pelanggan', width: 200, sortable: true},
                    {field: 'jadw_setujui_temuan', title: 'Persetujuan<br/>Temuan?', width: 100, sortable: true},
                    {field: 'sert_nama', title: 'Jadwal Detail', width: 300, sortable: true},
                    {field: 'jadw_tanggal_mulai', title: 'Tanggal<br/>Mulai', width: 100, sortable: true},
                    {field: 'jadw_tanggal_selesai', title: 'Tanggal<br/>Selesai', width: 100, sortable: true}, */
                ]],
            });
			
			dg.datagrid(
                'enableFilter', [
                    {field: 'nama_status_reg', type: 'label'},
                    {field: 'action', type: 'label'},
                ]);
        });
    </script>
@endpush
