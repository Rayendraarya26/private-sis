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
    <div class="dt-content" id="verifPage">
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
                    </div>
                </div><!-- CARD -->


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
                                                    @change="doFilter">
                                                <option value="all" selected>-- Semua Auditor --</option>
                                                @foreach($data->sis_jadwal_tims as $tim)
                                                    <option value="{{$tim->jadw_tim_kode}}">
                                                        {{$tim->master_pegawai->peg_nama}} | {{$tim->jadw_tim_kode}}
                                                        ({{ucwords($tim->jadw_tim_posisi)}})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select aria-label="filter auditor" class="form-control" id="fil_status"
                                                    @change="doFilter">
                                                <option value="all" selected>-- Semua Status --</option>
                                                <option value="proses">Proses Perbaikan (PROSES)</option>
                                                <option value="revisi">Revisi (REVISI)</option>
                                                <option value="fixed">Telah Diperbaiki (FIXED)</option>
                                                <option value="memadai">Closed (MEMADAI)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th style="text-align: center;">Status</th>
                                            <th style="text-align: center;">Auditor</th>
                                            <th style="text-align: center;">Uraian Ketidaksesuaian</th>
                                            <th style="text-align: center;">Tindakan Perbaikan <br>
                                                <i>(Disertai analisis penyebab, Koreksi, dan Tindakan Koreksi)</i>
                                            </th>
                                            <th style="text-align: center;">Bagian <br>(Pendamping)</th>
                                            <th style="text-align: center;">Bukti Tindakan Perbaikan</th>
                                            <th style="text-align: center;">Hasil dan Tanggal <br> Verifikasi</th>
                                            <th style="text-align: center;">Aksi</th>
                                        </tr>
                                        </thead>
                                        <tbody id="tbody-lks">
                                        <tr v-for="(lks, idx) in dataLKS">
                                            <td>
                                                <template v-if="lks.lks_status == 'revisi'">
                                                    <a href="javascript:void(0)" @click="showRevisi(lks.lks_id)">
                                                        @{{ lks.lks_status.toUpperCase() }}
                                                    </a>
                                                </template>
                                                <template v-else>
                                                    @{{ lks.lks_status.toUpperCase() }}
                                                </template>
                                            </td>
                                            <td>@{{ lks.jadw_tim_kode }}</td>
                                            <td>
                                                <p v-html="lks.lks_uraian_ketidaksesuaian"></p>
                                                <br>
                                                <br>
                                                Kategori
                                                ketidaksesuaian: @{{ lks.lks_kategori_ketidaksesuaian.toUpperCase() }}
                                                <br>
                                                Klausul ketidak
                                                sesuaian: <p v-html="lks.lks_klausul_ketidaksesuaian"></p>
                                                <br><br>
                                                Tgl Max Revisi
                                                @{{ lks.lks_expired_date_perbaikan }}
                                            </td>
                                            <td>
                                                <div style="padding: 10px 0 0 0">
                                                    <b style="font-size: 12px">Analisis Penyebab: </b>
                                                    <p v-html="lks.lks_perbaikan_analisa"></p>
                                                </div>
                                                <div style="padding: 10px 0 0 0">
                                                    <p v-html="lks.lks_perbaikan_koreksi"></p>
                                                </div>
                                                <div style="padding: 10px 0 0 0">
                                                    <b style="font-size: 12px">Tindakan Korektif: </b>
                                                    <p v-html="lks.lks_perbaikan_tindakan"></p>
                                                </div>
                                            </td>
                                            <td>
                                                @{{ lks.lks_bagian_pendamping }}
                                            </td>
                                            <td>
                                                {{--                                                <div style="padding: 10px 0 0 0">--}}
                                                {{--                                                    <b style="font-size: 12px">Tindakan Perbaikan: </b>--}}
                                                {{--                                                    <p v-html="lks.lks_bukti_tindakan_perbaikan"></p>--}}
                                                {{--                                                </div>--}}
                                                <div v-if="lks.perbaikan_files.length > 0">
                                                    <br>
                                                    <small>Berkas yang diunggah oleh client:</small>

                                                    <template v-for="(file,idx) in lks.perbaikan_files">
                                                        <br>
                                                        <a :href="file.url" target="_blank">
                                                            <i class="fad fa-download"></i>
                                                            Berkas @{{idx + 1}}
                                                        </a>
                                                    </template>

                                                </div>
                                            </td>
                                            <td>
                                                <div v-html="lks.hasil_verif"></div>
                                            </td>
                                            <td>
                                                <template v-if="lks.allow_edit">
                                                    <template v-if="lks.lks_sudah_ditutup == 'ya'">
                                                        <i class="fas fa-badge-check"></i> Terverifikasi
                                                    </template>
                                                    <template v-else>
                                                        <button class="btn btn-primary btn-xs btn-block"
                                                                @click="promptVerifikasi(lks.lks_id)">
                                                            <i class="fas fa-check"></i> Close
                                                        </button>

                                                        <button class="btn btn-warning btn-xs btn-block"
                                                                @click="propmtRevisi(lks.lks_id)">
                                                            <i class="fas fa-edit"></i> Revisi
                                                        </button>
                                                    </template>
                                                </template>
                                                <template v-else>
                                                    <i class="fas fa-user-shield"></i> Protected <br>
                                                    <div style="font-size: 10px">
                                                        Salah satu dari:
                                                        <ul>
                                                            <li>LKS belum dikirim ke auditor</li>
                                                            <li>LKS milik auditor lain</li>
                                                            <li>LKS sudah ditutup</li>
                                                        </ul>
                                                    </div>
                                                </template>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- CARD -->
            </div>
        </div>

        <div class="modal fade" id="modalRevisi" tabindex="-1" role="dialog"
             aria-labelledby="modalRevisi" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">

                <!-- Modal Content -->
                <div class="modal-content">

                @csrf
                <!-- Modal Header -->
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalRevisiTitle">
                            Revisi LKS
                        </h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- /modal header -->

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <label for="revisi_ket">Keterangan</label>
                        <textarea name="revisi_ket" id="revisi_ket" cols="30" rows="10"></textarea>
                    </div>
                    <!-- /modal body -->

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button @click="processRevisi" id="simpanRevisi" type="button" class="btn btn-primary btn-sm">
                            Simpan
                        </button>
                    </div>
                    <!-- /modal footer -->
                </div>
                <!-- /modal content -->
            </div>
        </div>

        <div class="modal fade" id="modalClose" tabindex="-1" role="dialog"
             aria-labelledby="modalClose" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">

                <!-- Modal Content -->
                <div class="modal-content">

                @csrf
                <!-- Modal Header -->
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalCloseTitle">
                            Tutup LKS
                        </h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- /modal header -->

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <label for="close_ket">Keterangan</label>
                        <textarea name="close_ket" id="close_ket" cols="30" rows="10"></textarea>
                    </div>
                    <!-- /modal body -->

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button @click="processVerifikasi" id="simpanClose" type="button"
                                class="btn btn-primary btn-sm">
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

