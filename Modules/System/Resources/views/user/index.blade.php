@extends('layouts.layout_app')

@section('title', 'Manage User')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Manage User</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div id="ttData" style="width:100%;min-width: 310px"></div>
                        <div id="toolbar" style="padding: 10px 0 10px 20px">
                            <div class="row">
                                <div>
                                    <a href="{{ url("$module/create") }}" class="btn btn-outline-success btn-xs">
                                        <i class="fas fa-plus"></i> Create
                                    </a>
                                </div>
                                &nbsp;&nbsp;
                                <div class="datagrid-btn-separator"></div>
                                &nbsp;
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
                url: `{{ url("$module/ajax/datagrid") }}`,
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
                            return `
                            <a class="btn btn-primary btn-xs" href="{{ url("$module") }}/${row.user_id}/edit">Edit</a>
                            <button class="btn btn-danger btn-xs"
                                    onclick="remove(${row.user_id}, '${row.user_email}')">Delete
                            </button>
                        `;
                        }
                    },
                ]],
                columns: [[

                    {field: 'user_fullname', title: 'Username', width: 200, sortable: true},
                    {field: 'user_email', title: 'Email', width: 200, sortable: true},
                    {field: 'user_is_active', title: 'Aktif ?', width: 80},
                    {field: 'user_is_banned', title: 'Banned ?', width: 80},
                    {
                        field: 'user_picture', title: 'Foto', width: 70, align: 'center',
                        formatter: function (val) {
                            return `<img src="${val}" style="width: 50px">`
                        }
                    },
                    {field: 'user_last_login', title: 'Tgl Login', width: 200, sortable: true},
                    {field: 'user_created_at', title: 'Tgl Daftar', width: 200, sortable: true},
                ]],
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
            console.log(nama)
            let agree = confirm(`Anda yakin untuk menghapus ${nama}`);
            if (agree) {
                $.ajax({
                    url: `{{ url("$module") }}/${id}`,
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
        }


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
                        url: `{{ url("$module/ajax/banned") }}`,
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
    </script>
@endpush
