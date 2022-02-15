@extends('layouts.layout_app')

@section('title', 'Rekomendasi Persetujuan')

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
                            <h3 class="dt-card__title">Input Rekomendasi Jadwal Audit Tahap II</h3>
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
    <script src="{{asset('assets/plugins/easyui/datagrid-detailview.js')}}"></script>
    <script>
        $(function () {
            let dg = $('#ttData').datagrid({
                view: detailview,
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid-jadwal-audit") }}`,
                rownumbers: false,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
                pagination: false,
                clientPaging: false,
                detailFormatter: function (index, row) {
                    let status_message          = "-";

                    if (row.lap_lengkp_id){
						status_message = `-`;
					} else {
						
						status_message = `Belum bisa mengisikan rekomendasi, karena laporang lengkap belum diisi, atau belum diverifikasi!!`;
					}

                    return `
                    <div style="padding: 20px 0 20px 0">
                        <h5>Status :</h5>
                        <p>${status_message}</p>
                    </div>`;
                },
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "<br/><br/><br/>",
                        width: 100,
                        align: 'center',
                        formatter: function (val, row) {
							let btnPreview   = `<a target="blank_" href="{{ url("$url/edit") }}?tipe=lihat-rekomendasi&jadw_id=${row.jadw_id}" class="btn btn-xs btn-primary btn-block"><i class="fas fa-pdf"></i> Preview</a>`
                            let btnBuat     = `<a href="{{ url("$url/edit") }}?tipe=rekomendasi&jadw_id=${row.jadw_id}" class="btn btn-xs btn-primary btn-block"><i class="fas fa-plus"></i> Input</a>`;
							if(row.lap_lengkp_id){
								if (row.rekmd_komte_status != 'on-going') {
									return btnPreview
								} else {
									return btnBuat
								}
							}
							else {
								return ``;
							}
                            
                        }
                    }
                ]],
                columns: [[
					{field: 'jadw_id', title: 'No.<br>Jadwal', width: 100, sortable: true, align: 'left',},
					{field: 'jadw_jenis', title: 'Jenis<br>Jadwal', width: 120, sortable: true, align: 'left',},
                    {field: 'cust_nama', title: 'Nama pelanggan', width: 200, sortable: true},
                    {field: 'jadw_audit_jenis', title: 'Jenis Audit', width: 150, sortable: true},
                    {field: 'sert_nama', title: 'Sertifikasi', width: 250, sortable: true},
                    {field: 'jadw_tanggal_mulai', title: 'Tanggal<br/>Mulai', width: 100, sortable: true},
                    {field: 'jadw_tanggal_selesai', title: 'Tanggal<br/>Selesai', width: 100, sortable: true},
                ]],
				onBeforeLoad: function () {
                   
                },
                onLoadSuccess: function (data) {
                    
                },
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'jadw_jenis', type: 'label'},
                ]);
        });
    </script>
@endpush
