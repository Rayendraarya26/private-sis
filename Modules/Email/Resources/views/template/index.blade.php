@extends('layouts.layout_app')

@section('title', 'Template Email')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Master template untuk email terjadwal</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div id="ttData" style="width:100%; min-width: 310px"></div>
                        <div id="toolbar" style="padding: 10px 0 10px 5px">
                            <div>
                                <a href="{{ url("$url/create") }}" class="btn btn-outline-success btn-xs">
                                    <i class="fas fa-plus"></i> Create
                                </a>
                            </div>
                            &nbsp;&nbsp;
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
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
                // fitColumns: true,
                toolbar: '#toolbar',
                pagination: true,
                pageSize: 50,
                clientPaging: false,
                frozenColumns: [[
                    {
                        field: 'action', title: "Aksi", width: 80, align: 'center', formatter: function (val, row) {
                            let btnPreview = `<button class="btn btn-warning btn-xs btn-block" onclick="preview(${row.template_id})">Preview</button>`;
                            let btnEdit = `<a href="{{url("$url/edit")}}/${row.template_uuid}" class="btn btn-primary btn-xs btn-block">Edit</a>`;
                            let btnDelete = `<button class="btn btn-danger btn-xs btn-block" onclick="confirmDelete('${row.template_uuid}', '${row.template_code}')">Delete</button>`;
                            return `${btnPreview} ${btnEdit} ${btnDelete}`;
                        }
                    }
                ]],
                columns: [[
                    {field: 'template_code', title: 'Kode', width: 200, sortable: true},
                    {field: 'template_desc', title: 'Deskripsi', width: 200, sortable: true},
                    {field: 'template_mail_subject', title: 'Title', width: 200, sortable: true},
                    {field: 'template_created_at', title: 'Tgl Buat', width: 120, sortable: true},
                    {field: 'template_updated_at', title: 'Tgl Ubah', width: 120, sortable: true},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'template_code', type: 'textbox'},
                    {field: 'template_subject', type: 'textbox'},
                    {field: 'template_desc', type: 'textbox'},
                ]);
        });

        function preview(templateId) {
            $.get(`{{url("$url/preview")}}?template_id=${templateId}`)
                .then(response => {
                    console.log(response);
                    $("#preview-title").html(response.results.template_mail_subject)
                    $("#preview-content").html(response.results.template_mail_body)
                })
                .fail(err => {
                    alert(err)
                })
            $("#preview-modal").modal('show')
        }

        function confirmDelete(templateUuid, templateCode) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-success mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Menghapus '${templateCode}' ?`,
                text: "Menghapus data bersifat permanen dan tidak dapat di kembalikan",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `{{url("$url/delete")}}/${templateUuid}`,
                        type: 'DELETE',
                        success: function (response) {
                            toastCenter({
                                type: 'success',
                                title: response.message
                            })

                            $('#ttData').datagrid('reload');
                        },
                        error: function (err) {
                            if (err.responseJSON.message) {
                                toastCenter({
                                    type: 'error',
                                    title: err.responseJSON.message
                                })
                            }
                        }
                    });
                }
            });
        }
    </script>
@endpush
