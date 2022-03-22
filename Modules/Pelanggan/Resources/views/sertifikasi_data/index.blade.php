@extends("layouts.layout_app")

@section('title', 'Data Sertifikasi')

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

                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Data
                                Sertifikat {{auth()->user()->sis_pelanggan?->cust_nama}}</h3>
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
                url: `{{ url("$url/ajax?action=datagrid") }}`,
                rownumbers: true,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
                pagination: true,
                pageSize: 50,
                clientPaging: false,
                detailFormatter: function (index, row) {
                    let komoditasNama = "-";
                    let tipe          = "-";
                    let merk          = "-";

                    if (row.komodt_nama != null) komoditasNama = row.komodt_nama;
                    if (row.cust_sert_tipe != null) tipe = row.cust_sert_tipe;
                    if (row.cust_sert_merk != null) merk = row.cust_sert_merk;

                    return `
                    <div style="padding: 20px 0 20px 0">
                        <h4>Komoditas</h4>
                        <ul>
                            <li>Nama Komoditas : ${komoditasNama}</li>
                            <li>Tipe : ${tipe}</li>
                            <li>Merk : ${merk}</li>
                        </ul>
                    </div>`;
                },
                // frozenColumns: [[
                //     {
                //         field: 'action',
                //         title: "Aksi",
                //         width: 100,
                //         align: 'center',
                //         formatter: function (val, row) {
                //             return ``;
                //         }
                //     }
                // ]],
                columns: [[
                    {
                        field: 'cust_sert_status', title: 'Status', width: 150, sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'on_going':
                                    return "Aktif"
                                case 'expired':
                                    return "Kadaluarsa"
                                case 'dibekukan':
                                    return "Dibekukan"
                            }
                        }
                    },
                    {field: 'cust_sert_nomor_referensi', title: 'No Ref', width: 220, sortable: true},
                    {field: 'cust_sert_nomor_sertifikat', title: 'No Sertifikat', width: 220, sortable: true},
                    {field: 'cust_sert_nomor_sni', title: 'No SNI', width: 220, sortable: true},
                    {field: 'cust_sert_expired_date', title: 'Tgl <br> Kadaluarsa', width: 150, sortable: true},
                    {
                        field: 'cust_sert_filepath',
                        title: 'Sertifikat',
                        width: 150,
                        sortable: true,
                        formatter: function (val) {
                            if (val != "" && val != null) {
                                return `<a href="${val}"><i class="fad fa-download"></i> Download</a>`
                            }
                        }
                    },
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'cust_sert_filepath', type: 'label'},
                    {
                        field: 'cust_sert_status',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'on_going', text: 'Aktif'},
                                {value: 'expired', text: 'Kadaluarsa'},
                                {value: 'dibekukan', text: 'Dibekukan'}
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'cust_sert_status',
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