@push('javascript')
    <script src="https://cdn.tiny.cloud/1/hb65btdze8ubxfoabqu7fqjpuzpmx0c4k0je5f883m4l9ajf/tinymce/5/tinymce.min.js">
    </script>
    <script>
        new Vue({
            el: "#verifPage",
            data: {
                dataLKS: [],
                selectedRevisiLksID: 0,
                selectedCloseLksID: 0,
            },
            mounted() {
                this.getDataLKS('all', 'all')
            },
            methods: {
                doFilter() {
                    const auditor = $("#fil_auditor").val()
                    const status  = $("#fil_status").val()
                    this.getDataLKS(auditor, status)
                },
                buildTinyMCERevisi() {
                    tinyMCE.init({
                        autosave_ask_before_unload: false,
                        invalid_elements: "script",
                        selector: '#revisi_ket',
                        plugins: 'autosave link image lists',
                        relative_urls: false,
                        height: 300,
                        placeholder: 'Tuliskan Keterangan Revisi...',
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
                buildTinyMCEClose() {
                    tinyMCE.init({
                        autosave_ask_before_unload: false,
                        invalid_elements: "script",
                        selector: '#close_ket',
                        plugins: 'autosave link image lists',
                        relative_urls: false,
                        height: 300,
                        placeholder: 'Tuliskan Keterangan Verifikasi...',
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
                propmtRevisi(lksID) {
                    this.selectedRevisiLksID = lksID
                    $("#modalRevisi").modal('show')
                    setTimeout(() => {
                        this.buildTinyMCERevisi();
                        tinymce.get('revisi_ket').setContent('');
                    }, 300);
                },
                processRevisi() {
                    $("#simpanRevisi").attr('disabled', true);
                    let content = tinymce.get('revisi_ket').getContent();
                    this.revisi(this.selectedRevisiLksID, content)

                    $("#modalRevisi").modal('hide')
                    $("#simpanRevisi").removeAttr('disabled')
                },
                promptVerifikasi(lksID) {
                    this.selectedCloseLksID = lksID
                    $("#modalClose").modal('show')
                    setTimeout(() => {
                        this.buildTinyMCEClose();
                        tinymce.get('close_ket').setContent('');
                    }, 300);
                },
                processVerifikasi() {
                    const swalWithBootstrapButtons = swal.mixin({
                        confirmButtonClass: 'btn btn-success mb-2',
                        cancelButtonClass: 'btn btn-warning mr-2 mb-2',
                        buttonsStyling: false,
                    });

                    swalWithBootstrapButtons({
                        title: `Set Closed ?`,
                        text: "Pastikan keputusan yang anda pilih benar, jika anda yakin silakan klik Ya",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then(async (result) => {
                        if (result.value) {
                            $("#simpanClose").attr('disabled', true);
                            let content = tinymce.get('close_ket').getContent();
                            await this.verifikasi(this.selectedCloseLksID, content)

                            $("#modalClose").modal('hide')
                            $("#simpanClose").removeAttr('disabled')
                        }
                    });
                },
                async getDataLKS(auditor, status) {
                    $.get(`{!! url("$url/ajax?action=data-verif-lks&jadwal_id=$data->jadw_id") !!}&auditor=${auditor}&status=${status}`)
                        .then(response => {
                            this.dataLKS = response.results
                            console.log(this.dataLKS);
                        })
                        .fail((xhr) => {
                            if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                            else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                        });
                },
                async verifikasi(lksID, catatan) {
                    return new Promise((resolve, reject) => {
                        let formData = {
                            lks_catatan_ditutup: catatan,
                            lks_id: lksID,
                        }
                        $.post(`{{url("$url/temuan/$data->jadw_id/verifikasi")}}`, formData)
                            .then(response => {
                                toastCenter({
                                    type: 'success',
                                    title: response.message
                                })
                                this.doFilter()
                                resolve()
                            })
                            .fail((xhr) => {
                                if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                                reject();
                            });
                    })

                },
                async revisi(lksID, notes) {
                    return new Promise((resolve, reject) => {
                        let formData = {
                            catatan: notes,
                            lks_id: lksID,
                        }
                        $.post(`{{url("$url/temuan/$data->jadw_id/revisi")}}`, formData)
                            .then(response => {
                                toastCenter({
                                    type: 'success',
                                    title: response.message
                                })
                                this.doFilter()
                                resolve()
                            })
                            .fail((xhr) => {
                                if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                                reject();
                            });
                    })

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
        })
    </script>
@endpush
