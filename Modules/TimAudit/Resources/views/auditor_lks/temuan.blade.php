@extends('layouts.layout_app')

@section('title', 'Temuan LKS')

@push('css')
    <link rel="stylesheet" href="{{asset('assets/plugins/datetimepicker/tempusdominus-bootstrap-4.min.css')}}">
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
    <div class="dt-content" id="auditorApp">
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
                            <h3 class="dt-card__title" style="text-align: center">
                                LAPORAN KETIDAKSESUAIAN dan LAPORAN VERIFIKASI
                            </h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="col-lg-12">
                            <table class="table table-bordered">
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
                    </div>
                </div>


                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title" style="text-align: center">
                                Rekomendasi LKS
                            </h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="row">
                            <div style="width: 100%">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select aria-label="filter auditor" class="form-control" id="fil_auditor"
                                                    @change="doFilterAuditor">
                                                <option value="all" selected>-- Semua Auditor --</option>
                                                @foreach($data->sis_jadwal_tims as $tim)
                                                    <option value="{{$tim->jadw_tim_kode}}">
                                                        {{$tim->master_pegawai->peg_nama}} | {{$tim->jadw_tim_kode}}
                                                        ({{ucwords($tim->jadw_tim_posisi)}})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3"></div>
                                        <div class="col-md-3"></div>
                                        <div class="col-md-3">
                                            <div style="float: right">
                                                <button class="btn btn-success" @click="promptTambah">
                                                    <i class="fas fa-plus"></i> Tambah
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Auditor</th>
                                            <th>Uraian Ketidaksesuaian</th>
                                            <th style="width: 10%">Aksi</th>
                                        </tr>
                                        </thead>
                                        <tbody id="tbody-lks">
                                        @foreach($data->sis_audit_lks as $lks)
                                            @php($lksIDs[] = $lks->lks_id)
                                            <tr>
                                                <td>{{$lks->sis_jadwal_tim->jadw_tim_kode}}</td>
                                                <td>
                                                    @if(auth()->user()->master_pegawai->peg_id == $lks->sis_jadwal_tim->master_pegawai->peg_id)
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div style="padding-bottom: 10px">
                                                                    <b style="font-size: 12px">No LKS: </b>
                                                                    <input type="text" id="lks_nomor_{{$lks->lks_id}}"
                                                                           class="form-control"
                                                                           placeholder="Tuliskan nomor LKS..."
                                                                           @keyup="changeNoLks({{$lks->lks_id}})"
                                                                           aria-label="nomor lks"
                                                                           value="{!! $lks->lks_nomor !!}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div style="padding-bottom: 10px">
                                                                    <b style="font-size: 12px">Uraian
                                                                        Ketidaksesuaian: </b>
                                                                    <textarea class="form-control editor_uraian"
                                                                              placeholder="Masukkaan deskripsi..."
                                                                              name="editor_uraian_{{$lks->lks_id}}"
                                                                              id="editor_uraian_{{$lks->lks_id}}"
                                                                              aria-label="editor revisi analisis">{!! $lks->lks_uraian_ketidaksesuaian !!}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div style="padding-bottom: 10px">
                                                                    <b style="font-size: 12px">Klausul Ketidaksesuaian: </b>
                                                                    <textarea class="form-control editor_klausul"
                                                                              placeholder="Masukkaan deskripsi..."
                                                                              name="editor_klausul_{{$lks->lks_id}}"
                                                                              id="editor_klausul_{{$lks->lks_id}}"
                                                                              aria-label="editor revisi analisis">{!! $lks->lks_klausul_ketidaksesuaian !!}</textarea>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div style="padding-bottom: 10px">
                                                                    <b style="font-size: 12px">Kategori Ketidaksesuaian: </b>
                                                                    <select aria-label="kategori ketidaksesuaian"
                                                                            @change="changeKategori({{$lks->lks_id}}, ...arguments)"
                                                                            name="lks_kategori_{{$lks->lks_id}}"
                                                                            class="form-control"
                                                                            id="lks_kategori_{{$lks->lks_id}}">
                                                                        <option selected disabled>-- Pilih Kategori --</option>
                                                                        <option
                                                                            value="observasi" {{$lks->lks_kategori_ketidaksesuaian == "observasi" ? 'selected': ""}}>
                                                                            Observasi
                                                                        </option>
                                                                        <option
                                                                            value="minor" {{$lks->lks_kategori_ketidaksesuaian == "minor" ? 'selected': ""}}>
                                                                            Minor
                                                                        </option>
                                                                        <option
                                                                            value="mayor" {{$lks->lks_kategori_ketidaksesuaian == "mayor" ? 'selected': ""}}>
                                                                            Mayor
                                                                        </option>
                                                                        <option
                                                                            value="kritis" {{$lks->lks_kategori_ketidaksesuaian == "kritis" ? 'selected': ""}}>
                                                                            Kritis
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div>
                                                                    <b style="font-size: 12px">Tgl Max Revisi: </b>
                                                                    <input type="date" name="lks_daterevisi_{{$lks->lks_id}}"
                                                                           class="form-control"
                                                                           @change="changeDateRevisi({{$lks->lks_id}})"
                                                                           aria-label="tgl max revisi"
                                                                           value="{{$lks->lks_expired_date_perbaikan?->format("Y-m-d")}}"
                                                                           id="lks_daterevisi_{{$lks->lks_id}}">
                                                                    <small><i>klik icon dikanan untuk memunculkan
                                                                            tanggal</i></small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        No LKS: {!! $lks->lks_nomor !!}
                                                        <br><br>
                                                        {!! $lks->lks_uraian_ketidaksesuaian !!}
                                                        <br>
                                                        Kategori
                                                        ketidaksesuaian: {{ucwords($lks->lks_kategori_ketidaksesuaian)}}
                                                        <br>
                                                        Klausul ketidak
                                                        sesuaian: {!! $lks->lks_klausul_ketidaksesuaian !!}
                                                        <br><br>
                                                        Tgl Max Revisi
                                                        {{$lks->lks_expired_date_perbaikan?->isoFormat("LL")}}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(auth()->user()->master_pegawai->peg_id == $lks->sis_jadwal_tim->master_pegawai->peg_id)
                                                        <button class="btn btn-danger btn-block btn-sm"
                                                                @click="promptDelete({{$lks->lks_id}})">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>


                                <a :href="loading_submit ? '#' : '{{url("$url")}}'" class="btn btn-outline-info">
                                    <i class="fad fa-arrow-left"></i> Kembali
                                </a>

                                @if(count($data->sis_audit_lks) > 0)
                                    <div class="stickyButton" style="float: right;">
                                        <template v-if="loading_submit">
                                            <div class="fa-3x" style="text-align: center">
                                                <i class="fas fa-spinner fa-spin" style="color: #0390DE"></i>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <button class="btn btn-primary" @click="saveDraft()" type="button"
                                                    id="btnSaveDraft">
                                                <i class="fas fa-save"></i> Simpan Draft
                                        </button>
                                    </template>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("javascript")
    <script src="https://cdn.tiny.cloud/1/hb65btdze8ubxfoabqu7fqjpuzpmx0c4k0je5f883m4l9ajf/tinymce/5/tinymce.min.js">
    </script>
    <script src="{{asset('assets/plugins/datetimepicker/tempusdominus-bootstrap-4.min.js')}}"></script>
    <script>
        function confirmDelete(lksID) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-success mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Hapus LKS ?`,
                text: `Menghapus data LKS bersifat permanen dan tidak dapat di kembalikan`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `{{url("$url/temuan/$data->jadw_id/delete")}}/${lksID}`,
                        type: 'DELETE',
                        dataType: 'json',
                        success: function (response) {
                            toastCenter({
                                type: 'success',
                                title: response.message
                            })

                            $('#ttData').datagrid('reload');
                        },
                        error: function (xhr) {
                            if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                            else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                        }
                    });
                }
            });
        }

        new Vue({
            el: "#auditorApp",
            data: {
                maxAuditDate: moment("{{$data->jadw_tanggal_selesai->format("Y-m-d")}}", "YYYY-MM-DD"),
                loading_submit: false,
                update_kategori: [],
                update_date_revisi: [],
                update_nomer_lks: [],
                total_lks: {{$data->sis_audit_lks->count()}}
            },
            mounted() {
                @if(isset($_GET['auditor']))
                $("#fil_auditor").val(`{{$_GET['auditor']}}`);
                @endif

                setTimeout(() => {
                    this.buildTinyMCEUraian()
                    this.buildTinyMCEKlausul();
                }, 500)
            },
            methods: {
                doFilterAuditor: function () {
                    const filterKode = $("#fil_auditor").val()
                    location.href    = `{{url("$url/temuan/$data->jadw_id")}}?auditor=${filterKode}`
                },
                buildTinyMCEUraian() {
                    tinyMCE.init({
                        invalid_elements: "script",
                        selector: '.editor_uraian',
                        plugins: 'autosave link image lists',
                        relative_urls: false,
                        height: 300,
                        width: '100%',
                        placeholder: 'Tuliskan uraian...',
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
                buildTinyMCEKlausul() {
                    tinyMCE.init({
                        invalid_elements: "script",
                        selector: '.editor_klausul',
                        plugins: 'autosave link image lists',
                        relative_urls: false,
                        height: 300,
                        width: '100%',
                        placeholder: 'Tuliskan klausul...',
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
                promptTambah() {
                    const swalWithBootstrapButtons = swal.mixin({
                        confirmButtonClass: 'btn btn-success mb-2',
                        cancelButtonClass: 'btn btn-warning mr-2 mb-2',
                        buttonsStyling: false,
                    });

                    swalWithBootstrapButtons({
                        title: 'Berapa total data yang akan anda tambah ?',
                        input: 'number',
                        inputAttributes: {
                            autocapitalize: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Generate',
                        cancelButtonText: 'Batal',
                        closeOnConfirm: false,
                        closeOnCancel: false,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.value) {
                            this.generateLKS(result.value)
                        }
                    });
                },
                generateLKS(total) {
                    $.ajax({
                        url: `{{url("$url/temuan/$data->jadw_id/generate")}}`,
                        type: 'POST',
                        method: 'POST',
                        dataType: 'json',
                        data: {total},
                        success: function (response) {
                            location.reload();
                            toastCenter({
                                type: 'success',
                                title: response.message
                            })
                        },
                        error: function (xhr) {
                            if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                            else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                        }
                    });
                },
                promptDelete(lksID) {
                    const swalWithBootstrapButtons = swal.mixin({
                        confirmButtonClass: 'btn btn-danger mb-2',
                        cancelButtonClass: 'btn btn-warning mr-2 mb-2',
                        buttonsStyling: false,
                    });

                    swalWithBootstrapButtons({
                        title: 'Hapus LKS ?',
                        type: "warning",
                        html: 'Menghapus data LKS bersifat permanen dan tidak dapat dikembalikan',
                        inputAttributes: {
                            autocapitalize: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Ok',
                        cancelButtonText: 'Batal',
                        closeOnConfirm: false,
                        closeOnCancel: false,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.value) {
                            $.ajax({
                                url: `{{url("$url/temuan/$data->jadw_id/delete")}}/${lksID}`,
                                type: 'POST',
                                method: 'POST',
                                dataType: 'json',
                                success: function (response) {
                                    toastCenter({type: 'success', 'title': response.message})
                                    location.reload()
                                },
                                error: function (xhr) {
                                    if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                    else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                                }
                            });
                        }
                    });
                },
                changeNoLks(lksID) {
                    let available    = false;
                    let availableIdx = 0;
                    if (this.update_nomer_lks.length == 0) {
                        available = false;
                    } else {
                        this.update_nomer_lks.map((e, idx) => {
                            if (e.lks_id == lksID) {
                                available    = true;
                                availableIdx = idx
                            }
                        });
                    }

                    let newNomor = $(`#lks_nomor_${lksID}`).val()
                    let data     = {lks_id: lksID, data: newNomor}
                    if (available) {
                        this.update_nomer_lks[availableIdx] = data
                    } else {
                        this.update_nomer_lks.push(data)
                    }
                },
                changeKategori(lksID) {
                    let available    = false;
                    let availableIdx = 0;
                    if (this.update_kategori.length == 0) {
                        available = false;
                    } else {
                        this.update_kategori.map((e, idx) => {
                            if (e.lks_id == lksID) {
                                available    = true;
                                availableIdx = idx
                            }
                        });
                    }

                    let value = $(`#lks_kategori_${lksID}`).val();
                    let data  = {lks_id: lksID, data: value}
                    if (available) {
                        this.update_kategori[availableIdx] = data
                    } else {
                        this.update_kategori.push(data)
                    }

                    let targetDateID = `#lks_daterevisi_${lksID}`;
                    let tmpDate      = moment(this.maxAuditDate)
                    switch (value) {
                        case "kritis":
                            tmpDate.add(0, 'd')
                            break;
                        case "mayor":
                            tmpDate.add(1, 'M')
                            break;
                        case "minor":
                            tmpDate.add(2, 'M')
                            break;
                        case "observasi":
                            tmpDate.add(3, 'M')
                            break;
                    }
                    $(targetDateID).val(tmpDate.format("YYYY-MM-DD"))
                    this.changeDateRevisi(lksID)
                },
                changeDateRevisi(lksID) {
                    let available    = false;
                    let availableIdx = 0;
                    if (this.update_date_revisi.length == 0) {
                        available = false;
                    } else {
                        this.update_date_revisi.map((e, idx) => {
                            if (e.lks_id == lksID) {
                                available    = true;
                                availableIdx = idx
                            }
                        });
                    }

                    let dateOri = $(`#lks_daterevisi_${lksID}`).val()
                    let data    = {lks_id: lksID, data: dateOri}
                    if (available) {
                        this.update_date_revisi[availableIdx] = data
                    } else {
                        this.update_date_revisi.push(data)
                    }
                },
                saveDraft() {
                    let isKritisExist = false;
                    this.update_kategori.map(e => {
                        if (e.data == "kritis") {
                            isKritisExist = true
                        }
                    })

                    if (isKritisExist) {
                        this.processPromiseAllWithPrompt();
                    } else {
                        this.processPromiseAll(false);
                    }
                },
                async processPromiseAllWithPrompt() {
                    const swalWithBootstrapButtons = swal.mixin({
                        confirmButtonClass: 'btn btn-danger mb-2',
                        cancelButtonClass: 'btn btn-success mr-2 mb-2',
                        buttonsStyling: false,
                    });

                    swalWithBootstrapButtons({
                        title: `Temuan Kritis ?`,
                        text: `Anda yakin untuk menambahkan temuan kritis ?, ini akan membuat proses audit otomatis berakhir (diajukan ke komite).`,
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ok',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.value) {
                            this.processPromiseAll(true)
                        }
                    });
                },
                processPromiseAll(withReload) {
                    let self      = this;
                    // Saving draft
                    let dtPromise = [];
                    tinymce.editors.forEach(function (editor) {
                        let data    = tinyMCE.get(editor.settings.id).getContent()
                        let lksArr  = editor.settings.id.split("_")
                        const lksID = lksArr[lksArr.length - 1];
                        let key     = "";

                        switch (lksArr[1]) {
                            case "uraian":
                                key = "lks_uraian_ketidaksesuaian";
                                break;
                            case "klausul":
                                key = "lks_klausul_ketidaksesuaian"
                                break;
                        }

                        dtPromise.push(self.saveToDatabase(lksID, key, data))
                    });

                    this.update_kategori.map(e => {
                        dtPromise.push(this.saveToDatabase(e.lks_id, 'lks_kategori_ketidaksesuaian', e.data))
                    })

                    this.update_date_revisi.map(e => {
                        dtPromise.push(this.saveToDatabase(e.lks_id, 'lks_expired_date_perbaikan', e.data))
                    })

                    this.update_nomer_lks.map(e => {
                        dtPromise.push(this.saveToDatabase(e.lks_id, 'lks_nomor', e.data))
                    })

                    this.loading_submit = true;
                    Promise.all(dtPromise)
                        .then(() => {
                            this.loading_submit = false;
                            toastCenter({
                                type: 'success',
                                title: "Simpan berhasil"
                            })

                            if (withReload) {
                                location.reload()
                            }
                        })
                        .catch(() => {
                            this.loading_submit = false;
                        })

                },
                async saveToDatabase(lksID, key, value) {
                    return new Promise((resolve, reject) => {
                        if (value == null || value == "") {
                            reject()
                            return toastCenter({type: 'error', title: "Text editor tidak dapat kosong"})
                        }
                        $.ajax({
                            url: `{{url("$url/temuan/$data->jadw_id/save-draft")}}`,
                            type: 'POST',
                            method: 'POST',
                            dataType: 'json',
                            data: {key, value, lks_id: lksID},
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
                }
            }
        })
    </script>
@endpush
