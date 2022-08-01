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
                            <br>
                            <div class="alert alert-info pt-1" role="alert" id="noteInvoiceExpired" style="display: none">
                                Jika invoice telah kadaluarsa dan anda <b>sudah membayar</b>, silakan <b>upload bukti pembayaran</b>
                                <br>
                                Jika invoice telah kadaluarsa dan anda <b>belum membayar</b>, silakan <b>chat admin untuk meminta Invoice Baru</b>
                            </div>
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
                    let kuitansi = "<span style='color: orange'>Menunggu Pembayaran</span>";
                    let note = "-";

                    if (row.bill_payment_date != null) {
                        tgl_bayar = row.bill_payment_date
                        kuitansi = `<a target="_blank" href='${row.bill_payment_file}'><i class='fad fa-download'></i> Unduh Bukti Pembayaran</a>`;
                        note = row.bill_payment_note;
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
                        width: 120,
                        align: 'center',
                        formatter: function (val, row) {
                            if (row.bill_payment_status != 'lunas') {
                                let btnColor = 'btn-success';
                                if (row.bill_payment_file != null) {
                                    btnColor = "btn-warning"
                                }
                                let btnUpload = `<a href="{{url("$url/upload")}}/${row.bill_id}" class="btn btn-xs ${btnColor} btn-block"><i class="fad fa-upload"></i> Bukti Pembayaran </a>`
                                let btnChatAdmin = '';
                                if (row.is_bill_expired && row.bill_payment_status == "menunggu pembayaran") {
                                    btnChatAdmin = `<a target="_blank" href="https://api.whatsapp.com/send?phone=628112827821&text=Selamat Siang Admin BBKKP, saya dari ${row.cust_nama} invoice saya untuk pembayaran dengan nomor ${row.bill_nomor_billing} telah expired, apakah saya bisa mendapatkan invoice yang baru ?" class="btn btn-xs btn-success btn-block"><i class="fab fa-whatsapp"></i> Chat Admin (Inv Expired)</a>`;
                                }
                                return btnUpload + btnChatAdmin
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
                    {field: 'bill_billing_date', title: 'Tgl<br>Billing', width: 100, sortable: true},
                    {
                        field: 'bill_due_date', title: 'Tgl<br>Jatuh Tempo', width: 140, sortable: true,
                        formatter: function (val, row){
                            let text = val;
                            if(row.is_bill_expired){
                                text += '<br> Invoice Kadaluarsa'
                            }
                            return text
                        },
                        styler: function (val, row) {
                            if(row.is_bill_expired && row.bill_payment_status != 'lunas'){
                                return 'color:white;background-color:#D41818;';
                            }
                        }
                    },
                    {
                        field: 'bill_items', title: 'Items', width: 400, sortable: false,
                        formatter: function (val) {
                            if (val.length > 0) {
                                let items = "<ul>";
                                val.map(e => {
                                    items += `<li>${e.itms_bil_tipe.toUpperCase()} <br>${e.itms_bil_desc}</li>`
                                    /* <br> <i>Rp${e.itms_bil_total.toString().formatUang('.')}</i> */
                                })
                                items += "</ul>";

                                return items;
                            }
                        }
                    },
                    {field: 'bill_nomor_billing', title: 'Nomor Billing', width: 150, sortable: true},
                ]],
                onLoadSuccess: function (data) {
                    if(data.rows.length > 0){
                        data.rows.map(e => {
                            if (e.is_bill_expired){
                                $("#noteInvoiceExpired").removeAttr('style')
                            }
                        })
                    }
                },
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
