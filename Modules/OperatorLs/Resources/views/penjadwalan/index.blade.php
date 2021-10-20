@extends('layouts.layout_app')

@section('title', 'Penjadwalan')

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
                            <h3 class="dt-card__title">Data Jadwal</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div id="ttData" style="width:100%; min-width: 310px"></div>
						<div id="toolbar" style="padding: 10px 0 10px 20px">
                            <div class="row">
								@if(authorized("{$module}@create"))
									<div>
										<a href="{{ url("$url/create") }}" class="btn btn-outline-success btn-xs">
											<i class="fas fa-plus"></i> Tambah
										</a>
									</div>
									&nbsp;&nbsp;&nbsp;
								@endif							
								@if(authorized("{$module}@destroy"))
									<div class="datagrid-btn-separator"></div>
									&nbsp;&nbsp;&nbsp;
									<div>
										<button class="btn btn-outline-danger btn-xs" onclick="confirmDelete()">
											<i class="fas fa-trash"></i> Hapus
										</button>
									</div>
								@endif
							</div>
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
                url: `{{ url("$url/ajax?action=datagrid-jadwal") }}`,
                rownumbers: false,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
                // fitColumns: true,
                toolbar: '#toolbar',
                pagination: true,
                pageSize: 50,
                clientPaging: false,
                frozenColumns: [[
                    {field: 'ck', checkbox: true, sortable: false},
                    {
                        field: 'action',
                        title: "Aksi",
                        width: 80,
                        align: 'center',
                        formatter: function (val, row) {
							let dom = `dropdownMenu_${row.jadw_id}`;
                            let btnEdit = ``;							
							if(row.jadw_tanggal_status == 'on-going')
								btnEdit += `<div data-options="iconCls:'fad fa-edit'" onclick="location.href = '{{ url("$url/edit") }}?tipe=edit-jadwal&jadw_id=${row.jadw_id}'">Edit Jadwal</div>`;
							
							if(row.jadw_tanggal_status == 'rejected')
								btnEdit += `<div data-options="iconCls:'fad fa-edit'" onclick="location.href = '{{ url("$url/edit") }}?tipe=edit-jadwal&jadw_id=${row.jadw_id}'">Revisi Jadwal</div>`;
							
							btnEdit += `<div data-options="iconCls:'fad fa-flag-checkered'" onclick="location.href = '{{ url("$url/detail") }}?tipe=log-jadwal&jadw_id=${row.jadw_id}'">Log Revisi</div>`;
							
                            return `
								<div>
									<button class="btn-action btn-info btn-block" data-index="${row.jadw_id}" title="Aksi">
										<i class="fa fa-setting"></i> Aksi
									</button>
									<div id="${dom}" style="width:150px; display: none;">
										@if(authorized("{$module}@edit")) ${btnEdit} @endif
								</div>
							</div>`
                        }
                    }
                ]],
                columns: [[
                    {field: 'cust_nama', title: 'Nama pelanggan', width: 200, sortable: true},
                    {field: 'jadw_jenis', title: 'Jenis', width: 100, sortable: true},
                    {field: 'jadw_tanggal_status', title: 'Persetujuan<br/>Tanggal', width: 100, sortable: true},
                    {field: 'jadw_tanggal_mulai', title: 'Tanggal<br/>Mulai', width: 100, sortable: true},
                    {field: 'jadw_tanggal_selesai', title: 'Tanggal<br/>Selesai', width: 100, sortable: true},
                ]],
				onLoadSuccess: function (data) {
                    $(this).datagrid('getPanel').find('.btn-action').each(function (idx, row) {
                        $(this).menubutton({
                            menu: '#dropdownMenu_' + data.rows[idx].jadw_id
                        });
                    });
                },
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
					{
                        field: 'jadw_tanggal_status',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            value: '',
                            data: [
                                {value: 'rejected', text: 'Revisi'},
                                {value: 'on-going', text: 'On-going'},
                                {value: '', text: 'Semua'}
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'jadw_tanggal_status',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
					
                ]);
        });
		
		function confirmDelete() {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-success mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Menghapus Data ?`,
                text: "Menghapus data bersifat permanen dan tidak dapat di kembalikan",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
					var idData = []; 
					var data = $('#ttData').datagrid('getData');
					var opts = $('#ttData').datagrid('options');
					for (var i = 0; i < data.rows.length; i++) {
						var tr = opts.finder.getTr($('#ttData')[0],i);
						var atLeastOneIsChecked = tr.find('input[type=checkbox]:checked').length > 0;
						if(atLeastOneIsChecked == true){
							idData.push(data.rows[i].jadw_id);
						}
					}
                    $.ajax({
                        url: `{{url("$url/delete")}}`,
						data: { 'ids[]': idData, 'tipe': 'data-jadwal' },
						type: 'DELETE',
                        success: function (response) {
                            toastCenter({
                                type: 'success',
                                title: response.message
                            })

                            let dg = $('#ttData');
                            dg.datagrid('reload');
                        },
                        error: function (err) {
                            if (err.responseJSON.message) {
                                toastCenter({
                                    type: 'error',
                                    title: err.responseJSON.message
                                })
                            }
                        }
                    });
                }
            });
        }
    </script>
@endpush
