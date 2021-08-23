@extends('layouts.layout_app')

@section('title', 'Manage Menu')

@push('css')
    <style>
        .fas,
        .fab,
        .fad,
        .fal,
        .far {
            background-image: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .fas::before,
        .fab::before,
        .fad::before,
        .fal::before,
        .far::before {
            font-size: 14px;
        }

        .fas.tree-folder:not(.tree-file)::before,
        .fab.tree-folder:not(.tree-file)::before,
        .fad.tree-folder:not(.tree-file)::before,
        .fal.tree-folder:not(.tree-file)::before,
        .far.tree-folder:not(.tree-file)::before {
            content: "\f105"
        }

        .fas.tree-folder-open:not(.tree-file)::before,
        .fab.tree-folder-open:not(.tree-file)::before,
        .fad.tree-folder-open:not(.tree-file)::before,
        .fal.tree-folder-open:not(.tree-file)::before,
        .far.tree-folder-open:not(.tree-file)::before {
            content: "\f107"
        }
    </style>
@endpush

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Manage Menu</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <table id="treegrid" style="width:100%;min-width: 310px"></table>
                        <div id="toolbar" style="padding: 10px 0 10px 20px">
                            <div class="row">
                                <div>
                                    <a href="{{ url("$url/create") }}" class="btn btn-outline-success btn-xs">
                                        <i class="fas fa-plus"></i> Tambah
                                    </a>
                                </div>
                                &nbsp;&nbsp;
                                <div class="datagrid-btn-separator"></div>
                                &nbsp;
                                @if(authorized("{$module}@ajaxActive"))
                                    <div>
                                        <button class="btn btn-outline-danger btn-xs" onclick="active('yes')">
                                            <i class="fas fa-check"></i> Aktif
                                        </button>
                                    </div>
                                    &nbsp;
                                    <div>
                                        <button class="btn btn-outline-danger btn-xs" onclick="active('no')">
                                            <i class="fas fa-ban"></i> Non Aktif
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
            $('#treegrid').treegrid({
                method: 'get',
                height: document.documentElement.scrollHeight - 250,
                url: `{{ url("$url/ajax/treegrid") }}`,
                idField: 'id',
                nowrap: false,
                singleSelect: false,
                toolbar: '#toolbar',
                // fitColumns: false,
                treeField: 'menu_name',
                columns: [[
                    {field: 'ck', checkbox: true, sortable: false},
                    {
                        field: 'menu_name', title: 'Nama Menu', width: 300, formatter: function (val, row) {
                            if (row.hasOwnProperty('children')) {
                                return `<i class="${row.menu_icon}">&nbsp;${val}</i>`
                            } else {
                                return val;
                            }

                        }
                    },
                    // {field: 'menu_desc', title: 'Deskripsi', width: 200},
                    {field: 'menu_order', title: 'Urutan', width: 100},
                    {field: 'menu_is_active', title: 'Aktif ?', width: 100},
                    {
                        field: 'action', title: 'Aksi', width: 200,
                        formatter: function (val, row) {
                            let btn_edit = `<a href="{{ url("$url") }}/${row.id}/edit" class="btn btn-xs btn-success">Edit</a>`;
                            let btn_action = `<a href="{{url("$url")}}/${row.id}/menu-action" class="btn btn-xs btn-primary">Menu Action</a>`;
                            let btn_delete = `<button class="btn btn-xs btn-danger" onclick="remove(${row.id}, '${row.menu_name}')">Delete</button>`;
                            let result = "";

                            @if(authorized("{$module}@edit"))
                                result += btn_edit + "&nbsp;"
                            @endif

                                @if(authorized("{$module}@destroy"))
                                result += btn_delete + "&nbsp;"
                            @endif

                                result += btn_action;
                            return result
                        }
                    }
                ]]
            });
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
                        url: `{{ url("$url") }}/${id}`,
                        type: 'DELETE',
                        success: function () {
                            $('#treegrid').treegrid('reload');
                        },
                        fail: function (error) {
                            console.log(error)
                        }
                    });
                }
            });
        }


        function active(status) {
            let rows = $('#treegrid').treegrid('getSelections');
            if (rows.length > 0) {
                let agree = status === "no" ? confirm(`Anda yakin menonaktifkan menu ini ?`) : confirm(`Anda yakin mengaktifkan menu ini ?`);
                if (agree) {
                    let dataId = [];
                    rows.map(e => {
                        dataId.push(e.id);
                    });
                    let formData = {ids: dataId, status};
                    $.ajax({
                        url: `{{ url("$url/ajax/active") }}`,
                        data: formData,
                        type: 'POST',
                        success: function (response) {
                            console.log(response)
                            $('#treegrid').treegrid('reload');
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
