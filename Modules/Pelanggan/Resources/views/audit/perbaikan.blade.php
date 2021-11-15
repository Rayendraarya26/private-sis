@extends("layouts.layout_app")

@section('title', 'Detail Audit')

@push('css')
    <style>
        .ol-rekomendasi {
            counter-reset: item;
            list-style-type: none;
        }

        .ol-rekomendasi li:before {
            content: '6.' counter(item, decimal) '. ';
            counter-increment: item;
        }

        .ol-rekomendasi li {
            padding-bottom: 40px;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css"/>
@endpush

@section('content')
    <div class="dt-content" id="perbaikanPage">
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
                            <h3 class="dt-card__title">Laporan Ketidaksesuaian dan Laporan Verifikasi</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table">
                                    <tr>
                                        <td style="width: 50px">1</td>
                                        <td>Jenis Kegiatan</td>
                                        <td>: {{$data->sis_jadwal_audit->jadw_audit_kegiatan}}</td>
                                    </tr>

                                    <tr>
                                        <td rowspan="3">2</td>
                                        <td>Nama Perusahaan</td>
                                        <td>: {{$data->sis_jadwal_audit->sis_permohonan->mohon_cust_nama}}</td>
                                    </tr>
                                    <tr>
                                        <td>No. Referensi</td>
                                        <td>: {{$data->sis_jadwal_audit->jadw_audit_nomor_referensi}}</td>
                                    </tr>
                                    <tr>
                                        <td>Alamat</td>
                                        <td>: {{$data->sis_jadwal_audit->sis_permohonan->mohon_cust_alamat}}</td>
                                    </tr>

                                    <tr>
                                        <td>3</td>
                                        <td>Tanggal Asesmen</td>
                                        <td>
                                            : {{ $data->sis_jadwal_audit->sis_jadwal->jadw_tanggal_mulai->isoFormat("LL") }}
                                            s/d {{ $data->sis_jadwal_audit->sis_jadwal->jadw_tanggal_selesai->isoFormat("LL") }}</td>
                                    </tr>

                                    <tr>
                                        <td>4</td>
                                        <td>Tim Asesmen</td>
                                        <td>:
                                            <ol>
                                                @foreach($data->sis_jadwal_audit->sis_jadwal->sis_jadwal_tims as $tim)
                                                    <li>
                                                        {{$tim->master_pegawai->peg_nama}}
                                                        ({{ucwords($tim->jadw_tim_posisi)}})
                                                    </li>
                                                @endforeach
                                            </ol>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>5</td>
                                        <td>Standar Acuan</td>
                                        <td>: {{$data->sis_jadwal_audit->jadw_audit_standart_acuan}}</td>
                                    </tr>

                                    <tr>
                                        <td>6</td>
                                        <td>Rekomendasi</td>
                                        <td>:</td>
                                    </tr>
                                </table>

                                <ol class="ol-rekomendasi">
                                    <li>
                                        <b>Inisial Auditor</b>: <br>
                                        {{ $data->sis_jadwal_audit->jadw_tim_kode }}
                                    </li>
                                    <li>
                                        <b>Uraian Ketidaksesuaian</b>: <br>
                                        {!! $data->lks_uraian_ketidaksesuaian !!}
                                    </li>
                                    <li>
                                        <b>*Tindakan Perbaikan</b>
                                        <small>(tuliskan perbaikan anda)</small>
                                        <div style="padding: 50px 0 0 10px">
                                            <h4>Analisis Penyebab</h4>
                                            <textarea class="form-control" placeholder="Masukkaan deskripsi..."
                                                      name="editor_perbaikan_analisis"
                                                      aria-label="editor revisi analisis"
                                                      id="editor_perbaikan_analisis">{{old('editor_perbaikan_analisis')}}</textarea>
                                        </div>
                                        <div style="padding: 50px 0 0 10px">
                                            <h4>Koreksi</h4>
                                            <textarea class="form-control" placeholder="Masukkaan deskripsi..."
                                                      name="editor_perbaikan_tindakan"
                                                      aria-label="editor revisi tindakan"
                                                      id="editor_perbaikan_tindakan">{{old('editor_perbaikan_tindakan')}}</textarea>
                                        </div>
                                        <div style="padding: 50px 0 0 10px">
                                            <h4>Tindakan Korektif</h4>
                                            <textarea class="form-control" placeholder="Masukkaan deskripsi..."
                                                      name="editor_perbaikan_korektif"
                                                      aria-label="editor revisi korektif"
                                                      id="editor_perbaikan_korektif">{{old('editor_perbaikan_korektif')}}</textarea>
                                        </div>
                                    </li>
                                    <li>
                                        <b>Bukti Tindakan Perbaikan</b>
                                        <small>(jika ada, unggah file bukti perbaikan)</small>
                                        <form class="dropzone" id="upload_perbaikan" action="#">
                                            <div class="dz-message" data-dz-message>
                                                <span>Unggah Berkas Bukti Tindakan Perbaikan (PDF/DOCX/ZIP)</span>
                                            </div>
                                        </form>
                                        <button class="btn btn-xs btn-danger" @click="resetDropzone">
                                            <i class="fas fa-undo"></i> Bersihkan semua berkas
                                        </button>
                                    </li>
                                </ol>

                                <br>
                                <template v-if="loading_submit">
                                    <div style="float: right">
                                        <div class="fa-3x" style="text-align: center">
                                            <i class="fas fa-spinner fa-spin" style="color: #0390DE"></i>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <button class="btn btn-primary" style="float: right" @click="submit">
                                        <i class="fad fa-paper-plane"></i> Simpan & Kirim
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('javascript')
    <script src="https://cdn.tiny.cloud/1/hb65btdze8ubxfoabqu7fqjpuzpmx0c4k0je5f883m4l9ajf/tinymce/5/tinymce.min.js"
            referrerpolicy="origin"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        const swalWithBootstrapButtons = swal.mixin({
            confirmButtonClass: 'btn btn-primary mb-2',
            cancelButtonClass: 'btn btn-warning mr-2 mb-2',
            buttonsStyling: false,
        });

        $(document).ready(function () {
            new Vue({
                el: "#perbaikanPage",
                data: {
                    perbaikan_berkas: [],
                    perbaikan_text_analisis: null,
                    perbaikan_text_koreksi: null,
                    perbaikan_text_tindakan: null,
                    loading_submit: false,
                },
                created() {
                    Dropzone.autoDiscover = false;
                },
                mounted() {
                    setTimeout(() => {
                        this.buildDropzone()
                        this.buildTinyMCEPenyebab()
                        this.buildTinyMCEKoreksi()
                        this.buildTinyMCETindakan()
                    }, 500)
                },
                methods: {
                    buildDropzone() {
                        let self = this
                        $("#upload_perbaikan").dropzone({
                            url: "#",
                            // addRemoveLinks: true,
                            autoProcessQueue: false,
                            acceptedFiles: "application/zip, application/pdf, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/msword, application/octet-stream, application/vnd.oasis.opendocument.text",
                            accept(file, done) {
                                self.perbaikan_berkas.push(file)
                                this.emit("success", file);
                                this.emit("complete", file);
                                return done();
                            },
                        });
                    },
                    resetDropzone() {
                        this.perbaikan_berkas = [];
                        Dropzone.forElement('#upload_perbaikan').removeAllFiles(true)
                    },
                    buildTinyMCEPenyebab() {
                        $("#editor_perbaikan_analisis").html(`
                            <p>Analis Penyebab:&nbsp;</p>
                            <ul>
                            <li>....</li>
                            <li>....</li>
                            </ul>
                        `);

                        tinyMCE.init({
                            invalid_elements: "script",
                            selector: '#editor_perbaikan_analisis',
                            plugins: 'autosave link image lists',
                            relative_urls: false,
                            height: 500,
                            placeholder: 'Tuliskan analisis penyebab...',
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
                    },
                    buildTinyMCEKoreksi() {
                        $("#editor_perbaikan_tindakan").html(`
                            <p>Koreksi:</p>
                            <ul>
                            <li>...</li>
                            <li>...</li>
                            </ul>
                        `);

                        tinyMCE.init({
                            invalid_elements: "script",
                            selector: '#editor_perbaikan_tindakan',
                            plugins: 'autosave link image lists',
                            relative_urls: false,
                            height: 500,
                            placeholder: 'Tuliskan koreksi...',
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
                    },
                    buildTinyMCETindakan() {
                        $("#editor_perbaikan_korektif").html(`
                            <p>Tindakan Korektif:</p>
                            <ul>
                            <li>....</li>
                            <li>....</li>
                            </ul>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>`);

                        tinyMCE.init({
                            invalid_elements: "script",
                            selector: '#editor_perbaikan_korektif',
                            plugins: 'autosave link image lists',
                            relative_urls: false,
                            height: 500,
                            placeholder: 'Tuliskan tindakan korektif...',
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
                    },
                    destroyTinyMCE() {
                        tinymce.remove("#editor_perbaikan");
                    },
                    submit() {
                        swalWithBootstrapButtons({
                            title: `Kirim Perbaikan ?`,
                            text: `Pastikan data yang anda isikan benar. Proses akan berjalan beberapa saat, mohon bersabar untuk menunggu`,
                            type: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Kirim',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(async (result) => {
                            if (result.value) {
                                let formData = new FormData();
                                formData.append("perbaikan_text_analisis", tinymce.get("editor_perbaikan_analisis").getContent())
                                formData.append("perbaikan_text_koreksi", tinymce.get("editor_perbaikan_tindakan").getContent())
                                formData.append("perbaikan_text_tindakan", tinymce.get("editor_perbaikan_korektif").getContent())
                                if (this.perbaikan_berkas.length > 0) {
                                    this.perbaikan_berkas.map(e => {
                                        formData.append("berkas[]", e)
                                    })
                                }

                                this.loading_submit = true;
                                let self            = this;
                                $.ajax({
                                    url: `{{action("$module@processPerbaikanLKS", [$data->sis_jadwal_audit->sis_jadwal->jadw_id, $data->lks_id])}}`,
                                    type: 'post',
                                    processData: false,
                                    contentType: false,
                                    data: formData,
                                    success: async function (res) {
                                        toastCenter({
                                            type: 'success',
                                            title: res.message
                                        })

                                        setTimeout(() => {
                                            location.href       = "{{url("$url/temuan-lks/" . $data->sis_jadwal_audit->sis_jadwal->jadw_id)}}"
                                            self.loading_submit = false;
                                        }, 1000)
                                    },
                                    error: function (xhr) {
                                        self.loading_submit = false;
                                        if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                        else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                                    }
                                });
                            }
                        })
                    },
                }
            });
        });
    </script>
@endpush
