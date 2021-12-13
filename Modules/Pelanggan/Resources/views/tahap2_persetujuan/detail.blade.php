@extends('layouts.layout_app')

@section('title', 'Detail Temuan LKS')

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
                                        @foreach($dataJadwal->sis_jadwal_audits as $audit)
                                            {{$audit->jadw_audit_kegiatan . (!$loop->last ? ' - ' : '.')}}
                                        @endforeach
                                    </td>
                                </tr>

                                <tr>
                                    <td rowspan="3">2</td>
                                    <td>Nama Perusahaan</td>
                                    <td>: {{$dataJadwal->sis_pelanggan->cust_nama}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>No. Referensi</td>
                                    <td>:
                                        @foreach($dataJadwal->sis_jadwal_audits as $audit)
                                            @if($audit->jadw_audit_nomor_referensi != "")
                                                {{$audit->jadw_audit_nomor_referensi . (!$loop->last ? ' ; ' : '.')}}
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>: {{$dataJadwal->sis_pelanggan->cust_alamat}}
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td>Tanggal Asesmen</td>
                                    <td>
                                        : {{ $dataJadwal->jadw_tanggal_mulai->isoFormat("LL") }}
                                        s/d {{ $dataJadwal->jadw_tanggal_selesai->isoFormat("LL") }}</td>
                                </tr>

                                <tr>
                                    <td>4</td>
                                    <td>Tim Asesmen</td>
                                    <td>:
                                        <ol>
                                            @foreach($dataJadwal->sis_jadwal_tims as $tim)
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
                                        @foreach($dataJadwal->sis_jadwal_audits as $audit)
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
                                Temuan LKS
                            </h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="col-md-12">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Auditor</th>
                                    <th>Uraian Ketidaksesuaian</th>
                                    <th>Tindakan Perbaikan <br>
                                        <i>(Disertai analisis penyebab, Koreksi, dan Tindakan Koreksi)</i>
                                    </th>
                                    <th>Bukti Tindakan Perbaikan</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($dataLks as $lks)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$lks->sis_jadwal_tim->jadw_tim_kode}}</td>
                                        <td>
                                            {!! $lks->lks_uraian_ketidaksesuaian !!}
                                            <br>
                                            Kategori ketidaksesuaian: {{ucwords($lks->lks_kategori_ketidaksesuaian)}}
                                            <br>
                                            Klausul ketidak sesuaian: {!! $lks->lks_klausul_ketidaksesuaian !!}
                                        </td>
                                        <td>
                                            {!! $lks->lks_perbaikan_analisa !!}
                                            {!! $lks->lks_perbaikan_koreksi !!}
                                            {!! $lks->lks_perbaikan_tindakan !!}
                                        </td>
                                        <td>
                                            {!! $lks->lks_bukti_tindakan_perbaikan !!}

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
                        @if($dataJadwal->jadw_setujui_temuan == 'diajukan')
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-8" style="text-align: center; justify-content: center">
                                        <button class="btn btn-danger" id="revisiTemuan"
                                                onclick="promptRevisi({{$dataJadwal->jadw_id}})">
                                            <i class="fas fa-window-close"></i>
                                            Revisi Temuan
                                        </button>
                                        &nbsp;
                                        <button class="btn btn-success" onclick="promptAgree({{$dataJadwal->jadw_id}})"
                                                id="agreeTemuan">
                                            <i class="fas fa-check-circle"></i>
                                            Setujui Temuan
                                        </button>
                                    </div>
                                    <div class="col-sm-2"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('javascript')
    <script>
        @if($dataJadwal->jadw_setujui_temuan == 'diajukan')
        function promptRevisi(id) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-default mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: 'Keterangan Revisi',
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Revisi',
                cancelButtonText: 'Batal',
                closeOnConfirm: false,
                closeOnCancel: false,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    submitApproval(id, 'revisi', result.value)
                }
            });
        }

        function promptAgree(id) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-success mb-2',
                cancelButtonClass: 'btn btn-danger mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: 'Setujui Temuan ?',
                html: `Keputusan ini bersifat permanen dan tidak dapat dikembalikan<br><br> tekan ESC untuk batal`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
                closeOnConfirm: false,
                closeOnCancel: false,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    submitApproval(id, 'setuju', null)
                }
            });
        }

        function submitApproval(id, status, message) {
            $("#agreeTemuan").attr("disabled", true)
            $("#revisiTemuan").attr("disabled", true)
            $.ajax({
                url: `{{url("$url/approve/temuan")}}`,
                type: 'POST',
                dataType: 'json',
                data: {jadw_id: id, jadw_setujui_temuan: status, message},
                success: function (response) {
                    toastCenter({
                        type: 'success',
                        title: response.message
                    })

                    location.href = "/{{$url}}"
                },
                error: function (xhr) {
                    if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                    else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                }
            });
        }
        @endif
    </script>
@endpush
