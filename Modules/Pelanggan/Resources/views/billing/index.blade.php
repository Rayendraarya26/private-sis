@extends("layouts.layout_app")

@section('title', 'Billing')

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
                            <h3 class="dt-card__title">Tagihan Pembayaran Sertifikasi</h3>
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
                // fitColumns: true,
                pagination: true,
                pageSize: 50,
                clientPaging: false,
                detailFormatter: function (index, row) {
                    let tgl_bayar = "-";
                    let kuitansi  = "<span style='color: orange'>Menunggu Pembayaran</span>";
                    let note      = "-";

                    if (row.bill_payment_date != null) {
                        tgl_bayar = row.bill_payment_date
                        kuitansi  = `<a href='${row.bill_payment_file}'><i class='fad fa-download'></i> Unduh Bukti Pembayaran</a>`;
                        note      = row.bill_payment_note;
                    }

                    return `
                    <div style="padding: 20px 0 20px 0">
                        <h4>Bukti Pembayaran</h4>
                        <ul>
                            <li>Tanggal Pembayaran : ${tgl_bayar}</li>
                            <li>Bukti Pembayaran : ${kuitansi}</li>
                            <li>Note : ${note}</li>
                        </ul>
                    </div>`;
                },
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "Aksi",
                        width: 100,
                        align: 'center',
                        formatter: function (val, row) {
                            if (row.bill_payment_status != 'lunas') {
                                return `<a href="{{url("$url/upload")}}/${row.bill_nomor_billing}" class="btn btn-xs btn-success"><i class="fad fa-upload"></i> Bukti Pembayaran </a>`
                            }
                        }
                    }
                ]],
                columns: [[
                    {
                        field: 'bill_payment_status',
                        title: 'Status <br> Pembayaran',
                        width: 120,
                        sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'menunggu konfirmasi':
                                    return "Menunggu Konfirmasi";
                                case 'menunggu pembayaran':
                                    return "Menunggu Pembayaran";
                                case 'lunas':
                                    return "Lunas";
                            }
                        }
                    },
                    {
                        field: 'bill_invoice_file',
                        title: 'Invoice',
                        width: 100,
                        sortable: true,
                        formatter: function (val) {
                            return `<a href="${val}" target="_blank"><i class="fad fa-download"></i> Invoice</a>`
                        }
                    },
                    {field: 'bill_billing_date', title: 'Tgl Billing', width: 220, sortable: true},
                    {field: 'bill_due_date', title: 'Tgl Jatuh Tempo', width: 220, sortable: true},
                    {
                        field: 'bill_items', title: 'Items', width: 400, sortable: false,
                        formatter: function (val) {
                            if (val.length > 0) {
                                let items = "<ul>";
                                val.map(e => {
                                    items += `<li>${e.itms_bil_tipe.toUpperCase()} <br>${e.itms_bil_desc} <br> <i>Rp${e.itms_bil_total.toString().formatUang('.')}<i></li>`
                                })
                                items += "</ul>";

                                return items;
                            }
                        }
                    },
                    {field: 'bill_nomor_billing', title: 'Nomor Billing', width: 150, sortable: true},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'bill_invoice_file', type: 'label'},
                    {field: 'bill_items', type: 'label'},
                    {
                        field: 'bill_payment_status',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'menunggu konfirmasi', text: 'Menunggu Konfirmasi'},
                                {value: 'menunggu pembayaran', text: 'Menunggu Pembayaran'},
                                {value: 'lunas', text: 'Lunas'}
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'bill_payment_status',
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
