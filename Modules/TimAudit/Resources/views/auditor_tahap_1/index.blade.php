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
                        title: "<br><br><br>",
                        width: 80,
                        align: 'center',
                        formatter: function (val, row) {
							let dom = `dropdownMenu_${row.aud_thp1_id}`;
                            let btnEdit = ``;	
							if(row.aud_thp1_status_temuan == 'proses'){
								btnEdit += `<div data-options="iconCls:'fas fa-edit'" onclick="location.href = '{{ url("$url/edit") }}?tipe=audit-tahap1&aud_thp1_id=${row.aud_thp1_id}'">Input Tinjauan</div>`;
							}
							else{
								btnEdit += `<div data-options="iconCls:'fas fa-print'" onclick="window.open('{{ url("$url/print") }}?tipe=hasil-tinjauan&aud_thp1_id=${row.aud_thp1_id}')">Hasil Tinjauan</div>`;
							}
							
                            return `
								<div>
									<button class="btn-action btn-info btn-block" data-index="${row.aud_thp1_id}" title="Aksi">
										<i class="fa fa-setting"></i> Aksi
									</button>
									<div id="${dom}" style="width:170px; display: none;">
										@if(authorized("{$module}@edit")) ${btnEdit} @endif
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
                    {field: 'aud_thp1_status', title: 'Status<br/>Audit', width: 100, sortable: true},
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
                        field: 'aud_thp1_status_temuan',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            value: '',
                            data: [
                                {value: 'proses', text: 'Proses/Draft'},
                                {value: '', text: 'Semua'}
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
