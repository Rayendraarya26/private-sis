@extends('layouts.layout_app')

@section('title', 'Billing')

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
                            <h3 class="dt-card__title">Data Billing</h3>
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
		function statusStyle(value,row,index){
            if (value == 'lunas'){
                return 'background-color:blue;color:white;';
            }
			else{
				return 'background-color:#ffee00;color:red;';
			}
        }
		
        $(function () {
            let dg = $('#ttData').datagrid({
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid-billing") }}`,
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
							let dom = `dropdownMenu_${row.bill_id}`;
                            let btnEdit = ``;
							let btnDetail = ``;
							if(row.bill_payment_status != 'lunas'){
								if(row.status_payment != 'ya'){
									btnEdit += `<div data-options="iconCls:'fad fa-edit'" onclick="location.href = '{{ url("$url/edit") }}?tipe=data&bill_id=${row.bill_id}'">Edit</div>`;
								}
								else{
									btnEdit += `<div data-options="iconCls:'fad fa-edit'" onclick="location.href = '{{ url("$url/edit") }}?tipe=pelunasan&bill_id=${row.bill_id}'">Pelunasan</div>`;
								}
							}
							else{
								if(row.jdwl_bill_id == 'belum'){
									btnEdit += `<div data-options="iconCls:'fad fa-edit'" onclick="confirmBelumLunas(${row.bill_id})">Set Belum Lunas</div>`;
								}
								btnDetail += `<div data-options="iconCls:'fad fa-folder-open'" onclick="location.href = '{{ url("$url/detail") }}?bill_id=${row.bill_id}'">Detail</div>`;
							}
							
							

                            return `
								<div>
									<button class="btn-action btn-info btn-block" data-index="${row.bill_id}" title="Aksi">
										<i class="fa fa-setting"></i> Aksi
									</button>
									<div id="${dom}" style="width:150px; display: none;">
										@if(authorized("{$module}@edit")) ${btnEdit} @endif
										@if(authorized("{$module}@detail")) ${btnDetail} @endif
								</div>
							</div>`
                        }
                    }
                ]],
                columns: [[
                    {field: 'bill_status_pembayaran', title: 'Sudah<br>Dibayar?', width: 100, sortable: true},
                    {field: 'bill_payment_status', title: 'Lunas ?', width: 100, sortable: true, styler:statusStyle},
                    {field: 'bill_nomor_billing', title: 'No.<br/>Billing', width: 120, sortable: true},
                    {field: 'bill_billing_date', title: 'Tanggal<br/>Billing', width: 100, sortable: true},
                    {field: 'bill_due_date', title: 'Jatuh<br/>Tempo', width: 100, sortable: true},
                    {field: 'cust_nama', title: 'Nama Perusahaan', width: 320, sortable: true},
                    {field: 'itms_bil_total', title: 'Total(Rp.)', width: 100, sortable: true},
                    {field: 'bill_harus_lunas', title: 'Harus<br/>Lunas?', width: 80, sortable: true, align:'center'}, 
                ]],
				onBeforeLoad: function () {
                    $(this).datagrid('getPanel').find('.btn-action').each(function (idx, row) {
                        try {
                            $(this).menubutton('destroy');
                        } catch (e) {
                            console.log('failed destroy');
                        }
                    });
                },
                onLoadSuccess: function (data) {
                    $(this).datagrid('getPanel').find('.btn-action').each(function (idx, row) {
                        $(this).menubutton({
                            menu: '#dropdownMenu_' + data.rows[idx].bill_id
                        });
                    });
					
					var opts = $(this).datagrid('options');
					for(var i=0; i<data.rows.length; i++){
						if (data.rows[i].can_delete == 'false'){
							var tr = opts.finder.getTr($(this)[0],i);
							tr.find('input[type=checkbox]').css('display','none');
							// $(this).datagrid('getExpander', i).hide();
						} 
					}
                },
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'itms_bil_total', type: 'label'},
                    {
                        field: 'bill_payment_status',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            value: '',
                            data: [
                                {value: 'menunggu pembayaran', text: 'menunggu pembayaran'},
                                {value: 'menunggu konfirmasi', text: 'menunggu konfirmasi'},
                                {value: 'lunas', text: 'Lunas'},
                                {value: '', text: 'Semua'}
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'bill_payment_status',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
					{
                        field: 'bill_status_pembayaran',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            value: '',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'belum', text: 'Belum'},
                                {value: 'sudah', text: 'Sudah'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'bill_status_pembayaran',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
					{
                        field: 'bill_harus_lunas',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            value: '',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'ya', text: 'ya'},
                                {value: 'tidak', text: 'tidak'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'bill_harus_lunas',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                ]);
        });
		
		function confirmBelumLunas(id) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-success mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Set Belum Lunas ?`,
                text: "Apakah anda yakin untuk men-set data ini menjadi belum lunas?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'OK',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
					$.ajax({
                        url: `{{url("$url/update")}}`,
						data: { 'bil_id': id, 'tipe':'reset-pelunasan' },
						type: 'POST',
                        success: function (response) {
                            toastCenter({
                                type: 'success',
                                title: response.message
                            })

                            $('#ttData').datagrid({url:`{{ url("$url/ajax?action=datagrid-billing") }}`});
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
							if (data.rows[i].can_delete == 'true'){
								idData.push(data.rows[i].bill_id);
							} 
							
						}
					}
                    $.ajax({
                        url: `{{url("$url/delete")}}`,
						data: { 'ids[]': idData, 'tipe': 'data_billing' },
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
