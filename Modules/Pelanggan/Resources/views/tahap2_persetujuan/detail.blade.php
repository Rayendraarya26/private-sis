@extends('layouts.layout_app')

@section('title', 'Detail')

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
                                INFORMASI AUDIT
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
                                    <td>Ruang Lingkup <i>(Nace Code)</i></td>
                                    <td>:
                                        @if($dataJadwal->sis_jadwal_audits->count() > 1)
                                            <ol>
                                                @foreach($dataJadwal->sis_jadwal_audits as $audit)
                                                    <li>{{$audit->jadw_audit_ruang_lingkup . ' - ' . $audit->jadw_audit_kode_nace . (!$loop->last ? ' ; ' : '.')}}</li>
                                                @endforeach
                                            </ol>
                                        @else
                                            @foreach($dataJadwal->sis_jadwal_audits as $audit)
                                                {{$audit->jadw_audit_ruang_lingkup . ' - ' . $audit->jadw_audit_kode_nace . (!$loop->last ? ' ; ' : '.')}}
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td>4</td>
                                    <td>Tanggal Audit</td>
                                    <td>
                                        : {{ $dataJadwal->jadw_tanggal_mulai->isoFormat("LL") }}
                                        s/d {{ $dataJadwal->jadw_tanggal_selesai->isoFormat("LL") }}

                                        @if($dataJadwal->jadw_file_jadwal != '') | <a
                                            href="{{ url($dataJadwal->jadw_file_jadwal) }}" target="_blank">Download
                                            Jadwal</a>@endif
                                    </td>
                                </tr>

                                <tr>
                                    <td>5</td>
                                    <td>Tim Audit</td>
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
                                    <td>6</td>
                                    <td>Standar Acuan</td>
                                    <td>:
                                        @foreach($dataJadwal->sis_jadwal_audits as $audit)
                                            @if($audit->jadw_audit_standart_acuan != "")
                                                {{$audit->jadw_audit_standart_acuan . (!$loop->last ? ' ; ' : '.')}}
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td>Jumlah Temuan LKS</td>
                                    <td>:
                                        <ul>
                                            <li>Kritis: {{$dataLKS['jumlah']['kritis']}}</li>
                                            <li>Mayor: {{$dataLKS['jumlah']['mayor']}}</li>
                                            <li>Minor: {{$dataLKS['jumlah']['minor']}}</li>
                                            <br>
                                            <li>Total: {{$dataLKS['jumlah']['total']}}</li>
                                        </ul>
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
                                PROSES AUDIT
                            </h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2">
                                    Laporan Ringkas
                                </label>
                                <div class="col-sm-10">
                                    <a href="{{ url("$url/cetak/$dataJadwal->jadw_id/lap-ringkas") }}"
                                       target="_blank"><i class="fad fa-download"></i> Download</a>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2">
                                    LKS
                                </label>
                                <div class="col-sm-10">
                                    <a href="{{ url("$url/cetak/$dataJadwal->jadw_id/lks") }}" target="_blank"><i
                                            class="fad fa-download"></i> Download</a>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2">
                                    Daftar Periksa File Upload Tim
                                </label>
                                <div class="col-sm-10">
                                    <table class="table table-bordered mb-0">
                                        <tr>
                                            <th>Nama</th>
                                            <th>Posisi</th>
                                            <th>File Daftar Periksa</th>
                                        </tr>
                                        @foreach($dataJadwal->sis_jadwal_tims as $tim)
                                            <tr>
                                                <td>{{$tim->peg_nama}} ({{$tim->jadw_tim_kode}})</td>
                                                <td>{{ucwords($tim->jadw_tim_posisi)}}</td>
                                                <td>@if($tim->dftr_periksa_file != '')<a
                                                        href="{{ url($tim->dftr_periksa_file) }}" target="_blank"><i
                                                            class="fad fa-download"></i> Download</a>@endif</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2">
                                    Logbook Tim
                                </label>
                                <div class="col-sm-10">
                                    <table class="table table-bordered mb-0">
                                        <tr>
                                            <th>Nama</th>
                                            <th>Posisi</th>
                                            <th>File Logbook</th>
                                        </tr>
                                        @foreach($dataJadwal->sis_jadwal_tims as $tim)
                                            <tr>
                                                <td>{{$tim->peg_nama}} ({{$tim->jadw_tim_kode}})</td>
                                                <td>{{ucwords($tim->jadw_tim_posisi)}}</td>
                                                <td>
                                                    @if($tim->sis_audit_logbook?->logbook_filepath != '')
                                                        <a href="{{ url($tim->sis_audit_logbook->logbook_filepath) }}"
                                                           target="_blank">
                                                            <i class="fad fa-download"></i>
                                                            Download
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                            @if(!empty($dataJadwal->sis_audit_ppcs))
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-2">
                                        Laporan PPC
                                    </label>
                                    <div class="col-sm-10">
                                        <table class="table table-bordered mb-0">
                                            <tr>
                                                <th>Jenis File Laporan</th>
                                                <th>Download File</th>
                                            </tr>
                                            @foreach($dataJadwal->sis_audit_ppcs as $ppc)
                                                <tr>
                                                    <td>
                                                        @if($ppc->audit_ppc_jenis_file == '19')
                                                            19. RENCANA PENGAMBILAN CONTOH
                                                        @elseif($ppc->audit_ppc_jenis_file == '20')
                                                            20. BERITA ACARA PENGAMBILAN CONTOH
                                                        @elseif($ppc->audit_ppc_jenis_file == '21')
                                                            21. LABEL CONTOH UJI
                                                        @elseif($ppc->audit_ppc_jenis_file == '22')
                                                            22. LAPORAN KEGIATAN PENGAMBILAN CONTOH
                                                        @endif
                                                    </td>
                                                    <td>@if($ppc->audit_ppc_filepath != '')<a
                                                            href="{{ url($ppc->audit_ppc_filepath) }}"
                                                            target="_blank"><i class="fad fa-download"></i>
                                                            Download</a>@endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @endif
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
