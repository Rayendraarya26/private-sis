@extends('layouts.layout_app')

@section('title', 'Tambah Temuan LKS')

@push('css')
    <link rel="stylesheet" href="{{asset('assets/plugins/datetimepicker/tempusdominus-bootstrap-4.min.css')}}">
@endpush

@section('content')
    <div class="dt-content" id="temuanPage">
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
                        <div class="col-md-12">
                            <form method="post"
                                  action="{{action("$module@storeTemuan", $data->jadw_id)}}"
                                  enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3"
                                           for="jadw_audit_id">
                                        Jadwal Audit*
                                        @error('jadw_audit_id')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <select name="jadw_audit_id" class="form-control" id="jadw_audit_id">
                                            <option selected disabled>-- Pilih Jadwal Audit --</option>
                                            @foreach($data->sis_jadwal_audits as $ja)
                                                <option value="{{$ja->jadw_audit_id}}">
                                                    {{ucwords($ja->jadw_audit_jenis)}}
                                                    / {{ucwords($ja->jadw_audit_tujuan_audit)}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lks_kategori_ketidaksesuaian">
                                        Kategori Ketidaksesuaian*
                                        @error('lks_kategori_ketidaksesuaian')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <select name="lks_kategori_ketidaksesuaian" class="form-control"
                                                id="lks_kategori_ketidaksesuaian">
                                            <option selected disabled>-- Pilih Kategori --</option>
                                            <option value="observasi">Observasi</option>
                                            <option value="minor">Minor</option>
                                            <option value="mayor">Mayor</option>
                                            <option value="kritis">Kritis</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lks_klausul_ketidaksesuaian">
                                        Klausul Ketidaksesuaian*
                                        @error('lks_klausul_ketidaksesuaian')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" placeholder="Masukkaan klausul..."
                                                  name="lks_klausul_ketidaksesuaian"
                                                  aria-label="Klausul ketidaksesuaian"
                                                  id="lks_klausul_ketidaksesuaian">{{old('lks_klausul_ketidaksesuaian')}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lks_uraian_ketidaksesuaian">
                                        Uraian Ketidaksesuaian*
                                        @error('lks_uraian_ketidaksesuaian')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" placeholder="Masukkaan uraian..."
                                                  name="lks_uraian_ketidaksesuaian"
                                                  aria-label="Uraian ketidaksesuaian"
                                                  id="lks_uraian_ketidaksesuaian">{{old('lks_uraian_ketidaksesuaian')}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lks_expired_date_perbaikan">
                                        Tanggal makimal perbaikan untuk <b>client</b>*
                                        @error('lks_expired_date_perbaikan')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="lks_expired_date_perbaikan"
                                               class="form-control"
                                               data-toggle="datetimepicker" data-target="#lks_expired_date_perbaikan"
                                               id="lks_expired_date_perbaikan">
                                        <small><i>klik lagi untuk menghilangkan tanggal</i></small>
                                    </div>
                                </div>


                                <button class="btn btn-success btn-block"><i class="fas fa-paper-plane"></i> Submit
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('javascript')
    <script src="https://cdn.tiny.cloud/1/hb65btdze8ubxfoabqu7fqjpuzpmx0c4k0je5f883m4l9ajf/tinymce/5/tinymce.min.js">
    </script>
    <script src="{{asset('assets/plugins/datetimepicker/tempusdominus-bootstrap-4.min.js')}}"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        const swalWithBootstrapButtons = swal.mixin({
            confirmButtonClass: 'btn btn-primary mb-2',
            cancelButtonClass: 'btn btn-warning mr-2 mb-2',
            buttonsStyling: false,
        });


        function initUraianEditor() {
            tinyMCE.init({
                invalid_elements: "script",
                selector: '#lks_uraian_ketidaksesuaian',
                plugins: 'autosave link image lists',
                relative_urls: false,
                height: 500,
                placeholder: 'Tuliskan uraian ketidaksesuaian...',
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

        function initKlausulEditor() {
            tinyMCE.init({
                invalid_elements: "script",
                selector: '#lks_klausul_ketidaksesuaian',
                plugins: 'autosave link image lists',
                relative_urls: false,
                height: 500,
                placeholder: 'Tuliskan klausul ketidaksesuaian...',
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

        function initDatePerbaikan() {
            $('#lks_expired_date_perbaikan').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss',
                showClose: true,
            });
        }


        $(document).ready(function () {
            initUraianEditor();
            initKlausulEditor();
            initDatePerbaikan();

            @if(old('jadw_audit_id'))
            $("#jadw_audit_id").val('{{old("jadw_audit_id")}}')
            @endif

            @if(old('lks_kategori_ketidaksesuaian'))
            $("#lks_kategori_ketidaksesuaian").val('{{old("lks_kategori_ketidaksesuaian")}}')
            @endif
        });
    </script>
@endpush
