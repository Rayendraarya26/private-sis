@extends("layouts.layout_app")

@section('title', 'Jadwal Tahap 1')

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
    </div>
@endsection

@push("javascript")
    <script>
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
                        width: 120,
                        align: 'center',
                        formatter: function (val, row) {
                            return `<a href="{{url("$url/detail")}}/${row.aud_thp1_id}" class="btn btn-primary btn-block btn-xs"><i class="fas fa-info"></i> Detail</a>`
                        }
                    }
                ]],
                columns: [[
                    // {
                    //     field: 'aud_thp1_status_temuan', title: 'Temuan', width: 200, sortable: true,
                    //     formatter: function (val) {
                    //         switch (val) {
                    //             case 'proses':
                    //                 return 'Proses';
                    //             case 'diajukan':
                    //                 return 'Diajukan';
                    //             case 'revisi':
                    //                 return 'Revisi';
                    //             case 'setuju':
                    //                 return 'Setuju';
                    //         }
                    //     }
                    // },
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
                                    field: 'aud_thp1_status_temuan',
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
                                    field: 'sert_tahap1_jenis',
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
