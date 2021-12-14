@extends("layouts.layout_app")

@section('title', 'Temuan LKS')

@push('css')
    <style>
        .custom-container {
            margin: 0 auto;
            padding: 10px;
        }

        .stickyButton {
            position: -webkit-sticky;
            position: sticky;
            bottom: 20px;
            border-color: red;
        }

    </style>
@endpush

@section('content')
    <div class="dt-content" id="lksPage">
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
                            <h3 class="dt-card__title" style="text-align: center">
                                LAPORAN KETIDAKSESUAIAN dan LAPORAN VERIFIKASI
                            </h3>
                        </div>
                    </div>


                    <div class="dt-card__body">
                        <div class="col-lg-12">
                            <table class="table">
                                <tr>
                                    <td style="width: 50px">1</td>
                                    <td>Jenis Kegiatan</td>
                                    <td>:
                                        @foreach($data->sis_jadwal_audits as $audit)
                                            {{$audit->jadw_audit_kegiatan . (!$loop->last ? ' - ' : '.')}}
                                        @endforeach
                                    </td>
                                </tr>

                                <tr>
                                    <td rowspan="3">2</td>
                                    <td>Nama Perusahaan</td>
                                    <td>: {{$data->sis_pelanggan->cust_nama}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>No. Referensi</td>
                                    <td>:
                                        @foreach($data->sis_jadwal_audits as $audit)
                                            @if($audit->jadw_audit_nomor_referensi != "")
                                                {{$audit->jadw_audit_nomor_referensi . (!$loop->last ? ' ; ' : '.')}}
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>: {{$data->sis_pelanggan->cust_alamat}}
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td>Tanggal Asesmen</td>
                                    <td>
                                        : {{ $data->jadw_tanggal_mulai->isoFormat("LL") }}
                                        s/d {{ $data->jadw_tanggal_selesai->isoFormat("LL") }}</td>
                                </tr>

                                <tr>
                                    <td>4</td>
                                    <td>Tim Asesmen</td>
                                    <td>:
                                        <ol>
                                            @foreach($data->sis_jadwal_tims as $tim)
                                                <li>
                                                    {{$tim->master_pegawai->peg_nama}} | {{$tim->jadw_tim_kode}}
                                                    <b>({{ucwords($tim->jadw_tim_posisi)}})</b>
                                                </li>
                                            @endforeach
                                        </ol>
                                    </td>
                                </tr>

                                <tr>
                                    <td>5</td>
                                    <td>Standar Acuan</td>
                                    <td>:
                                        @foreach($data->sis_jadwal_audits as $audit)
                                            @if($audit->jadw_audit_standart_acuan != "")
                                                {{$audit->jadw_audit_standart_acuan . (!$loop->last ? ' ; ' : '.')}}
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                            </table>
                        </div>


                        {{-- Data LKS --}}
                        <div class="custom-container">
                            <div class="col-md-12">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Auditor</th>
                                        <th>Uraian Ketidaksesuaian</th>
                                        <th>Tindakan Perbaikan <br>
                                            <i>(Disertai analisis penyebab, Koreksi, dan Tindakan Koreksi)</i>
                                        </th>
                                        <th>Bukti Tindakan Perbaikan</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($data->sis_audit_lks as $lks)

                                        <tr>
                                            <td>{{$lks->sis_jadwal_tim->jadw_tim_kode}}</td>
                                            <td>
                                                {!! $lks->lks_uraian_ketidaksesuaian !!}
                                                <br>
                                                Kategori
                                                ketidaksesuaian: {{ucwords($lks->lks_kategori_ketidaksesuaian)}}
                                                <br>
                                                Klausul ketidak sesuaian: {!! $lks->lks_klausul_ketidaksesuaian !!}
                                            </td>
                                            <td>
                                                <div style="padding: 10px 0 0 0">
                                                    <b style="font-size: 12px">Analisis Penyebab: </b>
                                                    <textarea class="form-control editor_perbaikan_analisis"
                                                              placeholder="Masukkaan deskripsi..."
                                                              name="editor_perbaikan_analisis"
                                                              id="editor_perbaikan_analisis_{{$lks->lks_id}}"
                                                              @change="saveAnalisa({{$lks->lks_id}})"
                                                              aria-label="editor revisi analisis">{{old('editor_perbaikan_analisis') ?? $lks->lks_perbaikan_analisa}}</textarea>
                                                </div>
                                                <div style="padding: 10px 0 0 0">
                                                    <b style="font-size: 12px">Koreksi: </b>
                                                    <textarea class="form-control editor_perbaikan_tindakan"
                                                              placeholder="Masukkaan deskripsi..."
                                                              name="editor_perbaikan_tindakan"
                                                              id="editor_perbaikan_tindakan_{{$lks->lks_id}}"
                                                              aria-label="editor revisi tindakan">{{old('editor_perbaikan_tindakan') ?? $lks->lks_perbaikan_koreksi}}</textarea>
                                                </div>
                                                <div style="padding: 10px 0 0 0">
                                                    <b style="font-size: 12px">Tindakan Korektif: </b>
                                                    <textarea class="form-control editor_perbaikan_korektif"
                                                              placeholder="Masukkaan deskripsi..."
                                                              name="editor_perbaikan_korektif"
                                                              id="editor_perbaikan_korektif_{{$lks->lks_id}}"
                                                              aria-label="editor revisi korektif">{{old('editor_perbaikan_korektif') ?? $lks->lks_perbaikan_tindakan}}</textarea>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="padding: 10px 0 0 0">
                                                    <b style="font-size: 12px">Tindakan Perbaikan: </b>
                                                    <textarea class="form-control editor_tindakan_perbaikan"
                                                              placeholder="Masukkaan deskripsi..."
                                                              name="editor_tindakan_perbaikan"
                                                              id="editor_tindakan_perbaikan_{{$lks->lks_id}}"
                                                              aria-label="editor revisi korektif">{{old('editor_tindakan_perbaikan') ?? $lks->lks_bukti_tindakan_perbaikan}}</textarea>
                                                </div>

                                                @foreach($lks->sis_audit_lks_files as $file)
                                                    <br>
                                                    <a href="{{asset($file->lks_filepath)}}">
                                                        <i class="fad fa-download"></i> Berkas {{$loop->iteration}}
                                                    </a>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <a href="{{url("$url")}}" class="btn btn-info btn-outline-info">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <div class="stickyButton" style="float: right">
                                <button class="btn btn-primary" @click="saveDraft()" type="button">
                                    <i class="fas fa-save"></i> Simpan Draft
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("javascript")
    <script src="https://cdn.tiny.cloud/1/hb65btdze8ubxfoabqu7fqjpuzpmx0c4k0je5f883m4l9ajf/tinymce/5/tinymce.min.js"
            referrerpolicy="origin"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        $(document).ready(function () {
            new Vue({
                el: "#lksPage",
                mounted() {
                    setTimeout(() => {
                        // this.buildDropzone()

                        this.buildTinyMCEPenyebab()
                        this.buildTinyMCEKoreksi()
                        this.buildTinyMCETindakan()
                        this.buildTinyMCETindakanPerbaikan();
                    }, 500)
                },
                methods: {
                    buildTinyMCEPenyebab() {
                        $(".editor_perbaikan_analisis").html(`
                            <p>Analis Penyebab:&nbsp;</p>
                            <ul>
                            <li>....</li>
                            <li>....</li>
                            </ul>
                        `);

                        tinyMCE.init({
                            invalid_elements: "script",
                            selector: '.editor_perbaikan_analisis',
                            plugins: 'autosave link image lists',
                            relative_urls: false,
                            height: 300,
                            width: 400,
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
                        $(".editor_perbaikan_tindakan").html(`
                            <p>Koreksi:</p>
                            <ul>
                            <li>...</li>
                            <li>...</li>
                            </ul>
                        `);

                        tinyMCE.init({
                            invalid_elements: "script",
                            selector: '.editor_perbaikan_tindakan',
                            plugins: 'autosave link image lists',
                            relative_urls: false,
                            height: 300,
                            width: 400,
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
                        $(".editor_perbaikan_korektif").html(`
                            <p>Tindakan Korektif:</p>
                            <ul>
                            <li>....</li>
                            <li>....</li>
                            </ul>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>`);

                        tinyMCE.init({
                            invalid_elements: "script",
                            selector: '.editor_perbaikan_korektif',
                            plugins: 'autosave link image lists',
                            relative_urls: false,
                            height: 300,
                            width: 400,
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
                    buildTinyMCETindakanPerbaikan() {
                        $(".editor_tindakan_perbaikan").html(`
                            <p>Tindakan Perbaikan:</p>
                            <ul>
                            <li>....</li>
                            <li>....</li>
                            </ul>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>`);

                        tinyMCE.init({
                            invalid_elements: "script",
                            selector: '.editor_tindakan_perbaikan',
                            plugins: 'autosave link image lists',
                            relative_urls: false,
                            height: 300,
                            width: 400,
                            placeholder: 'Tuliskan tindakan perbaikan...',
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
                    saveDraft() {
                        let self      = this;
                        // Saving draft
                        let dtPromise = [];
                        tinymce.editors.forEach(async function (editor) {
                            let data    = tinyMCE.get(editor.settings.id).getContent()
                            let lksArr  = editor.settings.id.split("_")
                            const lksID = lksArr[lksArr.length - 1];
                            let key     = "";

                            switch (lksArr[2]) {
                                case "analisis":
                                    key = "lks_perbaikan_analisa";
                                    break;
                                case "tindakan":
                                    key = "lks_perbaikan_tindakan"
                                    break;
                                case "korektif":
                                    key = "lks_perbaikan_koreksi"
                                    break;
                                case "perbaikan":
                                    key = "lks_bukti_tindakan_perbaikan"
                                    break;
                            }
                            dtPromise.push(self.saveToDatabase(resolve, reject, lksID, key, data))
                        });

                        Promise.all(dtPromise).then((values) => {
                            console.log(values)
                            toastCenter({
                                type: 'success',
                                title: "Simpan draft berhasil"
                            })
                        });
                    },
                    async saveToDatabase(resolve, reject, lksID, key, value) {
                        $.ajax({
                            url: `{{url("$url/temuan-lks/$data->jadw_id/save-perbaikan-text")}}/${lksID})`,
                            type: 'POST',
                            dataType: 'json',
                            data: {key, value},
                            success: function (response) {
                                resolve()
                                console.log(response.message)
                            },
                            error: function (xhr) {
                                if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                            }
                        });
                    }
                }
            });
        });
    </script>
@endpush
