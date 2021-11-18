@extends('layouts.layout_app')

@section('title', 'Penyusunan Komite Audit')

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
                            <h3 class="dt-card__title">Data Jadwal dan Penyusunan Komite Audit</h3>
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
                    {field: 'ck', checkbox: true, sortable: false},
                    {
                        field: 'action',
                        title: "Aksi",
                        width: 80,
                        align: 'center',
                        formatter: function (val, row) {
							let dom = `dropdownMenu_${row.jadw_id}`;
                            let btnEdit = ``;			
							btnEdit += `<div data-options="iconCls:'fad fa-edit'" onclick="location.href = '{{ url("$url/edit") }}?tipe=edit-tim&jadw_id=${row.jadw_id}'">Input Komite</div>`;
							
                            return `
								<div>
									<button class="btn-action btn-info btn-block" data-index="${row.jadw_id}" title="Aksi">
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
                    {field: 'jadw_id', title: 'No.<br>Jadwal', width: 100, sortable: true},
                    {field: 'cust_nama', title: 'Nama pelanggan', width: 200, sortable: true},
                    {field: 'jadw_audit_jenis', title: 'Jenis Audit', width: 100, sortable: true},
                    {field: 'sert_nama', title: 'Sertifikasi', width: 250, sortable: true},
                    {field: 'jadw_tanggal_mulai', title: 'Tanggal<br/>Mulai', width: 100, sortable: true},
                    {field: 'jadw_tanggal_selesai', title: 'Tanggal<br/>Selesai', width: 100, sortable: true},
                    {field: 'total_tim', title: 'Total<br/>Komite', width: 100, sortable: true},
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
                    {field: 'total_tim', type: 'label'},
                ]);
        });
    </script>
@endpush
