@extends('layouts.layout_app')

@section('title', 'Persetujuan Pembatalan Permohonan')

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
                            <h3 class="dt-card__title">Data Permohonan Sertifikasi yang Diajukan Pelanggan untuk Dibatalkan</h3>
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
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid-permohonan") }}`,
                rownumbers: false,
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
                    let htmls = `<div style="padding: 20px 0 20px 0"><h4>Keterangan Pembatalan</h4><ul>`;
                    htmls += `<li><p>Keterangan : ${row.mohon_cancel_reason}</p></li>`;
                    htmls += `<li><p>File : ${row.mohon_cancel_file}</p></li>`;

                    htmls += "</ul></div>"

                    return htmls
                },
				view: detailview,
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "",
                        width: 80,
                        align: 'center',
                        formatter: function (val, row) {
							let btnDetail = '';
							btnDetail = `<a href="{{url("$url/detail")}}/${row.mohon_id}?action=detail-permohonan" class="btn btn-primary btn-xs btn-block"><i class="fas fa-badge-check"></i> Detail</a>`;
							
                            return `@if(authorized("{$module}@detail")) ${btnDetail} @endif`;
                        }
                    }
                ]],
                columns: [[
                    {field: 'mohon_id', title: 'No.<br/>Permohonan', width: 120, sortable: true},
                    {field: 'created_at', title: 'Tgl Pengajuan', width: 150, sortable: true},
                    {field: 'mohon_cust_nama', title: 'Nama Perusahaan', width: 320, sortable: true},
                    {field: 'sert_nama', title: 'Nama Sertifikasi', width: 320, sortable: true},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'sert_nama', type: 'textbox'},
                ]);
        });
		
		function confirmVerif(mohon_id) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-success mb-2',
                cancelButtonClass: 'btn btn-warning mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Detail Permohonan?`,
                text: `Apakah anda ingin men-setujui pembatalan permohonan untuk permohonan ini? (NB: Mengubah status menjadi 'Setuju Pembatalan' bersifat permanen dan tidak dapat di kembalikan)`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Diterima',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
					window.location.href = `{{url("$url")}}/proses_cancel/${mohon_id}`;
                }
            });
        }
    </script>
@endpush
