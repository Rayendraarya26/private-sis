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

        .dropzone {
            width: 100%;
            height: 200px;
            min-height: 0px !important;
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
                        @php($lksIDs=[])
                        <div class="custom-container">
                            <div class="col-md-12">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center;">
                                            <input type="checkbox" aria-label="check-all" @click="checkAll"
                                                   v-model="allChecked">Semua
                                        </th>
                                        <th style="text-align: center;">Auditor</th>
                                        <th style="text-align: center;">Uraian Ketidaksesuaian</th>
                                        <th style="text-align: center;">Tindakan Perbaikan <br>
                                            <i>(Disertai analisis penyebab, Koreksi, dan Tindakan Koreksi)</i>
                                        </th>
                                        <th style="text-align: center;">Bagian <br>(Pendamping)</th>
                                        <th style="text-align: center;">Bukti Tindakan Perbaikan</th>
                                        <th style="text-align: center;">Hasil dan Tanggal <br>Verifikasi</th>
                                    </tr>
                                    </thead>
                                    <tbody id="tbody-lks">
                                    @foreach($data->sis_audit_lks as $lks)
                                        <?php
                                        $editable = in_array($lks->lks_status, ['proses', 'revisi']);
                                        if ($editable) {
                                            $lksIDs[] = $lks->lks_id;
                                        }

                                        $hasilVerif = "";
                                        $verifKe    = 1;
                                        foreach ($lks->sis_audit_lks_revisis as $revisi) {
                                            if ($revisi->lks_revisi_oleh == "auditor") {
                                                $hasilVerif .= sprintf("<div style='text-align: center'>Verifikasi %d <br> %s</div>", $verifKe, $revisi->created_at->isoFormat("LL"));
                                                $hasilVerif .= sprintf("<br> %s <br><br>", $revisi->lks_revisi_catatan);
                                                $verifKe++;
                                            }
                                        }

                                        if ($lks->lks_sudah_ditutup == "ya") {
                                            $hasilVerif .= sprintf("<div style='text-align: center'>Verifikasi %d <br> %s </div>", $verifKe, $lks->lks_tanggal_ditutup->isoFormat("LL"));
                                            $hasilVerif .= sprintf("<br> %s <br><br> <b>LKS %d DITUTUP</b>", $lks->lks_catatan_ditutup, $lks->lks_nomor);
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                @if($editable)
                                                    <input type="checkbox" aria-label="check-{{$lks->lks_id}}"
                                                           v-model="check"
                                                           value="{{$lks->lks_id}}"
                                                           id="check_{{$lks->lks_id}}">
                                                @else
                                                    <i style="color: green"><i class="fad fa-paper-plane"></i>
                                                        Terkirim ke Auditor
                                                    </i>
                                                @endif
                                            </td>
                                            <td>
                                                {{$lks->sis_jadwal_tim->jadw_tim_kode}}
                                                <br>
                                                <br>
                                                @if($lks->lks_status == "revisi")
                                                    <a href="javascript:void(0)" @click="showRevisi({{$lks->lks_id}})">
                                                        <i class="fad fa-info"></i> REVISI LKS
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                {!! $lks->lks_uraian_ketidaksesuaian !!}
                                                <br>
                                                <b>Kategori
                                                    ketidaksesuaian</b>: {{ucwords($lks->lks_kategori_ketidaksesuaian)}}
                                                <br>
                                                <b>Klausul ketidak
                                                    sesuaian</b>: {!! $lks->lks_klausul_ketidaksesuaian !!}
                                                <br>
                                                <b>Tgl Max Revisi</b>:
                                                {{ $lks->lks_expired_date_perbaikan->isoFormat("LL") }}
                                            </td>
                                            <td>
                                                <div style="padding: 10px 0 0 0">
                                                    <b style="font-size: 12px">Analisis Penyebab: </b>
                                                    @if($editable)
                                                        <textarea class="form-control editor_perbaikan_analisis"
                                                                  placeholder="Masukkaan deskripsi..."
                                                                  name="editor_perbaikan_analisis"
                                                                  id="editor_perbaikan_analisis_{{$lks->lks_id}}"
                                                                  @change="saveAnalisa({{$lks->lks_id}})"
                                                                  aria-label="editor revisi analisis">{!! $lks->lks_perbaikan_analisa !!}</textarea>
                                                    @else
                                                        <br>
                                                        {!! $lks->lks_perbaikan_analisa !!}
                                                    @endif
                                                </div>
                                                <div style="padding: 10px 0 0 0">
                                                    <b style="font-size: 12px">Koreksi: </b>
                                                    @if($editable)
                                                        <textarea class="form-control editor_perbaikan_tindakan"
                                                                  placeholder="Masukkaan deskripsi..."
                                                                  name="editor_perbaikan_tindakan"
                                                                  id="editor_perbaikan_korektif_{{$lks->lks_id}}"
                                                                  aria-label="editor revisi tindakan">{!! $lks->lks_perbaikan_koreksi !!}</textarea>
                                                    @else
                                                        <br>
                                                        {!! $lks->lks_perbaikan_koreksi !!}
                                                    @endif
                                                </div>
                                                <div style="padding: 10px 0 0 0">
                                                    <b style="font-size: 12px">Tindakan Korektif: </b>
                                                    @if($editable)
                                                        <textarea class="form-control editor_perbaikan_korektif"
                                                                  placeholder="Masukkaan deskripsi..."
                                                                  name="editor_perbaikan_korektif"
                                                                  id="editor_perbaikan_tindakan_{{$lks->lks_id}}"
                                                                  aria-label="editor revisi korektif">{!! $lks->lks_perbaikan_tindakan !!}</textarea>
                                                    @else
                                                        <br>
                                                        {!! $lks->lks_perbaikan_tindakan !!}
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                {!! $lks->lks_bagian_pendamping !!}
                                            </td>
                                            <td>
                                                {{--                                                <div style="padding: 10px 0 0 0">--}}
                                                {{--                                                    <b style="font-size: 12px">Tindakan Perbaikan: </b>--}}
                                                {{--                                                    <textarea class="form-control editor_tindakan_perbaikan"--}}
                                                {{--                                                              placeholder="Masukkaan deskripsi..."--}}
                                                {{--                                                              name="editor_tindakan_perbaikan"--}}
                                                {{--                                                              id="editor_tindakan_perbaikan_{{$lks->lks_id}}"--}}
                                                {{--                                                              aria-label="editor revisi korektif">{{ $lks->lks_bukti_tindakan_perbaikan }}</textarea>--}}
                                                {{--                                                </div>--}}

                                                @if($editable)
                                                    <div style="padding-top: 20px">
                                                        <small>(jika ada, unggah file bukti perbaikan)</small>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                   id="file_perbaikan_{{$lks->lks_id}}" multiple
                                                                   @change="uploadFile({{$lks->lks_id}},...arguments)"
                                                                   accept="image/png,image/jpg,application/zip, application/pdf, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/msword, application/octet-stream, application/vnd.oasis.opendocument.text">
                                                            <label class="custom-file-label"
                                                                   for="file_perbaikan_{{$lks->lks_id}}">
                                                                Unggah file perbaikan...</label>
                                                        </div>
                                                        <small id="file_info_{{$lks->lks_id}}"></small>
                                                    </div>
                                                @endif

                                                @if(count($lks->sis_audit_lks_files) > 0)
                                                    <br>
                                                    <small>Melakukan upload ulang berarti menghapus file yang
                                                        lama</small>

                                                    @foreach($lks->sis_audit_lks_files as $file)
                                                        <br>
                                                        <a href="{{asset($file->lks_filepath)}}" target="_blank">
                                                            <i class="fad fa-download"></i> Berkas {{$loop->iteration}}
                                                        </a>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                {!! $hasilVerif !!}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                @if(count($lksIDs) == 0)
                                    <div class="alert alert-info" style="text-align: center">
                                        SEMUA LKS TELAH TERKIRIM KE AUDITOR. TERIMAKASIH
                                    </div>
                                @else
                                    <div class="alert alert-warning" style="text-align: center">
                                        {{count($lksIDs)}} LKS BELUM DIKIRIM KE AUDITOR. SEGERA LAKUKAN PERBAIKAN
                                    </div>
                                @endif
                            </div>

                            <a href="{{url("$url")}}" class="btn btn-outline-info">
                                <i class="fad fa-arrow-left"></i> Kembali
                            </a>
                            <div class="stickyButton" style="float: right">
                                <template v-if="loading_submit">
                                    <div class="fa-3x" style="text-align: center">
                                        <i class="fas fa-spinner fa-spin" style="color: #0390DE"></i>
                                    </div>
                                </template>
                                <template v-else>
                                    @if(count($lksIDs) > 0)
                                        <button class="btn btn-primary" @click="saveDraft()" type="button"
                                                id="btnSaveDraft">
                                            <i class="fas fa-save"></i> Simpan Draft
                                        </button>

                                        <button class="btn btn-success" @click="sendToAuditor()" type="button"
                                                id="btnSaveDraft">
                                            <i class="fas fa-paper-plane"></i>
                                            (@{{ check.length }})
                                            Kirim Auditor
                                        </button>
                                    @endif
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("javascript")
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/hb65btdze8ubxfoabqu7fqjpuzpmx0c4k0je5f883m4l9ajf/tinymce/5/tinymce.min.js"
            referrerpolicy="origin"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        $(document).ready(function () {
            new Vue({
                el: "#lksPage",
                data: {
                    allChecked: false,
                    check: [],
                    perbaikan_berkas: [],
                    loading_submit: false,
                    lksIDs: {{json_encode($lksIDs)}}
                },
                watch: {
                    check() {
                        this.allChecked = this.check.length == this.lksIDs.length;
                    }
                },
                mounted() {
                    setTimeout(() => {
                        this.buildTinyMCEPenyebab()
                        this.buildTinyMCEKoreksi()
                        this.buildTinyMCETindakan()
                        this.buildTinyMCETindakanPerbaikan();
                    }, 500)
                },
                methods: {
                    checkAll() {
                        if (!this.allChecked) {
                            this.check = this.lksIDs;
                        } else {
                            this.check = [];
                        }
                    },
                    buildTinyMCEPenyebab() {
                        // this.lksIDs.map(lksID => {
                        //     let targetID = `#editor_perbaikan_analisis_${lksID}`;
                        //     let htmlText = $(targetID).html();
                        //     if (htmlText == "" || htmlText == null) {
                        //         $(targetID).html(`
                        //             <p>Analisis:</p>
                        //             <ul>
                        //             <li>...</li>
                        //             <li>...</li>
                        //             </ul>
                        //         `);
                        //     }
                        // })

                        tinyMCE.init({
                            autosave_ask_before_unload: false,
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
                    buildTinyMCETindakan() {
                        // this.lksIDs.map(lksID => {
                        //     let targetID = `#editor_perbaikan_tindakan${lksID}`;
                        //     let htmlText = $(targetID).html();
                        //     if (htmlText == "" || htmlText == null) {
                        //         $(targetID).html(`
                        //             <p>Koreksi:</p>
                        //             <ul>
                        //             <li>...</li>
                        //             <li>...</li>
                        //             </ul>
                        //         `);
                        //     }
                        // })

                        tinyMCE.init({
                            autosave_ask_before_unload: false,
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
                    buildTinyMCEKoreksi() {
                        // this.lksIDs.map(lksID => {
                        //     let targetID = `#editor_perbaikan_korektif${lksID}`;
                        //     let htmlText = $(targetID).html();
                        //     if (htmlText == "" || htmlText == null) {
                        //         $(targetID).html(`
                        //             <p>Koreksi:</p>
                        //             <ul>
                        //             <li>...</li>
                        //             <li>...</li>
                        //             </ul>
                        //         `);
                        //     }
                        // })

                        tinyMCE.init({
                            autosave_ask_before_unload: false,
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
                        // this.lksIDs.map(lksID => {
                        //     let targetID = `#editor_tindakan_perbaikan${lksID}`;
                        //     let htmlText = $(targetID).html();
                        //     if (htmlText == "" || htmlText == null) {
                        //         $(targetID).html(`
                        //             <p>Koreksi:</p>
                        //             <ul>
                        //             <li>...</li>
                        //             <li>...</li>
                        //             </ul>
                        //         `);
                        //     }
                        // })

                        tinyMCE.init({
                            autosave_ask_before_unload: false,
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
                    async saveDraft() {
                        return new Promise((resolve, reject) => {
                            let needReload = false;
                            let self       = this;
                            // Saving draft
                            let dtPromise  = [];
                            for (const editor of tinymce.editors) {
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
                                if (data != null && data != "") {
                                    dtPromise.push(self.saveToDatabase(lksID, key, data))
                                }
                            }

                            if (this.perbaikan_berkas.length > 0) {
                                needReload = true;
                                for (const berkas of this.perbaikan_berkas) {
                                    dtPromise.push(self.saveFileToDatabase(berkas.lks_id, berkas.files))
                                }
                            }

                            this.loading_submit = true;
                            Promise.all(dtPromise)
                                .then(() => {
                                    this.loading_submit = false;
                                    toastCenter({
                                        type: 'success',
                                        title: "Simpan draft berhasil"
                                    })

                                    resolve();
                                    if (needReload) {
                                        location.reload();
                                    }
                                })
                                .catch(() => {
                                    reject();
                                    this.loading_submit = false;
                                })
                        })
                    },
                    async uploadFile(lksID, file) {
                        let available    = false;
                        let availableIdx = 0;
                        if (this.perbaikan_berkas.length == 0) {
                            available = false;
                        } else {
                            this.perbaikan_berkas.map((e, idx) => {
                                if (e.lks_id == lksID) {
                                    available    = true;
                                    availableIdx = idx
                                }
                            });
                        }

                        let data = {lks_id: lksID, files: file.target.files}
                        if (available) {
                            this.perbaikan_berkas[availableIdx] = data
                        } else {
                            this.perbaikan_berkas.push(data)
                        }

                        $(`#file_info_${lksID}`).html(`${data.files.length} berkas akan di unggah`)
                    },
                    async saveFileToDatabase(lksID, files) {
                        return new Promise((resolve, reject) => {
                            let requestForm = new FormData();
                            console.log(files.length)
                            for (let i = 0; i <= files.length; i++) {
                                if (files[i] != undefined) {
                                    requestForm.append(`files[]`, files[i])
                                }
                            }

                            axios.post(`{{url("$url/temuan-lks/$data->jadw_id/save-perbaikan-file")}}/${lksID}`, requestForm)
                                .then(function () {
                                    resolve();
                                })
                                .catch(function () {
                                    reject();
                                });
                        });
                    },
                    async saveToDatabase(lksID, key, value) {
                        return new Promise((resolve, reject) => {
                            if (value == null || value == "") {
                                reject()
                                return toastCenter({type: 'error', 'title': "Text editor tidak dapat kosong"})
                            }
                            $.ajax({
                                url: `{{url("$url/temuan-lks/$data->jadw_id/save-perbaikan-text")}}/${lksID}`,
                                type: 'POST',
                                method: 'POST',
                                dataType: 'json',
                                data: {key, value},
                                success: function (response) {
                                    console.log(response.message)
                                    resolve()
                                },
                                error: function (xhr) {
                                    if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                    else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                                    reject();
                                }
                            });
                        })
                    },
                    async sendToAuditor() {
                        if (this.check.length == 0) {
                            return toastCenter({
                                type: 'error',
                                title: 'Silakan check sebelum mengirim ke auditor'
                            })
                        }

                        await this.saveDraft()

                        this.loading_submit = true;
                        let self            = this;
                        $.ajax({
                            url: `{{url("$url/temuan-lks/$data->jadw_id/send-to-auditor")}}`,
                            type: 'POST',
                            method: 'POST',
                            dataType: 'json',
                            data: {ids: this.check},
                            success: function (response) {
                                toastCenter({
                                    type: 'success',
                                    title: response.message
                                })
                                location.reload();
                                self.loading_submit = false;
                            },
                            error: function (xhr) {
                                if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                                self.loading_submit = false;
                            }
                        });
                    },
                    async showRevisi(lksID) {
                        const swalWithBootstrapButtons = swal.mixin({
                            confirmButtonClass: 'btn btn-success mb-2',
                            buttonsStyling: false,
                        });

                        $.get(`{!! url("$url/ajax?action=data-verif-revisi-by-lks&jadwal_id=$data->jadw_id") !!}&lks_id=${lksID}`)
                            .then(response => {
                                console.log(response)
                                swalWithBootstrapButtons({
                                    title: `Detail Revisi`,
                                    html: response.results.lks_revisi_catatan,
                                    type: 'info',
                                })
                            })
                            .fail((xhr) => {
                                console.log(xhr)
                                if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                            });
                    }
                }
            });
        });
    </script>
@endpush
