@extends("layouts.layout_app")

@section('title', 'Tahap 1')

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
        function promptRevisi(id) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-default mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: 'Keterangan Revisi',
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Revisi',
                cancelButtonText: 'Batal',
                closeOnConfirm: false,
                closeOnCancel: false,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    submitApproval(id, 'revisi', result.value)
                }
            });
        }

        function promptAgree(id) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-success mb-2',
                cancelButtonClass: 'btn btn-danger mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: 'Setujui Temuan ?',
                html: `Keputusan ini bersifat permanen dan tidak dapat dikembalikan<br><br> tekan ESC untuk batal`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
                closeOnConfirm: false,
                closeOnCancel: false,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    submitApproval(id, 'setuju', null)
                }
            });
        }

        function submitApproval(id, status, message) {
            $.ajax({
                url: `{{url("$url/approve/temuan")}}`,
                type: 'POST',
                dataType: 'json',
                data: {jadw_id: id, jadw_setujui_temuan: status, message},
                success: function (response) {
                    toastCenter({
                        type: 'success',
                        title: response.message
                    })

                    location.href = "/{{$url}}"
                },
                error: function (xhr) {
                    if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                    else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                }
            });
        }

        function confirmTemuan(id) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-success mb-2',
                cancelButtonClass: 'btn btn-danger mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Konfirmasi Temuan 1 ?`,
                html: `keputusan ini bersifat permanen <br><br> tekan ESC untuk batal`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Setuju',
                cancelButtonText: 'Tolak',
                closeOnConfirm: false,
                closeOnCancel: false,
                reverseButtons: true
            }).then((result) => {
                let status = null;
                if (result.value) {
                    promptAgree(id)
                } else if (result.dismiss === swal.DismissReason.cancel) {
                    promptRevisi(id)
                }
            });
        }

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
                            let btnTemuan  = `<a href="{{url("$url/detail")}}/${row.jadw_id}" class="btn btn-warning btn-block btn-xs">(${row.total_temuan}) Temuan LKS</a>`;
                            let btnApprove = `<button onclick="confirmTemuan(${row.jadw_id})" class="btn btn-primary btn-block btn-xs"><i class="fas fa-check-circle"></i> Approval</button>`;

                            if (row.jadw_setujui_temuan != "diajukan") {
                                btnApprove = "";
                            }
                            return btnTemuan + btnApprove
                        }
                    }
                ]],
                columns: [[
                    {
                        field: 'jadw_setujui_temuan', title: 'Status Persetujuan', width: 200, sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'diajukan':
                                    return 'Diajukan';
                                case 'setuju':
                                    return 'Setuju';
                                case 'revisi':
                                    return 'Revisi';
                            }
                        }
                    },
                    {field: 'tanggal', title: 'Tanggal Pelaksanaan', width: 200, sortable: true},
                    {
                        field: 'audits', title: 'Agenda', width: 200, sortable: true,
                        formatter: function (val) {
                            let htmls = ""
                            if (val.length > 0) {
                                htmls += `<ol>`
                                val.map(e => {
                                    htmls += `
                                    <li>
                                        <b>${e.jadw_audit_jenis}</b> <br> No. Sert: ${e.jadw_audit_nomor_sertifikat} <br> No. Ref: ${e.jadw_audit_nomor_referensi}
                                    </li>`
                                })
                                htmls += `</ol>`
                            }

                            return htmls
                        }
                    },
                    {
                        field: 'tims', title: 'Tim Auditor', width: 200, sortable: true,
                        formatter: function (val) {
                            let htmls = ""
                            if (val.length > 0) {
                                htmls += `<ol>`
                                val.map(e => {
                                    htmls += `
                                    <li>
                                        <b>${e.tim_posisi}</b> <br> ${e.tim_nama} (${e.tim_kode})
                                    </li>`
                                })
                                htmls += `</ol>`
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
                        field: 'jadw_setujui_temuan',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'diajukan', text: 'Diajukan'},
                                {value: 'setuju', text: 'Setuju'},
                                {value: 'revisi', text: 'Revisi'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'jadw_jenis',
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
