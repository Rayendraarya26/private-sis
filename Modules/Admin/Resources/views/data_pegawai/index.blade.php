@extends('layouts.layout_app')

@section('title', 'Data Pegawai')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
                <div class="dt-card">
                    <div class="dt-card__body">
                        <div id="ttData" style="width:100%;min-width: 310px"></div>
                        <div id="toolbar" style="padding: 10px 0 10px 20px">
                            <div class="row">
                                @if(authorized("{$module}@create"))
                                    <div>
                                        <a href="{{ url("$url/create") }}" class="btn btn-outline-success btn-xs">
                                            <i class="fas fa-plus"></i> Create
                                        </a>
                                    </div>
                                @endif
                                &nbsp;&nbsp;&nbsp;
                                @if(authorized("{$module}@banned"))
                                    <div class="datagrid-btn-separator"></div>

                                    <div>
                                        <button class="btn btn-outline-danger btn-xs" onclick="banned('yes')">
                                            <i class="fas fa-ban"></i> Banned
                                        </button>
                                    </div>
                                    &nbsp;
                                    <div>
                                        <button class="btn btn-outline-danger btn-xs" onclick="banned('no')">
                                            <i class="fas fa-check"></i> Unbanned
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
                height: document.documentElement.scrollHeight - 250,
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
                    {field: 'ck', checkbox: true, sortable: false},
                    {
                        field: 'action', title: 'Aksi', sortable: false, width: 105, align: 'center',
                        formatter: function (value, row) {
                            let dom       = `dropdownMenu_${row.user_id}`;
                            let btnEdit   = `<div data-options="iconCls:'fad fa-edit'" onclick="location.href = '{{ url("$url") }}/edit/${row.user_id}'">Edit</div>`;
                            let btnDelete = `<div data-options="iconCls:'fad fa-trash'" onclick="remove(${row.user_id}, '${row.user_email}')">Delete</div>`;

                            return `
                            <div>
                                <button class="btn-action btn-info btn-block" data-index="${row.user_id}" title="Aksi">
                                    <i class="fa fa-setting"></i> Aksi
                                </button>
                                <div id="${dom}" style="width:150px; display: none;">
                                    @if(authorized("{$module}@edit")) ${btnEdit} @endif
                            @if(authorized("{$module}@destroy")) ${btnDelete} @endif
                            </div>
                        </div>`;
                        }
                    },
                ]],
                columns: [[

                    {field: 'user_fullname', title: 'Fullname', width: 200, sortable: true},
                    {field: 'user_email', title: 'Email', width: 200, sortable: true},
                    {
                        field: 'user_picture', title: 'Foto', width: 70, align: 'center',
                        formatter: function (val) {
                            return `<img src="${val}" style="height: 20px">`
                        }
                    },
                    {field: 'user_is_active', title: 'Aktif ?', width: 80},
                    {field: 'user_last_login', title: 'Tgl Login', width: 200, sortable: true},
                    {field: 'user_created_at', title: 'Tgl Daftar', width: 200, sortable: true},
                    {field: 'user_is_banned', title: 'Banned ?', width: 80},
                    {field: 'user_banned_at', title: 'Tgl Banned', width: 200, sortable: true},
                ]],
                onLoadSuccess: function (data) {
                    $(this).datagrid('getPanel').find('.btn-action').each(function (idx, row) {
                        $(this).menubutton({
                            menu: '#dropdownMenu_' + data.rows[idx].user_id
                        });
                    });
                },
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'ck', type: 'label'},
                    {field: 'action', type: 'label'},
                    {field: 'user_picture', type: 'label'},
                    {field: 'user_last_login', type: 'label'},
                    {field: 'user_created_at', type: 'label'},
                    {
                        field: 'user_is_active',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'yes', text: 'Ya'},
                                {value: 'no', text: 'Tidak'}
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'user_is_active',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                    {
                        field: 'user_is_banned',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'yes', text: 'Ya'},
                                {value: 'no', text: 'Tidak'}
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'user_is_banned',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                ]);
        });

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
                        url: `{{ url("$url/delete") }}/${id}`,
                        type: 'DELETE',
                        success: function (response) {
                            console.log(response)
                            $('#ttData').datagrid('reload');
                        },
                        fail: function (error) {
                            console.log(error)
                        }
                    });
                }
            });
        }

        @if(authorized("{$module}@banned"))
        function banned(status) {
            let rows = $('#ttData').datagrid('getSelections');
            if (rows.length > 0) {
                let agree = status === "no" ? confirm(`Anda yakin untuk membuka banned ?`) : confirm(`Anda yakin untuk melakukan banned ?`);
                if (agree) {
                    let dataId = [];
                    rows.map(e => {
                        dataId.push(e.user_id);
                    });
                    let formData = {ids: dataId, status};
                    $.ajax({
                        url: `{{ url("$url/banned") }}`,
                        data: formData,
                        type: 'POST',
                        success: function (response) {
                            console.log(response)
                            $('#ttData').datagrid('reload');
                        },
                        fail: function (error) {
                            console.log(error)
                        }
                    });
                }
            }
        }
        @endif
    </script>
@endpush
