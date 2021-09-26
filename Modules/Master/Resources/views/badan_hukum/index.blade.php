@extends('layouts.layout_app')

@section('title', 'Badan Hukum')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Data master badan hukum</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div id="ttData" style="width:100%; min-width: 310px"></div>
                        <div id="toolbar" style="padding: 10px 0 10px 5px">
                            @if(authorized("{$module}@create"))
                                <div>
                                    <a href="{{ url("$url/create") }}" class="btn btn-outline-success btn-xs">
                                        <i class="fas fa-plus"></i> Create
                                    </a>
                                </div>
                            @endif
                            &nbsp;&nbsp;
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
                url: `{{ url("$url/ajax?action=datagrid") }}`,
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
                    {
                        field: 'action',
                        title: "Aksi",
                        width: 80,
                        align: 'center',
                        formatter: function (val, row) {
                            let btnEdit = `<a href="{{url("$url/edit")}}/${row.badan_hukum_id}" class="btn btn-primary btn-xs btn-block">Edit</a>`;
                            let btnDelete = `<button class="btn btn-danger btn-xs btn-block" onclick="confirmDelete('${row.badan_hukum_id}', '${row.badan_hukum_nama}')">Delete</button>`;
                            let output = "";

                            @if(authorized("{$module}@edit"))
                                output += btnEdit
                            @endif
                                @if(authorized("{$module}@destroy"))
                                output += btnDelete
                            @endif


                                return output;
                        }
                    }
                ]],
                columns: [[
                    {field: 'badan_hukum_nama', title: 'Badan Hukim', width: 220, sortable: true},
                    {field: 'created_at', title: 'Tgl Buat', width: 120, sortable: true},
                    {field: 'updated_at', title: 'Tgl Ubah', width: 120, sortable: true},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'badan_hukum_nama', type: 'textbox'},
                    {field: 'created_at', type: 'textbox'},
                    {field: 'updated_at', type: 'textbox'},
                ]);
        });

        function confirmDelete(badanHukumId, badanHukumNama) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-success mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Menghapus '${badanHukumNama}' ?`,
                text: "Menghapus data bersifat permanen dan tidak dapat di kembalikan",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `{{url("$url/delete")}}/${badanHukumId}`,
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
