@extends('layouts.layout_app')

@section('title', 'Outbox System (Automatic)')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Riwayat email keluar dikirim oleh sistem (otomatis)</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div id="ttData" style="width:100%; min-width: 310px"></div>
                        <div id="toolbar" style="padding: 10px 0 10px 20px">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Preview Email Modal -->
    <div class="modal fade" id="preview-modal" tabindex="-1" role="dialog" aria-labelledby="model-4"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">

            <!-- Modal Content -->
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h3 class="modal-title" id="preview-title">Loading...</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- /modal header -->

                <!-- Modal Body -->
                <div class="modal-body" id="preview-content">
                    <p>Sedang memuat data email...</p>
                </div>
                <!-- /modal body -->

            </div>
            <!-- /modal content -->

        </div>
    </div>
    <!-- /modal -->
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
                singleSelect: true,
                remoteFilter: true,
                multiSort: true,
                // fitColumns: true,
                toolbar: '#toolbar',
                pagination: true,
                pageSize: 50,
                clientPaging: false,
                frozenColumns: [[
                    {
                        field: 'action', title: "Aksi", width: 100, formatter: function (val, row) {
                            @if(authorized("$module/previewEmail"))
                                return `<button class="btn btn-warning btn-xs btn-block" onclick="preview(${row.outbox_id})">Preview</button>`;
                            @endif
                        }
                    }
                ]],
                columns: [[
                    {field: 'outbox_title', title: 'Title', width: 200, sortable: true},
                    {field: 'outbox_to_email', title: 'Email Penerima', width: 200},
                    {
                        field: 'outbox_read', title: 'Terbaca ?', width: 80,
                        formatter: function (val) {
                            if (val === "yes") {
                                return "Ya"
                            } else {
                                return "Tidak"
                            }
                        },
                        styler: function (val) {
                            if (val === "yes") {
                                return "background:green;color:white"
                            }
                        }
                    },
                    {field: 'outbox_read_at', title: 'Tgl Terbaca', width: 200, sortable: true},
                    {field: 'outbox_created_at', title: 'Tgl Terbuat', width: 200, sortable: true},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'outbox_to_name', type: 'textbox'},
                    {field: 'outbox_to_email', type: 'textbox'},
                    {
                        field: 'outbox_read',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'yes', text: 'Ya'},
                                {value: 'no', text: 'Tidak'}
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'outbox_read',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                ]);
        });

        function preview(outboxId) {
            $("#preview-title").html("Loading...")
            $("#preview-content").html("Sedang memuat data email...")
            $.get(`{{url("$url/preview")}}?outbox_id=${outboxId}`)
                .then(response => {
                    console.log(response);
                    $("#preview-title").html(response.results.outbox_title)
                    $("#preview-content").html(response.results.outbox_message)
                })
                .fail(err => {
                    alert(err)
                })
            $("#preview-modal").modal('show')
        }
    </script>
@endpush
