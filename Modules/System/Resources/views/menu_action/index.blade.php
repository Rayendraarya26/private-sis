@extends('layouts.layout_app')

@section('title', 'Menu Action')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
                <a class="btn btn-sm btn-default" href="{{ url("/system/menu") }}" style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali
                </a>

                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Menu Action</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div id="dgTable" style="width:100%;min-width: 310px"></div>
                        <div id="toolbar" style="padding: 10px 0 10px 20px">
                            <div class="row">
                                <div>
                                    <button onclick="addrow()" class="btn btn-outline-success btn-xs">
                                        <i class="fas fa-plus"></i> Tambah
                                    </button>
                                </div>
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
            let dg = $('#dgTable').edatagrid({
                method: 'get',
                height: document.documentElement.scrollHeight - 350,
                url: `{{ url("$url/ajax/datagrid") }}`,
                saveUrl: `{{url("$url")}}`,
                updateUrl: `{{url("$url/update")}}`,
                rownumbers: true,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
                toolbar: '#toolbar',
                pagination: true,
                pageSize: 50,
                clientPaging: false,
                columns: [[
                    {
                        field: 'action', title: 'Aksi', align: 'center', sortable: false, width: 100,
                        formatter: function (value, row) {
                            let cancel = `<button class="btn btn-xs btn-danger btn-block" onclick="cancelrow(this)"><i class="fad fa-cancel"></i> Cancel</button>`;
                            let save = `<button class="btn btn-xs btn-success btn-block" onclick="saverow(this)"><i class="fad fa-save"></i> Simpan</button>`;
                            let edit = `<button class="btn btn-xs btn-primary btn-block" onclick="editrow(this)"><i class="fad fa-edit"></i> Ubah</button>`;
                            let del = `<button class="btn btn-xs btn-danger btn-block" onclick="remove(${row.action_id}, '${row.action_name}')"><i class="fad fa-trash"></i> Hapus</button>`;

                            let output = "";
                            if (row.editing) {
                                output += save + cancel;
                            } else {
                                @if(authorized("{$module}@update"))
                                    output += edit
                                @endif

                                    @if(authorized("{$module}@destroy"))
                                    output += del
                                @endif
                            }

                            return output
                        }
                    },
                    {
                        field: 'action_name',
                        title: 'Nama',
                        width: 100,
                        sortable: true,
                        editor: {
                            type: 'textbox',
                            options: {
                                readonly: false,
                                required: false
                            },
                        }
                    },
                    {
                        field: 'action_controller',
                        title: 'Lokasi Controller',
                        width: 600,
                        sortable: true,
                        editor: {
                            type: 'textbox',
                            options: {
                                readonly: false,
                                required: false
                            },
                        }
                    },
                    {
                        field: 'action_created_at',
                        title: 'Tgl Dibuat',
                        width: 200,
                        sortable: true,
                    },
                ]],
                onBeforeEdit: function (index, row) {
                    row.editing = true;
                    if (!row.action_controller) {
                        row.action_controller = 'Modules\\NAMA_MODULE\\Http\\Controllers\\NAMA_CONTROLLER@NAMA_FUNGSI';
                    }
                    $(this).datagrid('refreshRow', index);
                },
                onAfterEdit: function (index, row) {
                    row.editing = false;
                    $(this).datagrid('refreshRow', index);
                },
                onCancelEdit: function (index, row) {
                    row.editing = false;
                    $(this).edatagrid('refreshRow', index);
                },
            });
            dg.edatagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'action_name', type: 'textbox'},
                    {field: 'action_controller', type: 'textbox'},
                    {field: 'action_created_at', type: 'label'},
                ]);
        });

        function addrow() {
            window.editingRow = 0;
            $('#dgTable').edatagrid('addRow', 0)
        }

        function getRowIndex(target) {
            let tr = $(target).closest('tr.datagrid-row');
            return parseInt(tr.attr('datagrid-row-index'));
        }

        function editrow(target) {
            window.editingRow = getRowIndex(target);
            $('#dgTable').edatagrid('beginEdit', getRowIndex(target));
        }

        function saverow(target) {
            let index = getRowIndex(target);
            // let row = $('#dgTable').edatagrid('getRows')[index];
            $('#dgTable').edatagrid('endEdit', index);
            $('#dgTable').edatagrid('reload');
        }

        function cancelrow(target) {
            $('#dgTable').edatagrid('cancelEdit', getRowIndex(target));
        }

        function remove(id, nama) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-success mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Menghapus '${nama}' ?`,
                text: "Menghapus data bersifat permanen dan tidak dapat di kembalikan",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `{{ url("$url") }}/${id}`,
                        type: 'DELETE',
                        success: function (response) {
                            console.log(response)
                            $('#dgTable').edatagrid('reload');
                        },
                        fail: function (error) {
                            console.log(error)
                        }
                    });
                }
            });
        }


        function active(status) {
            let rows = $('#dgTable').edatagrid('getSelections');
            if (rows.length > 0) {
                let agree = status === "no" ? confirm(`@lang('system::menu_action.confirm_deactive')`) : confirm(`@lang('system::menu_action.confirm_active')`);
                if (agree) {
                    let dataId = [];
                    rows.map(e => {
                        dataId.push(e.group_id);
                    });
                    let formData = {ids: dataId, status};
                    $.ajax({
                        url: `{{ url("$url/ajax/active") }}`,
                        data: formData,
                        type: 'POST',
                        success: function (response) {
                            console.log(response)
                            $('#dgTable').edatagrid('reload');
                        },
                        fail: function (error) {
                            console.log(error)
                        }
                    });
                }
            }
        }
    </script>
@endpush
