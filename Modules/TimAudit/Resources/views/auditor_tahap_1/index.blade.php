@extends('layouts.layout_app')

@section('title', 'Audit Tahap 1')

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
@endsection

@push("javascript")
    <script>
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
                        title: "<br><br>",
                        width: 80,
                        align: 'center',
                        formatter: function (val, row) {
							let dom = `dropdownMenu_${row.jadw_audit_id}`;
                            let btnEdit = ``;	
							if(row.jadw_audit_status != 'on-going'){
								btnEdit += `<div data-options="iconCls:'fas fa-edit'" onclick="location.href = '{{ url("$url/edit") }}?tipe=audit-tahap1&jadw_audit_id=${row.jadw_audit_id}'">Revisi Audit</div>`;
								btnEdit += `<div data-options="iconCls:'fas fa-print'" onclick="window.open('{{ url("$url/print") }}?tipe=hasil-tinjauan&jadw_audit_id=${row.jadw_audit_id}')">Hasil Tinjauan</div>`;
								btnEdit += `<div data-options="iconCls:'fas fa-print'" onclick="window.open('{{ url("$url/print") }}?tipe=audit-tahap1&jadw_audit_id=${row.jadw_audit_id}')">Laporan Tahap 1</div>`;
							}
							else {
								btnEdit += `<div data-options="iconCls:'fas fa-edit'" onclick="location.href = '{{ url("$url/edit") }}?tipe=audit-tahap1&jadw_audit_id=${row.jadw_audit_id}'">Input Audit</div>`;
							}
							
                            return `
								<div>
									<button class="btn-action btn-info btn-block" data-index="${row.jadw_audit_id}" title="Aksi">
										<i class="fa fa-setting"></i> Aksi
									</button>
									<div id="${dom}" style="width:150px; display: none;">
										@if(authorized("{$module}@edit")) ${btnEdit} @endif
								</div>
							</div>`
                        }
                    }
                ]],
                columns: [[
                    {field: 'jadw_id', title: 'No.<br>Jadwal', width: 150, sortable: true, align: 'left',
						formatter: function (val, row) {
                            return `${row.jadw_id}`
                        }
					},
                    {field: 'cust_nama', title: 'Nama pelanggan', width: 200, sortable: true},
                    {field: 'jadw_audit_jenis', title: 'Jenis Audit', width: 150, sortable: true},
                    {field: 'sert_nama', title: 'Sertifikasi', width: 250, sortable: true},
                    {field: 'jadw_tanggal_mulai', title: 'Tanggal<br/>Mulai', width: 100, sortable: true},
                    {field: 'jadw_tanggal_selesai', title: 'Tanggal<br/>Selesai', width: 100, sortable: true},
                    {field: 'jadw_audit_status', title: 'Status<br/>Audit', width: 100, sortable: true},
                ]],
				onLoadSuccess: function (data) {
                    $(this).datagrid('getPanel').find('.btn-action').each(function (idx, row) {
                        $(this).menubutton({
                            menu: '#dropdownMenu_' + data.rows[idx].jadw_audit_id
                        });
                    });
                },
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
					{
                        field: 'jadw_audit_status',
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
                                    field: 'jadw_audit_status',
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
