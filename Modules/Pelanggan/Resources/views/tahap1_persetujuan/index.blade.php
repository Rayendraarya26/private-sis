@extends("layouts.layout_app")

@section('title', 'Persetujuan Temuan Tahap 1')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
                <div class="dt-card">
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            {!! implode('', $errors->all('<li>:message</li>')) !!}
                        </div>
                    @endif
                    @if(session('message'))
                        <div class="alert alert-success" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="dt-card__body">
                        <div id="ttData" style="width:100%; min-width: 310px"></div>
                    </div>
                </div>
            </div>
        </div>

        @include("$view._index_approve")

        @include("$view._index_revisi")
    </div>
@endsection

@push("javascript")
    <script>
        function confirmTahap1(id) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-success mb-2',
                cancelButtonClass: 'btn btn-danger mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Konfirmasi Tahap 1 ?`,
                html: `keputusan ini bersifat permanen <br><br> tekan ESC untuk batal`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Setuju',
                cancelButtonText: 'Revisi',
                closeOnConfirm: false,
                closeOnCancel: false,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    approveModal(id);
                } else if (result.dismiss === swal.DismissReason.cancel) {
                    revisionModal(id);
                }
            });
        }

        function approveModal(id) {
            $("#approve_aud_thp1_id").val(id)
            $("#modalApprove").modal('show')
        }

        function revisionModal(id) {
            $("#revision_aud_thp1_id").val(id)
            $("#modalRevisi").modal('show')
        }

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
                            let dom        = `dropdownMenu_${row.aud_thp1_id}`;
                            let btnDetail  = `<div data-options="iconCls:'fad fa-info-circle'" onclick="location.href = '{{url("$url/detail")}}/${row.aud_thp1_id}'">Detail</div>`;
                            let btnCetakLap   = `<div data-options="iconCls:'fad fa-print'" onclick="window.open('{{url("$url/cetak")}}/${row.aud_thp1_id}/laporan', '_blank')">Laporan</div>`;
                            let btnCetakTinjauan   = `<div data-options="iconCls:'fad fa-print'" onclick="window.open('{{url("$url/cetak")}}/${row.aud_thp1_id}/tinjauan', '_blank')">Hasil Tinjauan</div>`;
                            let btnCetakNotulen   = `<div data-options="iconCls:'fad fa-print'" onclick="window.open('{{url("$url/cetak")}}/${row.aud_thp1_id}/notulen', '_blank')">Notulen Rapat</div>`;
                            let btnApprove = `<div data-options="iconCls:'fad fa-check-circle'" onclick="confirmTahap1('${row.aud_thp1_id}')">Persetujuan</div>`;

                            if (row.aud_thp1_status_temuan !== "diajukan") {
                                btnApprove = "";
                            }

                            return `
                        <div>
                        <button class="btn-action btn-info" data-index="${row.aud_thp1_id}" title="Aksi">
                            <i class="fa fa-setting"></i> Aksi
                        </button>
                            <div id="${dom}" style="width:150px; display: none;">
                            @if(authorized("{$module}@detail")) ${btnDetail} @endif
                            @if(authorized("{$module}@approveTemuan")) ${btnApprove} @endif
                            @if(authorized("{$module}@cetakLaporan")) ${btnCetakLap} @endif
                            @if(authorized("{$module}@cetakTinjauan")) ${btnCetakTinjauan} @endif
                            @if(authorized("{$module}@cetakNotulen")) ${btnCetakNotulen} @endif
                            </div>`;
                        }
                    }
                ]],
                columns: [[
                    {
                        field: 'aud_thp1_status_temuan', title: 'Temuan', width: 200, sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'proses':
                                    return 'Proses';
                                case 'diajukan':
                                    return 'Diajukan';
                                case 'revisi':
                                    return 'Revisi';
                                case 'setuju':
                                    return 'Setuju';
                            }
                        },
                        styler: function (val) {
                            switch (val) {
                                case 'proses':
                                    return 'color:black;background-color:#f57f17;';
                                case 'setuju':
                                    return 'color:white;background-color:#2e7d32;';
                                case 'revisi':
                                    return 'color:white;background-color:#e65100;';
                            }
                        }
                    },
                    {
                        field: 'sert_tahap1_jenis', title: 'Jenis', width: 200, sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'pusat':
                                    return 'Pusat';
                                case 'sni':
                                    return 'SNI';
                            }
                        }
                    },
                    {field: 'tanggal', title: 'Tanggal Pelaksanaan', width: 200, sortable: true},
                    {
                        field: 'tims', title: 'Tim Auditor', width: 200, sortable: true,
                        formatter: function (val) {
                            let htmls = ""
                            if (val.length > 0) {
                                htmls += `<ul>`
                                val.map(e => {
                                    htmls += `
                                    <li>
                                        <b>${e.posisi}</b> <br> ${e.nama} (${e.kode})
                                    </li>`
                                })
                                htmls += `</ul>`
                            }

                            return htmls
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
                            menu: '#dropdownMenu_' + data.rows[idx].aud_thp1_id
                        });
                    });
                },
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'tanggal', type: 'label'},
                    {
                        field: 'sert_tahap1_jenis',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'pusat', text: 'Pusat'},
                                {value: 'sni', text: 'SNI'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'sert_tahap1_jenis',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                    {
                        field: 'aud_thp1_status_temuan',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'proses', text: 'Pusat'},
                                {value: 'diajukan', text: 'Diajukan'},
                                {value: 'revisi', text: 'Revisi'},
                                {value: 'setuju', text: 'Setuju'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'aud_thp1_status_temuan',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                ]);
        });
    </script>
@endpush
