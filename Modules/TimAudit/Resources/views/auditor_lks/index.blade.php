@extends('layouts.layout_app')

@section('title', 'LKS')

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
                            <h3 class="dt-card__title">Data Jadwal Audit dan LKS</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div id="ttData" style="width:100%; min-width: 310px"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalRekomendasi" tabindex="-1" role="dialog"
             aria-labelledby="modalRekomendasi" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">

                <!-- Modal Content -->
                <div class="modal-content">

                @csrf
                <!-- Modal Header -->
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalRekomendasiTitle">
                            Rekomendasi LKS
                        </h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- /modal header -->

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <label for="rekomendasi_ket">Tuliskan Rekomendasi</label>
                        <textarea name="rekomendasi_ket" id="rekomendasi_ket" cols="30" rows="10"></textarea>
                    </div>
                    <!-- /modal body -->

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button id="simpanRekomendasi" type="button" onclick="processRekomendasi()"
                                class="btn btn-success btn-sm">
                            Simpan
                        </button>
                    </div>
                    <!-- /modal footer -->
                </div>
                <!-- /modal content -->
            </div>
        </div>
    </div>
@endsection

@push("javascript")
    <script src="https://cdn.tiny.cloud/1/hb65btdze8ubxfoabqu7fqjpuzpmx0c4k0je5f883m4l9ajf/tinymce/5/tinymce.min.js">
    </script>
    <script>
        let selectedRekomendasiJadwalID = 0;

        @if(authorized("{$module}@processRekomendasi"))
        function buildTinyMCERekomendasi() {
            tinyMCE.init({
                autosave_ask_before_unload: false,
                invalid_elements: "script",
                selector: '#rekomendasi_ket',
                plugins: 'autosave link image lists',
                relative_urls: false,
                height: 300,
                placeholder: 'Tuliskan Keterangan Rekomendasi...',
                images_reuse_filename: true,
                automatic_uploads: true,
                images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
                images_upload_credentials: true,
                toolbar: [
                    {name: 'history', items: ['undo', 'redo']},
                    {name: 'styles', items: ['styleselect']},
                    {name: 'formatting', items: ['bold', 'italic']},
                    {name: 'alignment', items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']},
                    {name: 'list', items: ['bullist', 'numlist']},
                    {name: 'indentation', items: ['outdent', 'indent']},
                    {name: 'link', items: ['link', 'image']},
                    {name: 'restore', items: ['restoredraft']},
                ],
            });
        }

        function promptRekomendasi(jadwalID) {
            selectedRekomendasiJadwalID = jadwalID;
            $("#modalRekomendasi").modal('show');
            setTimeout(() => {
                buildTinyMCERekomendasi()
                tinymce.get('rekomendasi_ket').setContent('');
            }, 300);
        }

        function processRekomendasi() {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-success mb-2',
                cancelButtonClass: 'btn btn-warning mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Simpan ?`,
                text: "Ketika anda klik simpan, proses audit telah selesai dan sistem akan mengirimkan notfikasi ke operator LS untuk membuat tim komite. Anda yakin ?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(async (result) => {
                if (result.value) {
                    $("#simpanRekomendasi").attr('disabled', true);
                    let content = tinymce.get('rekomendasi_ket').getContent();
                    await submitRekomendasi(selectedRekomendasiJadwalID, content)

                    $('#ttData').datagrid('reload');
                    $("#modalRekomendasi").modal('hide')
                    $("#simpanRekomendasi").removeAttr('disabled')
                }
            });
        }

        async function submitRekomendasi(jadwalId, rekomendasi) {
            return new Promise((resolve, reject) => {
                let formData = {
                    rekomendasi,
                    jadw_id: jadwalId,
                }
                $.post(`{{url("$url/temuan")}}/${jadwalId}/rekomendasi`, formData)
                    .then(response => {
                        toastCenter({
                            type: 'success',
                            title: response.message
                        })
                        resolve()
                    })
                    .fail((xhr) => {
                        if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                        else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                        reject();
                    });
            })
        }

        @endif

        $(function () {
            let dg = $('#ttData').datagrid({
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid-jadwal-audit") }}`,
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
                        width: 130,
                        align: 'center',
                        formatter: function (val, row) {
                            let btnTemuan      = "";
                            let btnVerif       = "";
                            let btnPreview     = "";
                            let btnRekomendasi = "";
                            if (row.jadw_setujui_temuan == "none" || row.jadw_setujui_temuan == "revisi") {
                                @if(authorized("{$module}@temuan"))
                                if (row.total_temuan > 0) {
                                    btnTemuan = `<a href="{{url("$url/temuan")}}/${row.jadw_id}" class="btn btn-xs btn-warning btn-block">(${row.total_temuan}) Temuan LKS</a>`
                                } else {
                                    btnTemuan = `<a href="{{url("$url/temuan")}}/${row.jadw_id}" class="btn btn-xs btn-success btn-block"><i class="fas fa-check"></i> Temuan LKS</a>`
                                }
                                @endif
                            } else if (row.jadw_setujui_temuan == "setuju") {
                                @if(authorized("{$module}@cetak"))
                                    btnPreview = `<a href="{{url("$url/cetak")}}/${row.jadw_id}/lks" target="_blank" class="btn btn-xs btn-danger btn-block"><i class="fas fa-file-pdf"></i>  Preview LKS</a>`
                                @endif

                                //
                                @if(authorized("{$module}@verifikasiTemuan"))
                                if (!row.allow_rekomendasi) {
                                    btnVerif = `<a href="{{url("$url/temuan")}}/${row.jadw_id}/verifikasi" class="btn btn-xs btn-primary btn-block">(${row.total_temuan}) Verifikasi LKS</a>`
                                }
                                @endif

                                //
                                @if(authorized("{$module}@processRekomendasi"))
                                if (row.allow_rekomendasi) {
                                    btnRekomendasi = `<a href="javascript:void(0)" onclick="promptRekomendasi(${row.jadw_id})" class="btn btn-xs btn-success btn-block"><i class="fas fa-plus"></i> Rekomendasi</a>`
                                }
                                @endif
                            }

                            return btnPreview + btnTemuan + btnVerif + btnRekomendasi;
                        },
                    },
                ]],
                columns: [[
                    {field: 'jadw_setujui_temuan', title: 'Pengajuan ?', width: 200, sortable: true},
                    {field: 'cust_nama', title: 'Nama pelanggan', width: 200, sortable: true},
                    {field: 'jadw_jenis', title: 'Jenis Jadwal', width: 150, sortable: true},
                    {
                        field: 'total_jadwal', title: 'Jadwal', width: 80, sortable: true,
                        formatter: function (val) {
                            return val + " Jadwal";
                        },
                    },
                    {field: 'sert_nama', title: 'Sertifikasi', width: 250, sortable: true},
                    {field: 'jadw_tanggal_mulai', title: 'Tanggal<br/>Mulai', width: 100, sortable: true},
                    {field: 'jadw_tanggal_selesai', title: 'Tanggal<br/>Selesai', width: 100, sortable: true},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'total_jadwal', type: 'label'},
                    {field: 'jadw_audit_jenis', type: 'label'},
                ]);
        });
    </script>
@endpush
