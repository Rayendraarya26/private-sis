@extends("layouts.layout_app")

@section('title', 'Persetujuan Tanggal')

@section('content')
    <div class="dt-content" id="approvalPage">
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
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="dt-card__title">Data Tim</h3>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-form-label col-sm-3">Nama Perusahaan</div>
                                    <div class="col-sm-8">
                                        {{$data->sis_pelanggan->cust_nama}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-form-label col-sm-3">Alamat Perusahaan</div>
                                    <div class="col-sm-8">
                                        {{$data->sis_pelanggan->cust_alamat}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-form-label col-sm-3">No. Referensi</div>
                                    <div class="col-sm-8">
                                        @foreach($data->sis_jadwal_audits as $audit)
                                            {{$audit->jadw_audit_nomor_referensi . (!$loop->last ? ' - ' : '.')}}
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-form-label col-sm-3">Kode NACE</div>
                                    <div class="col-sm-8">
                                        @foreach($data->sis_jadwal_audits as $audit)
                                            {{$audit->jadw_audit_kode_nace . (!$loop->last ? ' - ' : '.')}}
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-form-label col-sm-3">EA Code</div>
                                    <div class="col-sm-8">
                                        @foreach($data->sis_jadwal_audits as $audit)
                                            {{$audit->jadw_audit_kode_ea . (!$loop->last ? ' - ' : '.')}}
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-form-label col-sm-3">Ruang Lingkup</div>
                                    <div class="col-sm-8">
                                        @foreach($data->sis_jadwal_audits as $audit)
                                            {{$audit->jadw_audit_ruang_lingkup . (!$loop->last ? ' - ' : '.')}}
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-form-label col-sm-3">Komoditas</div>
                                    <div class="col-sm-8">
                                        <ol>
                                            @foreach($data->sis_jadwal_audits as $audit)
                                                <li>{{$audit->jadw_audit_sni}}
                                                    {{$audit->jadw_audit_merk}}
                                                    {{$audit->jadw_audit_tipe}}
                                                    {{$audit->jadw_audit_ukuran}}</li>
                                            @endforeach
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-form-label col-sm-3">Standar Acuan Kegiatan</div>
                                    <div class="col-sm-8">
                                        @foreach($data->sis_jadwal_audits as $audit)
                                            {{$audit->jadw_audit_kegiatan . (!$loop->last ? ' - ' : '.')}}
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-form-label col-sm-3">Tujuan Audit</div>
                                    <div class="col-sm-8">
                                        @foreach($data->sis_jadwal_audits as $audit)
                                            {{$audit->jadw_audit_tujuan_audit . (!$loop->last ? ' - ' : '.')}}
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <h3 class="dt-card__title">Data Tim</h3>
                                <div class="form-group row">
                                    <div class="col-form-label col-sm-3">Tim Asesmen</div>
                                    <div class="col-sm-8">
                                        <ol>
                                            @foreach($data->sis_jadwal_tims as $tim)
                                                <li>{{ucwords($tim->master_pegawai->peg_nama)}}
                                                    : {{ucwords($tim->jadw_tim_posisi)}}</li>
                                            @endforeach
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <form method="post"
                                      onsubmit="$('#btnSubmit').attr('disabled',true)"
                                      action="{{action("$module@processApproveTim", $data->jadw_id)}}"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3">Persetujuan Anda*</label>
                                        <div class="col-sm-8">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input value="accepted" type="radio" name="jadw_team_status"
                                                       id="jadwal_status1" class="custom-control-input"
                                                       v-model="m_jadwal_status">
                                                <label class="custom-control-label"
                                                       for="jadwal_status1">Setuju</label>
                                            </div>
                                            <br><br>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input value="revisi" type="radio" id="jadwal_status2"
                                                       name="jadw_team_status"
                                                       class="custom-control-input" v-model="m_jadwal_status"
                                                       aria-label="revisi">
                                                <label class="custom-control-label"
                                                       for="jadwal_status2">Revisi</label>


                                                <div style="padding-top: 30px" v-if="m_jadwal_status == 'revisi'">
                                                <textarea class="form-control" placeholder="Masukkaan deskripsi..."
                                                          name="editor_revisi" aria-label="editor revisi"
                                                          id="editor_revisi">{{old('editor_revisi')}}</textarea>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success" id="btnSubmit">
                                        <i class="fas fa-paper-plane"></i> Kirim
                                    </button>
                                </form>
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
    <script>
        $(document).ready(function () {
            new Vue({
                el: "#approvalPage",
                data: {
                    m_jadwal_status: null
                },
                mounted() {

                },
                watch: {
                    m_jadwal_status: function (val) {
                        if (val == "revisi") {
                            setTimeout(() => this.buildTinyMCE(), 200)
                        } else {
                            this.destroyTinyMCE();
                        }
                    }
                },
                methods: {
                    buildTinyMCE() {
                        tinyMCE.init({
                            autosave_ask_before_unload: false,
                            invalid_elements: "script",
                            selector: '#editor_revisi',
                            plugins: 'autosave link image lists',
                            relative_urls: false,
                            height: 500,
                            placeholder: 'Tuliskan keterangan perubahan yang anda inginkan...',
                            images_reuse_filename: true,
                            automatic_uploads: true,
                            images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
                            images_upload_credentials: true,
                            toolbar: [{
                                name: 'history',
                                items: ['undo', 'redo']
                            },
                                {
                                    name: 'styles',
                                    items: ['styleselect']
                                },
                                {
                                    name: 'formatting',
                                    items: ['bold', 'italic']
                                },
                                {
                                    name: 'alignment',
                                    items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']
                                },
                                {
                                    name: 'list',
                                    items: ['bullist', 'numlist']
                                },
                                {
                                    name: 'indentation',
                                    items: ['outdent', 'indent']
                                },
                                {
                                    name: 'link',
                                    items: ['link', 'image']
                                },
                                {
                                    name: 'restore',
                                    items: ['restoredraft']
                                },
                            ],
                        });
                    },
                    destroyTinyMCE() {
                        tinymce.remove("#editor_revisi");
                    }
                }
            });
        });
    </script>
@endpush
