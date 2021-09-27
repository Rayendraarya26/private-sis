@extends('layouts.layout_app')

@section('title', 'Detail Klausul Tahap I Sertifikasi')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
				<a class="btn btn-sm btn-default"
                   href="{{url("$url")}}"
                   style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali ke Data Sertifikasi
                </a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Detail Klausul Tahap I Sertifikasi "{{$data->sert_nama}}"</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div id="ttData" style="width:100%; min-width: 310px;"></div>
                        <div id="toolbar" style="padding: 10px 0 10px 20px">
							<div class="row">
								@if(authorized("{$module}@create"))
									<div>
										<a href="{{ url("$url/create?tipe=create-sertifikasi-klausul-tahap1&sert_id=$data->sert_id") }}" class="btn btn-outline-success btn-xs">
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
                url: `{{ url("$url/ajax?action=datagrid-sertifikasi-klausul-tahap1") }}`,
				queryParams: {
					sert_id: `{{$data->sert_id}}`,
				},
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
							let btnEdit = `<a href="{{url("$url/edit?tipe=edit-sertifikasi-klausul-tahap1")}}&sert_id={{$data->sert_id}}&klausul_thp1_id=${row.klausul_thp1_id}" class="btn btn-primary btn-xs btn-block">Edit</a>`;
							let output = "";

                            @if(authorized("{$module}@edit"))
                                output += btnEdit
                            @endif
							return output;
                        }
                    }
                ]],
                columns: [[
                    {field: 'klausul_thp1_nomor', title: 'No.', width: 140, sortable: true},
                    {field: 'klausul_thp1_peryataan', title: 'Pernyataan', width: 400, sortable: true},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'klausul_thp1_nomor', type: 'label'},
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
							idData.push(data.rows[i].klausul_thp1_id);
						}
					}
                    $.ajax({
                        url: `{{url("$url/delete")}}`,
						data: { 'ids[]': idData, 'tipe' : 'delete-sertifikasi-klausul-tahap1', sert_id: `{{$data->sert_id}}` },
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
