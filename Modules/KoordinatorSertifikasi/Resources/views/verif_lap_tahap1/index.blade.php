@extends('layouts.layout_app')

@section('title', 'Verifikasi Laporan Audit Tahap 1')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
				@if(session('message'))
					<div class="alert alert-primary alert-dismissible fade show" role="alert">
						{!! session('message') !!}
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
				@endif
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Data Jadwal Audit Tahap 1</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div id="ttData" style="width:100%; min-width: 310px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
	 @include("$view._index_approve")

	@include("$view._index_revisi")
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
                title: `Verifikasi Laporan Tahap 1 ?`,
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
                url: `{{ url("$url/ajax?action=datagrid-jadwal-audit") }}`,
                rownumbers: false,
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
                        title: "<br><br><br>",
                        width: 100,
                        align: 'center',
                        formatter: function (val, row) {
							let dom = `dropdownMenu_${row.aud_thp1_id}`;
                            let btnCetak = ``;	
							btnCetak += `<div data-options="iconCls:'fas fa-print'" onclick="window.open('{{ url("$url/cetak") }}?tipe=hasil-tinjauan&aud_thp1_id=${row.aud_thp1_id}')">Hasil Tinjauan</div>`;
							btnCetak += `<div data-options="iconCls:'fas fa-print'" onclick="window.open('{{ url("$url/cetak") }}?tipe=lap_lengkap&aud_thp1_id=${row.aud_thp1_id}')">Laporan Tahap 1</div>`;
							
                            return `
								<div>
									<a href="javascript:void(0)" class="btn btn-xs btn-warning btn-block" onClick="confirmTahap1('${row.aud_thp1_id}')"><i class="fas fa-check-square-o"></i> Verifikasi</a>
									<button class="btn-action btn-info btn-block " data-index="${row.aud_thp1_id}" title="Cetak">
										<i class="fa fa-setting"></i> Lihat
									</button>
									<div id="${dom}" style="width:200px; display: none;">
										${btnCetak}
								</div>
							</div>`
                        }
                    }
                ]],
                columns: [[
                    {field: 'aud_thp1_id', title: 'No.<br>Jadwal', width: 150, sortable: true, align: 'left',
						formatter: function (val, row) {
                            return `${row.aud_thp1_id}`
                        }
					},
                    {field: 'cust_nama', title: 'Nama pelanggan', width: 200, sortable: true},
                    {field: 'sert_nama', title: 'Sertifikasi', width: 250, sortable: true},
                    {field: 'aud_thp1_tanggal_mulai', title: 'Tanggal<br/>Mulai', width: 100, sortable: true},
                    {field: 'aud_thp1_tanggal_selesai', title: 'Tanggal<br/>Selesai', width: 100, sortable: true},
                    {field: 'aud_thp1_status', title: 'Status Audit', width: 150, sortable: true},
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
					{
                        field: 'aud_thp1_status',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            value: '',
                            data: [
                                {value: 'memenuhi', text: 'Memenuhi'},
                                {value: 'tidak-memenuhi', text: 'Tidak Memenuhi'},
                                {value: '', text: 'Semua'}
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'aud_thp1_status',
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
