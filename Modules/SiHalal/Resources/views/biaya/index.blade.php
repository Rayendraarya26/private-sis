@extends('layouts.layout_app')

@section('title', 'Manajemen Biaya SiHalal')

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
		function confirmInvoice(id_reg) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-success mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Ajukan Permohonan ?`,
                text: `Ajukan ke tahap invoice permohonan dengan no pengajuan "${id_reg}", fitur aksi ini bersifat permanen dan tidak dapat di kembalikan?`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ajukan',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
					$.messager.progress();
                    $.ajax({
                        url: `{{url("$url/update")}}`,
                        type: 'POST',
                        dataType: 'json',
                        data: {id_reg: id_reg},
                        success: function (response) {
							$.messager.progress('close');
                            toastCenter({
                                type: 'success',
                                title: response.message
                            })

                            // Destroy MenuButton (rebuild onloadsuccess)
                            let dg = $('#ttData');
                            dg.datagrid('reload');
                        },
                        error: function (xhr) {
							$.messager.progress('close');
                            if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                            else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                        }
                    });
                }
            });
        }
		
        $(function () {
            let dg = $('#ttData').datagrid({
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid-permohonan-biaya") }}`,
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
                        width: 130,
                        align: 'center',
                        formatter: function (val, row) {
							var btnAksi = ``;
							btnAksi += `<a href="{{ url("$url/detail") }}/${row.id_reg}" class="btn btn-xs btn-success btn-block"><i class="fal fa-table"></i> Detail Biaya</a>`;
							btnAksi += `<a href="javascript:void(0)" class="btn btn-xs btn-info btn-block" onclick="confirmInvoice('${row.id_reg}')"><i class="far fa-comment-alt-edit"></i> Update Status</a>`;
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
