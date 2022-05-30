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
                                        <i class="fas fa-plus"></i> Ajukan Permohonan
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
                            let btnApproveHarga = `<div data-options="iconCls:'fad fa-check-circle'" onclick="confirmHarga('${row.mohon_id}', ${row.mohon_harga_permohonan})">Approve Harga</div>`;
                            let btnCancel = `<div data-options="iconCls:'fad fa-cancel'" onclick="location.href = '{{url("$url/cancel")}}/${row.mohon_id}'">Pembatalan</div>`;

                            if (row.mohon_approved_status !== "on-progress" &&
                                row.mohon_approved_status !== 'revisi' &&
                                row.mohon_approved_status !== 'fix' ) {
                                btnDelete = "";
                            }
                            if (row.mohon_approved_status !== "revisi") {
                                btnEdit = "";
                            }

                            if (row.mohon_tagihan_biaya_status == "proses" && (row.mohon_harga_permohonan == 0 || row.mohon_harga_permohonan == null)) {
                                btnApproveHarga = "";
                            } else if (row.mohon_tagihan_biaya_status != "proses") {
                                btnApproveHarga = "";
                            }

                            if (btnDelete != ""){
                                btnCancel = ""
                            }

                            console.log(btnCancel);

                            return `
                        <div>
                        <button class="btn-action btn-info" data-index="${row.mohon_id}" title="Aksi">
                            <i class="fa fa-setting"></i> Aksi
                        </button>
                        <div id="${dom}" style="width:150px; display: none;">
                            @if(authorized("{$module}@detail")) ${btnDetail} @endif
                            @if(authorized("{$module}@approveHarga")) ${btnApproveHarga} @endif
                            @if(authorized("{$module}@edit")) ${btnEdit} @endif
                            @if(authorized("{$module}@track")) ${btnTrack} @endif
                            <!-- <div class="menu-sep"></div> -->
                                @if(authorized("{$module}@cancel")) ${btnCancel} @endif
                                @if(authorized("{$module}@destroy")) ${btnDelete} @endif
                            </div>
                        </div>`;
                        }
                    }
                ]],
                columns: [[
                    {field: 'mohon_id', title: '#No<br>Pengajuan', width: 100, sortable: true, align: "center"},
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
                        field: 'mohon_tagihan_biaya_status',
                        title: 'Persetujuan <br> Biaya',
                        width: 120,
                        sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'proses':
                                    return "Proses";
                                case 'tidak':
                                    return "Ditolak";
                                case 'setuju':
                                    return "Disetujui";
                            }
                        },
                        styler: function (val) {
                            switch (val) {
                                case 'proses':
                                    return 'color:black;background-color:#f57f17;';
                                case 'tidak':
                                    return 'color:white;background-color:#b71c1c;';
                                case 'setuju':
                                    return 'color:white;background-color:#2e7d32;';
                            }
                        }
                    },
                    {
                        field: 'permohonan',
                        title: 'Data Permohonan',
                        width: 400,
                        sortable: false,
                        formatter: function (val, row) {
                            if (val != null) {
                                if (val.length > 0) {
                                    let htmls = "<ul>";
                                    val.map(e => {
                                        htmls += `<li>${e.sert_nama} <br><b>(<i>${e.mohon_det_jenis_status}</i>)</b></li>`
                                    })
                                    htmls += "</ul>"
                                    return htmls
                                }
                            }
                        },
                    },
                    {
                        field: 'mohon_harga_permohonan',
                        title: 'Biaya Sertifikasi',
                        width: 150,
                        sortable: true,
                        align: 'right',
                        formatter: function (val, row) {
                            if (val) {
                                let signed = "";
                                @if(authorized("{$module}@detail"))
                                if (row.mohon_tagihan_biaya_status == "proses" && val > 0) {
                                    signed = `<span style="cursor:pointer;" onclick="confirmHarga(${row.mohon_id}, ${row.mohon_harga_permohonan})"><i class="fas fa-question"></i> Butuh Persetujuan</span>`
                                } else if (row.mohon_tagihan_biaya_status == "tidak") {
                                    signed = `<span style="color: red"><i class="fas fa-close"></i> Tolak</span>`
                                } else if (row.mohon_tagihan_biaya_status == "setuju") {
                                    signed = `<span style="color: green"><i class="fas fa-check"></i> Setuju</span>`
                                }
                                @endif

                                    return val.toString().formatUang(".") + `<br> ${signed}`
                            } else {
                                return 0
                            }
                        }
                    },
                    {field: 'created_at', title: 'Tgl Pengajuan', width: 150, sortable: true},
                    {
                        field: 'mohon_cancel_status',
                        title: 'Status <br> Pembatalan',
                        width: 120,
                        sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'process':
                                    return "Proses Pengajuan";
                                case 'no':
                                    return "Tidak";
                                case 'yes':
                                    return "Disetujui";
                            }
                        },
                        styler: function (val) {
                            switch (val) {
                                case 'process':
                                    return 'color:black;background-color:#f57f17;';
                                case 'yes':
                                    return 'color:white;background-color:#2e7d32;';
                            }
                        }
                    },
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
                            menu: '#dropdownMenu_' + data.rows[idx].mohon_id
                        });
                    });
                },
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'permohonan', type: 'label'},
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
                        field: 'mohon_tagihan_biaya_status',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'proses', text: 'Proses'},
                                {value: 'tidak', text: 'Ditolak'},
                                {value: 'setuju', text: 'Disetujui'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'mohon_tagihan_biaya_status',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                ]);
        });

        function reloadTable() {
            // Destroy MenuButton (rebuild onloadsuccess)
            let dg = $('#ttData');
            dg.datagrid('getPanel').find('.btn-action').each(function () {
                $(this).menubutton('destroy');
            })
            dg.datagrid('reload');
        }

        @if(authorized("{$module}@detail"))
        function confirmHarga(id, harga) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-success mb-2',
                cancelButtonClass: 'btn btn-danger mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Persetujuan Harga Rp${harga.toString().formatUang(".")} ?`,
                html: `Jika anda menolak maka proses pengajuan akan berhenti ditahap ini, keputusan ini bersifat permanen <br><br> tekan ESC untuk batal`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Setuju',
                cancelButtonText: 'Tolak',
                closeOnConfirm: false,
                closeOnCancel: false,
                reverseButtons: true
            }).then(async (result) => {
                if (result.value) {
                    result = await swalWithBootstrapButtons({
                        title: "Anda Yakin ?",
                        html: `Setujui harga sertifikasi <b>Rp${harga.toString().formatUang(".")}</b>.`,
                        type: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    })
                    if (result.value) {
                        await submitApproval(id, "setuju");
                        reloadTable()
                    }
                } else if (result.dismiss === swal.DismissReason.cancel) {
                    result = await swalWithBootstrapButtons({
                        title: "Anda Yakin ?",
                        html: `Tolak harga sertifikasi <b>Rp${harga.toString().formatUang(".")}</b> ? <br>Proses akan berhenti apabila anda menolak harga`,
                        type: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    })
                    if (result.value) {
                        await submitApproval(id, "tidak");
                        reloadTable()
                    }
                }
            });
        }

        function submitApproval(mohon_id, status) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `{{url("$url/approve-harga")}}`,
                    type: 'POST',
                    dataType: 'json',
                    data: {mohon_id, status},
                    success: function (response) {
                        toastCenter({
                            type: 'success',
                            title: response.message
                        })
                        resolve();
                    },
                    error: function (xhr) {
                        if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                        else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                        reject();
                    }
                });
            })
        }
        @endif

        function confirmDelete(id, nama) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-success mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Hapus Permohonan ?`,
                text: `Menghapus permohonan untuk sertifikat dengan no pengajuan "${id}" bersifat permanen dan tidak dapat di kembalikan`,
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
