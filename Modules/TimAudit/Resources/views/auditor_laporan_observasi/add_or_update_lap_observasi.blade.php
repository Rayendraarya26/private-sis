@extends('layouts.layout_app')

@section('title', 'Tambah / Perbarui Laporan Observasi')

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
                                LAPORAN OBSERVASI
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

                            <hr style="padding: 20px">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>KATEGORI</th>
                                    <th>JUMLAH</th>
                                    <th>NOMOR LKS</th>
                                    <th>KLAUSUL</th>
                                    <th>TANGGAL PENYELESAIAN</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Kritis</td>
                                    <td>{{$dataLKS['jumlah']['kritis']}}</td>
                                    <td>{{$dataLKS['no_lks']['kritis'] ?: '-' }}</td>
                                    <td>{{$dataLKS['klausul']['kritis'] ?: '-'}}</td>
                                    <td>{{$dataLKS['tgl_pelyelesaian']['kritis'] ?: '-'}}</td>
                                </tr>
                                <tr>
                                    <td>Mayor</td>
                                    <td>{{$dataLKS['jumlah']['mayor']}}</td>
                                    <td>{{$dataLKS['no_lks']['mayor'] ?: '-'}}</td>
                                    <td>{{$dataLKS['klausul']['mayor'] ?: '-'}}</td>
                                    <td>{{$dataLKS['tgl_pelyelesaian']['mayor'] ?: '-'}}</td>
                                </tr>
                                <tr>
                                    <td>Minor</td>
                                    <td>{{$dataLKS['jumlah']['minor']}}</td>
                                    <td>{{$dataLKS['no_lks']['minor'] ?: '-'}}</td>
                                    <td>{{$dataLKS['klausul']['minor'] ?: '-'}}</td>
                                    <td>{{$dataLKS['tgl_pelyelesaian']['minor'] ?: '-'}}</td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td>{{$dataLKS['jumlah']['total']}}</td>
                                    <td>{{$dataLKS['no_lks']['total'] ?: '-'}}</td>
                                    <td>{{$dataLKS['klausul']['total'] ?: '-'}}</td>
                                    <td>{{$dataLKS['tgl_pelyelesaian']['total'] ?: '-'}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                    <div class="dt-card">
                        <div class="dt-card__header">
                            <div class="dt-card__heading">
                                <h3 class="dt-card__title" style="text-align: center">
                                    TULIS LAPORAN OBSERVASI
                                </h3>
                            </div>
                        </div>
                        <div class="dt-card__body">
                            <div class="col-md-12">
                                <form action="{{ action("$module@processLaporan", $data->jadw_id) }}" method="post"
                                  enctype="multipart/form-data">
                                @csrf

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="obsvasi_uraian">
                                        Uraian Observasian *
                                        @error('obsvasi_uraian')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" placeholder="Masukkaan uraian..."
                                                  name="obsvasi_uraian"
                                                  aria-label="Uraian Observasi"
                                                  id="obsvasi_uraian">{{old('obsvasi_uraian') ?? $data->sis_audit_observasi?->obsvasi_uraian}}</textarea>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-paper-plane"></i> Submit
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

    <script>
        function initObservasianEditor() {
            tinyMCE.init({
                autosave_ask_before_unload: false,
                invalid_elements: "script",
                selector: '#obsvasi_uraian',
                plugins: 'autosave link image lists',
                relative_urls: false,
                height: 500,
                placeholder: 'Tuliskan kesimpulan...',
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

        $(document).ready(function () {
            initObservasianEditor();
        });
    </script>
@endpush
