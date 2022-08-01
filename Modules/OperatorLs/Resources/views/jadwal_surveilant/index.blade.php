@extends('layouts.layout_app')

@section('title', 'Jadwal Surveilant')

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
                            <h3 class="dt-card__title">Jadwal Surveilant yang akan datang</h3>
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
        function promptNotif(custSertId) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-success mb-2',
                cancelButtonClass: 'btn btn-danger mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Notifikasi Keuangan ?`,
                text: "Kirim notifikasi (email & push notif) mengenai pembuatan billing untuk surveilant",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Kirim',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    notifApi(custSertId)
                }
            });
        }

        function notifApi(custSertId) {
            let formData = new FormData();
            formData.append('cust_sert_id', custSertId)
            $.ajax({
                url: `{{action("$module@reminderFinance")}}`,
                type: 'post',
                processData: false,
                contentType: false,
                data: formData,
                success: async function (res) {
                    toastCenter({
                        type: 'success',
                        title: res.message
                    })

                    $('#ttData').datagrid('reload');
                },
                error: function (xhr) {
                    self.loading_submit = false;
                    if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                    else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                }
            });
        }

        $(function () {
            let dg = $('#ttData').datagrid({
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid") }}`,
                rownumbers: false,
                nowrap: false,
                singleSelect: true,
                remoteFilter: true,
                multiSort: true,
                // fitColumns: true,
                toolbar: '#toolbar',
                pagination: true,
                pageSize: 50,
                clientPaging: false,
                frozenColumns: [[
                        @if(authorized("$module@reminderFinance"))
                    {
                        field: 'action',
                        title: "Aksi",
                        width: 140,
                        align: 'center',
                        formatter: function (val, row) {
                            if (!row.is_bill_created) {
                                if (row.cust_sert_survailen_reminder_internal_count == 0) {
                                    return `<button onclick="promptNotif(${row.cust_sert_id})" class="btn btn-block btn-sm"><i class="fas fa-bell"></i> Kirim</button>`
                                } else {
                                    return `<button onclick="promptNotif(${row.cust_sert_id})" class="btn btn-block btn-sm btn-warning"><i class="fas fa-bell"></i> (${row.cust_sert_survailen_reminder_internal_count}) Kirim Ulang</button>`
                                }
                            }
                        }
                    }
                    @endif
                ]],
                columns: [[
                    {
                        field: 'is_bill_created', title: 'Billing', width: 100, sortable: false,
                        formatter: function (val) {
                            return val ? "Terbuat" : "Belum";
                        },
                        styler: function (val) {
                            if (val) {
                                return 'color:white;background-color:#2e7d32;';
                            } else {
                                return 'color:black;background-color:#FAAD14;';
                            }
                        }
                    },
                    {field: 'cust_nama', title: 'Nama Perusahaan', width: 320, sortable: false},
                    {field: 'sert_nama', title: 'Sertifikasi', width: 320, sortable: false},
                    {field: 'cust_sert_survailen_date', title: 'Tgl Surveilant', width: 150, sortable: true},
                    {field: 'cust_alamat', title: 'Alamat Perusahaan', width: 320, sortable: false},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'sert_nama', type: 'textbox'},
                    {
                        field: 'is_bill_created',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'yes', text: 'Terbuat'},
                                {value: 'no', text: 'Belum'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'is_bill_created',
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
