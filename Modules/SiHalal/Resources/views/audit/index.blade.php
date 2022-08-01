@extends('layouts.layout_app')

@section('title', 'Manajemen Audit SiHalal')

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
                url: `{{ url("$url/ajax?action=datagrid-permohonan-audit") }}`,
                rownumbers: true,
                nowrap: false,
                singleSelect: false,
                remoteSort: false,
                remoteFilter: false,
                multiSort: true,
                pagination: false,
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "<br/><br/>",
                        width: 80,
                        align: 'center',
                        formatter: function (val, row) {
							var btnAksi = ``;
							if(row.status == 'audit'){
								btnAksi += `<a href="{{ url("$url/detail") }}/${row.id_reg}" class="btn btn-xs btn-success btn-block"><i class="fal fa-table"></i> Detail</a>`;
							}
							
							return `${btnAksi}`;
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
