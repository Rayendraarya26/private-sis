@extends('layouts.layout_app')

@section('title', 'Verifikasi Laporan Lengkap')

@push('css')
    <style>
        .borderless tr td, .borderless th {
            border: none;
        }
    </style>
@endpush

@section('content')
    <div class="dt-content" id="laporanPage">
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
                                LAPORAN LENGKAP HASIL AUDIT
                            </h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">
                                    II. Umum
                                </label>
                                <div class="col-sm-8">
                                    <table class="table borderless">
                                        <tr>
                                            <td>Tahap Kegiatan</td>
                                            <td>:
                                                @foreach($data->sis_jadwal_audits as $audit)
                                                    {{$audit->jadw_audit_kegiatan . (!$loop->last ? ' - ' : '.')}}
                                                @endforeach
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Tanggal Pelaksanaan</td>
                                            <td>
                                                : {{ $data->jadw_tanggal_mulai->isoFormat("LL") }}
                                                s/d {{ $data->jadw_tanggal_selesai->isoFormat("LL") }}</td>
                                        </tr>

                                        <tr>
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
                                            <td>Jumlah Karyawan</td>
                                            @php($dataPemohon = $data->sis_jadwal_audits()->groupBy('mohon_id')->first()->sis_permohonan)
                                            <td>
                                                : {{$dataPemohon->mohon_cust_jumlah_operasional + $dataPemohon->mohon_cust_jumlah_bagian + $dataPemohon->mohon_cust_jumlah_manajemen + $dataPemohon->mohon_cust_jumlah_administrasi + $dataPemohon->mohon_cust_jumlah_part_time + $dataPemohon->mohon_cust_jumlah_non_permanen }}</td>
                                        </tr>
                                        <tr>
                                            <td>Ruang Lingkup <i>(Nace Code)</i></td>
                                            <td>:
                                                @if($data->sis_jadwal_audits->count() > 1)
                                                    <ol>
                                                        @foreach($data->sis_jadwal_audits as $audit)
                                                            <li>{{$audit->jadw_audit_ruang_lingkup . ' - ' . $audit->jadw_audit_kode_nace . (!$loop->last ? ' ; ' : '.')}}</li>
                                                        @endforeach
                                                    </ol>
                                                @else
                                                    @foreach($data->sis_jadwal_audits as $audit)
                                                        {{$audit->jadw_audit_ruang_lingkup . ' - ' . $audit->jadw_audit_kode_nace . (!$loop->last ? ' ; ' : '.')}}
                                                    @endforeach
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Komoditas</td>
                                            <td>:
                                                @foreach($data->sis_jadwal_audits as $audit)
                                                    @if($audit->master_komoditi->komodt_nama != "")
                                                        {{$audit->master_komoditi->komodt_nama . (!$loop->last ? ' ; ' : '.')}}
                                                    @endif
                                                @endforeach
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Kapasitas Produksi</td>
                                            <td>:
                                                @foreach($data->sis_jadwal_audits as $audit)
                                                    {{$audit->jadw_audit_kapasitas_produksi_tahunan . '/' . $audit->jadw_audit_kapasitas_produksi_tahunan_satuan . (!$loop->last ? ' ; ' : '.')}}
                                                @endforeach
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Alamat</td>
                                            <td>: {{$dataPemohon->mohon_cust_alamat}} </td>
                                        </tr>

                                        <tr>
                                            <td>Tujuan Audit</td>
                                            <td>:
                                                @foreach($data->sis_jadwal_audits as $audit)
                                                    {{$audit->jadw_audit_tujuan_audit . (!$loop->last ? ' ; ' : '.')}}
                                                @endforeach
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Jenis Audit</td>
                                            <td>:
                                                @foreach($data->sis_jadwal_audits()->groupBy('jadw_audit_jenis')->get() as $audit)
                                                    Audit {{ucwords($audit->jadw_audit_jenis) . (!$loop->last ? ' ; ' : '.')}}
                                                @endforeach
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>


                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">
                                    III. Susunan TIM Audit
                                </label>
                                <div class="col-sm-8">
                                    <ol>
                                        @foreach($data->sis_jadwal_tims as $tim)
                                            <li>
                                                {{ucwords($tim->jadw_tim_posisi)}}:
                                                {{$tim->master_pegawai->peg_nama}}
                                            </li>
                                        @endforeach
                                    </ol>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">
                                    IV. Jumlah Temuan LKS
                                </label>
                                <div class="col-sm-8">
                                    <ol>
                                        <li>Kritis: {{$dataLKS['jumlah']['kritis']}}</li>
                                        <li>Mayor: {{$dataLKS['jumlah']['mayor']}}</li>
                                        <li>Minor: {{$dataLKS['jumlah']['minor']}}</li>
                                        <br>
                                        <li>Total: {{$dataLKS['jumlah']['total']}}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title" style="text-align: center">
                                TULIS LAPORAN LENGKAP
                            </h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="col-md-12">
                            <form action="{{ action("$module@processLaporan", $data->jadw_id) }}" method="post"
                                  enctype="multipart/form-data">
                                @csrf

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">
                                        V. Penilaian secara umum penerapan SMM/SML/SPPT SNI
                                    </label>
                                    <div class="col-sm-8">
                                        {!! $data->sis_audit_lap_lengkap?->lap_lengkp_penilaian ?? '-' !!}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">
                                        VI. Penyimpangan dari Program Audit dan Alasannya
                                    </label>
                                    <div class="col-sm-8">
                                        {!! $data->sis_audit_lap_lengkap?->lap_lengkp_penyimpangan ?? '-' !!}
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">
                                        VII. Isu (masalah) Signifikan yang Berdampak Terhadap Program Audit
                                    </label>
                                    <div class="col-sm-8">
                                        {!! $data->sis_audit_lap_lengkap?->lap_lengkp_isu_berdampak ?? '-' !!}
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">
                                        VI. Penyimpangan dari Program Audit dan Alasannya
                                    </label>
                                    <div class="col-sm-8">
                                        {!! $data->sis_audit_lap_lengkap?->lap_lengkp_penyimpangan ?? '-' !!}
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">
                                        VIII. Isu-isu (permasalahan) yang Tidak Terselesaikan (jika teridentifikasi)
                                    </label>
                                    <div class="col-sm-8">
                                        {!! $data->sis_audit_lap_lengkap?->lap_lengkp_isu_tidak_terselesaikan ?? '-' !!}
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_perubahan">
                                        IX. Perubahan Signifikan (jikan
                                        Perusahaan
                                    </label>
                                    <div class="col-sm-8">
                                        {!! $data->sis_audit_lap_lengkap?->lap_lengkp_perubahan ?? '-' !!}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">
                                        XI. Kelemahan
                                    </label>
                                    <div class="col-sm-8">
                                        {!! $data->sis_audit_lap_lengkap?->lap_lengkp_kelemahan ?? '-' !!}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">
                                        XII. Tinjauan terhadap Keluhan Pelanggan
                                    </label>
                                    <div class="col-sm-8">
                                        {!! $data->sis_audit_lap_lengkap?->lap_lengkp_tinjauan_keluhan ?? '-' !!}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">
                                        XIII. Pengendalian Penggunaan Tanda Sertifikat Lembaga dan atau Tanda SNI
                                    </label>
                                    <div class="col-sm-8">
                                        {!! $data->sis_audit_lap_lengkap?->lap_lengkp_pengendalian_penggunaan ?? '-' !!}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_kedalaman_audit">
                                        XIV. Kedalaman Audit Internal dant
                                        sebelumnya (bila ada)
                                    </label>
                                    <div class="col-sm-8">
                                        {!! $data->sis_audit_lap_lengkap?->lap_lengkp_kedalaman_audit ?? '-' !!}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">
                                        XV. Pernyataan kesesuaian dan efektifitas pelaksanaan sistem manajemen
                                    </label>
                                    <div class="col-sm-8">
                                        {!! $data->sis_audit_lap_lengkap?->lap_lengkp_pernyataan_kesesuaian ?? '-' !!}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">
                                        XVI. Kesimpulan ketaatan terhadap lingkup sertifikasi
                                    </label>
                                    <div class="col-sm-8">
                                        {!! $data->sis_audit_lap_lengkap?->lap_lengkp_kesimpulan_ketaatan ?? '-' !!}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">
                                        XVII. Konfirmasi bahwa tujuan audit telah terpenuhi
                                    </label>
                                    <div class="col-sm-8">
                                        {!! $data->sis_audit_lap_lengkap?->lap_lengkp_konfirmasi_tujuan ?? '-' !!}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">
                                        XVIII. Saran untuk Tim berikutnya
                                    </label>
                                    <div class="col-sm-8">
                                        {!! $data->sis_audit_lap_lengkap?->lap_lengkp_saran ?? '-' !!}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">
                                        XIX. Kesimpulan
                                    </label>
                                    <div class="col-sm-8">
                                        {!! $data->sis_audit_lap_lengkap?->lap_lengkp_kesimpulan ?? '-' !!}
                                    </div>
                                </div>

                                <a href="{{url($url)}}" class="btn btn-default">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
