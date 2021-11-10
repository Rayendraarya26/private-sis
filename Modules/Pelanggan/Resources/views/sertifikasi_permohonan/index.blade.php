@extends("layouts.layout_app")

@section('title', 'Permohonan Sertifikasi')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Data Pengajuan Permohonan Sertifikasi</h3>
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
    <script src="{{asset('assets/plugins/easyui/datagrid-detailview.js')}}"></script>
    <script>
        $(function () {
            let dg = $('#ttData').datagrid({
                method: 'get',
                view: detailview,
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
                detailFormatter: function (index, row) {
                    let htmls = `<div style="padding: 20px 0 20px 0"><h4>Revisi</h4><ul>`;
                    if (row.revisi.length > 0) {
                        row.revisi.map(e => {
                            htmls += `<li>${e.status_judul}: ${e.status_pesan} <br><i>${e.created_at}</i></li>`;
                        })
                    }

                    htmls += "</ul></div>"

                    return htmls
                },
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "Aksi",
                        width: 80,
                        align: 'center',
                        formatter: function (val, row) {
                            let dom       = `dropdownMenu_${row.mohon_id}`;
                            let btnDetail = `<div data-options="iconCls:'fad fa-info-circle'" onclick="location.href = '{{url("$url/detail")}}/${row.mohon_id}'">Detail</div>`;
                            let btnEdit   = `<div data-options="iconCls:'fad fa-edit'" onclick="location.href = '{{url("$url/edit")}}/${row.mohon_id}'">Edit</div>`;
                            let btnDelete = `<div data-options="iconCls:'fad fa-trash'" onclick="confirmDelete('${row.mohon_id}', '${row.sert_nama}')">Delete</div>`;
                            let btnTrack  = `<div data-options="iconCls:'fad fa-flag-checkered'" onclick="location.href = '{{url("$url/track")}}/${row.mohon_id}'">Lacak</div>`;

                            if (row.mohon_approved_status !== "on-progress") {
                                btnDelete = "";
                                if (row.mohon_approved_status !== "revisi") {
                                    btnEdit = "";
                                }
                            }


                            return `
                        <div>
                        <button class="btn-action btn-info" data-index="${row.mohon_id}" title="Aksi">
                            <i class="fa fa-setting"></i> Aksi
                        </button>
                        <div id="${dom}" style="width:150px; display: none;">
                            @if(authorized("{$module}@detail")) ${btnDetail} @endif
                            @if(authorized("{$module}@edit")) ${btnEdit} @endif
                            @if(authorized("{$module}@track")) ${btnTrack} @endif
                            <!-- <div class="menu-sep"></div> -->
                                @if(authorized("{$module}@destroy")) ${btnDelete} @endif
                            </div>
                        </div>`;
                        }
                    }
                ]],
                columns: [[
                    {
                        field: 'mohon_approved_status',
                        title: 'Status <br> Permohonan',
                        width: 120,
                        sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'on-progress':
                                    return "Proses";
                                case 'rejected':
                                    return "Ditolak";
                                case 'accepted':
                                    return "Disetujui";
                                case 'revisi':
                                    return "Revisi";
                                case 'fix':
                                    return "Perbaikan Revisi";
                            }
                        },
                        styler: function (val) {
                            switch (val) {
                                case 'on-progress':
                                    return 'color:black;background-color:#f57f17;';
                                case 'rejected':
                                    return 'color:white;background-color:#b71c1c;';
                                case 'accepted':
                                    return 'color:white;background-color:#2e7d32;';
                                case 'revisi':
                                    return 'color:white;background-color:#e65100;';
                                case 'fix':
                                    return 'color:black;background-color:#4caf50;';
                            }
                        }
                    },
                    {
                        field: 'mohon_jenis_status',
                        title: 'Jenis Permohonan <br> Sertifikat',
                        width: 150,
                        sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'lama':
                                    return "Lama";
                                case 'baru':
                                    return "Baru";
                            }
                        }
                    },
                    {field: 'sert_nama', title: 'Nama Sertifikasi', width: 220, sortable: true},
                    {field: 'created_at', title: 'Tgl Pengajuan', width: 220, sortable: true},
                ]],
                onLoadSuccess: function (data) {
                    $(this).datagrid('getPanel').find('.btn-action').each(function (idx, row) {
                        $(this).menubutton({
                            menu: '#dropdownMenu_' + data.rows[idx].mohon_id
                        });
                    });
                },
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'sert_nama', type: 'textbox'},
                    {
                        field: 'mohon_approved_status',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'on-progress', text: 'Proses'},
                                {value: 'rejected', text: 'Ditolak'},
                                {value: 'accepted', text: 'Disetujui'},
                                {value: 'revisi', text: 'Revisi'},
                                {value: 'fix', text: 'Perbaikan Revisi'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'mohon_approved_status',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                    {
                        field: 'mohon_jenis_status',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'lama', text: 'Lama'},
                                {value: 'baru', text: 'Baru'}
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'mohon_jenis_status',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                ]);
        });

        function confirmDelete(id, nama) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-success mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Hapus Permohonan ?`,
                text: `Menghapus permohonan untuk sertifikat "${nama}" bersifat permanen dan tidak dapat di kembalikan`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `{{url("$url/delete")}}`,
                        type: 'DELETE',
                        dataType: 'json',
                        data: {mohon_id: id},
                        success: function (response) {
                            toastCenter({
                                type: 'success',
                                title: response.message
                            })

                            // Destroy MenuButton (rebuild onloadsuccess)
                            let dg = $('#ttData');
                            dg.datagrid('getPanel').find('.btn-action').each(function () {
                                $(this).menubutton('destroy');
                            })
                            dg.datagrid('reload');
                        },
                        error: function (xhr) {
                            if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                            else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                        }
                    });
                }
            });
        }
    </script>
@endpush
