@extends('layouts.layout_app')

@section('title', 'Master Sertifikasi')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Data Master Sertifikasi</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div id="ttData" style="width:100%; min-width: 310px;"></div>
                        <div id="toolbar" style="padding: 10px 0 10px 20px">
							<div class="row">
								@if(authorized("{$module}@create"))
									<div>
										<a href="{{ url("$url/create?tipe=create-sertifikasi") }}" class="btn btn-outline-success btn-xs">
											<i class="fas fa-plus"></i> Tambah
										</a>
									</div>
									&nbsp;&nbsp;&nbsp;
								@endif							
								@if(authorized("{$module}@destroy"))
									<div class="datagrid-btn-separator"></div>
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
                url: `{{ url("$url/ajax?action=datagrid-sertifikasi") }}`,
                rownumbers: true,
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
							let dom = `dropdownMenu_${row.sert_id}`;
                            let btnEdit = `<div data-options="iconCls:'fad fa-edit'" onclick="location.href = '{{ url("$url/edit?tipe=edit-sertifikasi") }}&sert_id=${row.sert_id}'">Edit</div>`;
                            let btnDetail = `<div data-options="iconCls:'fad fa-folder'" onclick="location.href = '{{ url("$url/detail?tipe=detail-klausul-tahap1") }}&sert_id=${row.sert_id}'">Klausul Tahap 1</div>
											<div data-options="iconCls:'fad fa-folder'" onclick="location.href = '{{ url("$url/detail?tipe=detail-klausul") }}&sert_id=${row.sert_id}'">Klausul</div>
											<div data-options="iconCls:'fad fa-folder'" onclick="location.href = '{{ url("$url/detail?tipe=detail-dokumen") }}&sert_id=${row.sert_id}'">Dokumen</div>
											`;

                            return `
								<div>
									<button class="btn-action btn-info btn-block" data-index="${row.sert_id}" title="Aksi">
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
                    {field: 'sert_nama', title: 'Sertifikasi', width: 400, sortable: true},
                    {field: 'sert_expired', title: 'Masa<br/>Berlaku(Tahun)', width: 140, sortable: true},
                    {field: 'sert_format_referensi', title: 'Format<br/>Nomor<br/>Referensi', width: 200, sortable: true},
                ]],
				onLoadSuccess: function (data) {
                    $(this).datagrid('getPanel').find('.btn-action').each(function (idx, row) {
                        $(this).menubutton({
                            menu: '#dropdownMenu_' + data.rows[idx].sert_id
                        });
                    });
                },
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'sert_expired', type: 'label'},
                    {field: 'sert_format_referensi', type: 'label'},
                    {field: 'sert_nama', type: 'textbox'},
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
							idData.push(data.rows[i].sert_id);
						}
					}
                    $.ajax({
                        url: `{{url("$url/delete")}}`,
						data: { 'ids[]': idData },
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
