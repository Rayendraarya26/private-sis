@extends('layouts.layout_app')

@section('title', 'Rapat Akhir')

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
                            <h3 class="dt-card__title">Rapat Akhir & Notulen</h3>
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
				rowStyler:function(index,row){
					if (row.jadw_setujui_temuan == 'revisi'){
						return 'background-color:#fff4b3;color:red;font-weight:normal;';
					}
				},
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "<br/><br/>",
                        width: 80,
                        align: 'center',
                        formatter: function (val, row) {
                            if (row.jadw_setujui_temuan == 'revisi') {
                                return `<a href="{{url("$url/unggah")}}/${row.jadw_id}" class="btn btn-xs btn-warning"><i class="fas fa-upload"></i> Revisi</a>`
                            } else {
                                return `<a href="{{url("$url/unggah")}}/${row.jadw_id}" class="btn btn-xs btn-success"><i class="fas fa-upload"></i> Ajukan</a>`
                            }
                        },
                    },
                ]],
                columns: [[
                    {field: 'jadw_id', title: 'No.<br/>Jadwal', width: 120, sortable: true},
                    {field: 'cust_nama', title: 'Nama pelanggan', width: 200, sortable: true},
                    {field: 'jadw_setujui_temuan', title: 'Persetujuan<br/>Temuan?', width: 100, sortable: true},
                    {field: 'sert_nama', title: 'Jadwal Detail', width: 300, sortable: true},
                    {field: 'jadw_tanggal_mulai', title: 'Tanggal<br/>Mulai', width: 100, sortable: true},
                    {field: 'jadw_tanggal_selesai', title: 'Tanggal<br/>Selesai', width: 100, sortable: true},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'total_jadwal', type: 'label'},
					{
                        field: 'jadw_setujui_temuan',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'revisi', text: 'Revisi'},
                                {value: 'diajukan', text: 'Diajukan'}
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'jadw_setujui_temuan',
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
