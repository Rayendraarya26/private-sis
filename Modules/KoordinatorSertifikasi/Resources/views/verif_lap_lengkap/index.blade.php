@extends('layouts.layout_app')

@section('title', 'Verifikasi Laporan Lengkap')

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
                @error('message')
                <div class="alert alert-danger">
                    {{$message}}
                </div>
                @enderror
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Data Pengajuan Laporan Lengkap</h3>
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
                title: `Verifikasi Laporan Tahap 2 ?`,
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
            $("#approve_jadw_id").val(id)
            $("#modalApprove").modal('show')
        }

        function revisionModal(id) {
            $("#revision_jadw_id").val(id)
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
                        title: "Aksi",
                        width: 100,
                        align: 'center',
                        formatter: function (val, row) {
							let dom = `dropdownMenu_${row.jadw_id}`;
                            let btnCetak = ``;	
							btnCetak += `<div data-options="iconCls:'fas fa-print'" onclick="window.open('{{url("$url/cetak")}}/${row.jadw_id}/lap-lengkap')">Laporan Lengkap</div>`;
							
                            return `
								<div>
									@if(authorized("{$module}@detail"))<a href="javascript:void(0)" class="btn btn-xs btn-warning btn-block" onClick="confirmTahap1('${row.jadw_id}')"><i class="fas fa-check-square-o"></i> Verifikasi</a>@endif
									<button class="btn-action btn-info btn-block " data-index="${row.jadw_id}" title="Cetak">
										<i class="fa fa-setting"></i> Lihat
									</button>
									<div id="${dom}" style="width:200px; display: none;">
										@if(authorized("{$module}@cetak")) ${btnCetak} @endif
									</div>
								</div>`;
                        },
                    },
                ]],
                columns: [[
					{field: 'jadw_id', title: 'No.<br>Jadwal', width: 150, sortable: true, align: 'left',},
                    {field: 'cust_nama', title: 'Nama pelanggan', width: 200, sortable: true},
                    {field: 'jadw_audit_jenis', title: 'Jenis Audit', width: 150, sortable: true},
                    {field: 'sert_nama', title: 'Sertifikasi', width: 250, sortable: true},
                    {field: 'jadw_tanggal_mulai', title: 'Tanggal<br/>Mulai', width: 100, sortable: true},
                    {field: 'jadw_tanggal_selesai', title: 'Tanggal<br/>Selesai', width: 100, sortable: true},
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
                            menu: '#dropdownMenu_' + data.rows[idx].jadw_id
                        });
                    });
                },
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'sudah_mengisi', type: 'label'},
                    {field: 'logbook_filepath', type: 'label'},
                ]);
        });
    </script>
@endpush
