@extends('layouts.layout_app')

@section('title', 'Manajemen Invoice SiHalal')

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
                            <h3 class="dt-card__title">Data Invoice SiHalal</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div id="ttData" style="width:100%; min-width: 310px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
	@include("$view.detail")
@endsection

@push("javascript")
    <script>		
		function confirmLunas() {
			var id_inv = $("#id_inv").val();
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-success mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Pelunasan Invoice ?`,
                text: `Proses ke tahap lunas untuk invoice dengan ID #"${id_inv}", fitur aksi ini bersifat permanen dan tidak dapat di kembalikan?`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Proses',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
				if (result.value) {
					$.messager.progress();
                    $.ajax({
                        url: `{{url("$url/update")}}`,
                        type: 'POST',
                        dataType: 'json',
                        data: {id_inv: id_inv},
                        success: function (response) {
							$.messager.progress('close');
							var status_resp = 'success';
							if(response.code != 200){
								status_resp = 'error';
							}
                            toastCenter({type: status_resp, 'title': response.message});
							$("#modalFormLunas").modal('hide');
							$('#ttData').datagrid('reload');
							
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
		
		function detailLunas(index) {
			var row = $('#ttData').datagrid('getRows')[index];
			$("#id_inv").val(row.id_inv);
			$("#id_inv_text").val(row.id_inv);
			$("#no_inv").val(row.no_inv);
			$("#no_ref").val(row.no_ref);
			$("#id_ref").val(row.id_ref);
			$("#tgl_inv").val(row.tgl_inv);
			$("#tipe_trans").val(row.tipe_trans);
			$("#nama_pu").val(row.nama_pu);
			$("#alamat1").val(row.alamat1 +", "+ row.alamat2 +", "+ row.alamat3);
			$("#No_telp").val(row.No_telp);
			$("#duedate").val(row.duedate);
			$("#total_inv").val(row.total_inv.toString().formatUang("."));
			
			$("#file_inv").attr("href", "{{config("app.sihalal_folder_invoice_url")}}"+row.file_inv);
			if(row.status_payment != 'SB001'){
				$("#simpanBtnLunas").hide();
			}
			else{
				// $("#simpanBtnLunas").show();
				$("#simpanBtnLunas").hide();
			}
            $("#modalFormLunas").modal('show');
            $("#modalFormLunasTitle").html(`Detail Inv #${row.id_inv}`);
        }
		
        $(function () {
            let dg = $('#ttData').datagrid({
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid-invoice") }}`,
                rownumbers: false,
                nowrap: false,
                singleSelect: false,
                remoteSort: false,
                remoteFilter: true,
                multiSort: true,
                pageSize: 10,
                pagination: true,
                clientPaging: false,
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "<br/><br/>",
                        width: 80,
                        align: 'center',
                        formatter: function (val, row, index) {
                            var btnAksi = ``;
							// btnAksi += `<a href="{{ url("$url/detail") }}/${row.id_inv}" class="btn btn-xs btn-info btn-block"><i class="fal fa-table"></i> Detail</a>`;
							btnAksi += `<a href="javascript:void(0)" class="btn btn-xs btn-info btn-block" onclick="detailLunas('${index}')"><i class="fal fa-table"></i> Detail</a>`;
                            return `${btnAksi}`;
                        },
                    },
                ]],
                columns: [[

					{field: 'no_inv', title: 'NO<br>INVOICE', width: 120, sortable: true},
                    // {field: 'no_ref', title: 'NO<br/>REF', width: 120, sortable: true},
                    {field: 'no_daftar', title: 'NO<br/>DAFTAR', width: 120, sortable: true},
					{
						field: 'tgl_inv', title: 'Tanggal<br/>Invoice', width: 120, sortable: true,
						formatter: function (val, row, index) {
							var date = new Date(val),
								dformat = ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' +  ((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + date.getFullYear();
							return dformat;
						}
					},
					{field: 'status_payment', title: 'Status<br/>Payment', width: 120, sortable: true},
					{field: 'nama_pu', title: 'PU', width: 300, sortable: true},
					{field: 'alamat1', title: 'Alamat PU', width: 300, sortable: true},
					{
						field: 'total_inv', title: 'Total(Rp.)', width: 100, sortable: true, align:'right',
						formatter: function (val, row, index) {
							return val.toString().formatUang(".");
						}
					},
					{
						field: 'duedate', title: 'Due Date', width: 100, sortable: true,
						formatter: function (val, row, index) {
							var date = new Date(val),
								dformat = ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' +  ((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + date.getFullYear();
							return dformat;
						}
					},
					{field: 'id_inv', hidden: true},
					{field: 'id_ref', hidden: true},
                ]],
            });
			
			dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'id_ref', type: 'label'},
                    {field: 'tgl_inv', type: 'label'},
                    {field: 'alamat1', type: 'label'},
                    {field: 'total_inv', type: 'label'},
                    {field: 'duedate', type: 'label'},
                ]);
        });
    </script>
@endpush
