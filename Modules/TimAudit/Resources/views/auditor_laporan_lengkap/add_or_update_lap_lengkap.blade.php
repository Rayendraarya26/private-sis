@extends('layouts.layout_app')

@section('title', 'Tambah / Perbarui Laporan Lengkap')

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
                                                : {{($dataPemohon->mohon_cust_jumlah_operasional ?? 0)  + ($dataPemohon->mohon_cust_jumlah_bagian ?? 0) + ($dataPemohon->mohon_cust_jumlah_manajemen ?? 0) + ($dataPemohon->mohon_cust_jumlah_administrasi ?? 0) + ($dataPemohon->mohon_cust_jumlah_part_time ?? 0) + ($dataPemohon->mohon_cust_jumlah_non_permanen ?? 0) }}</td>
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
                                            <td>: {{$dataPemohon?->mohon_cust_alamat}} </td>
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
                                        <li>Observasi: {{$dataLKS['jumlah']['observasi']}}</li>
                                        <br>
                                        <li>Total: {{$dataLKS['jumlah']['total']}}</li>
                                    </ol>
                                </div>
                            </div>
							@if($data->sis_audit_lap_lengkap?->lap_lengkp_revisi_note !== '')
							<div class="form-group row" style="color:red;">
                                <label class="col-form-label col-sm-3">
                                    V. Revisi Verifikasi Laporan
                                </label>
                                <div class="col-sm-8">
                                    {!! $data->sis_audit_lap_lengkap?->lap_lengkp_revisi_note ?? '-' !!}
                                </div>
                            </div>
							@endif
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
                            <form id="addForm" action="{{ action("$module@processLaporan", $data->jadw_id) }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_penilaian">
                                        V. Penilaian secara umum penerapan SMM/SML/SPPT SNI
                                        @error('lap_lengkp_penilaian')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="editor form-control" placeholder="..."
                                                  name="lap_lengkp_penilaian"
                                                  aria-label=""
                                                  id="lap_lengkp_penilaian">{{old('lap_lengkp_penilaian') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_penilaian}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_penyimpangan">
                                        VI. Penyimpangan dari Program Audit dan Alasannya
                                        @error('lap_lengkp_penyimpangan')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="editor form-control" placeholder="..."
                                                  name="lap_lengkp_penyimpangan"
                                                  aria-label=""
                                                  id="lap_lengkp_penyimpangan">{{old('lap_lengkp_penyimpangan') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_penyimpangan}}</textarea>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_isu_berdampak">
                                        VII. Isu (masalah) Signifikan yang Berdampak Terhadap Program Audit
                                        @error('lap_lengkp_isu_berdampak')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="editor form-control" placeholder="..."
                                                  name="lap_lengkp_isu_berdampak"
                                                  aria-label=""
                                                  id="lap_lengkp_isu_berdampak">{{old('lap_lengkp_isu_berdampak') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_isu_berdampak}}</textarea>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_penyimpangan">
                                        VI. Penyimpangan dari Program Audit dan Alasannya
                                        @error('lap_lengkp_penyimpangan')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="editor form-control" placeholder="..."
                                                  name="lap_lengkp_penyimpangan"
                                                  aria-label=""
                                                  id="lap_lengkp_penyimpangan">{{old('lap_lengkp_penyimpangan') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_penyimpangan}}</textarea>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_isu_tidak_terselesaikan">
                                        VIII. Isu-isu (permasalahan) yang Tidak Terselesaikan (jika teridentifikasi)
                                        @error('lap_lengkp_isu_tidak_terselesaikan')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="editor form-control" placeholder="..."
                                                  name="lap_lengkp_isu_tidak_terselesaikan"
                                                  aria-label=""
                                                  id="lap_lengkp_isu_tidak_terselesaikan">{{old('lap_lengkp_isu_tidak_terselesaikan') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_isu_tidak_terselesaikan}}</textarea>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_perubahan">
                                        IX. Perubahan Signifikan (jika ada) yang Mempengaruhi Sistem Manajemen
                                        Perusahaan
                                        @error('lap_lengkp_perubahan')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="editor form-control" placeholder="..."
                                                  name="lap_lengkp_perubahan"
                                                  aria-label=""
                                                  id="lap_lengkp_perubahan">{{old('lap_lengkp_perubahan') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_perubahan}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_kelemahan">
                                        XI. Kelemahan
                                        @error('lap_lengkp_kelemahan')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="editor form-control" placeholder="..."
                                                  name="lap_lengkp_kelemahan"
                                                  aria-label=""
                                                  id="lap_lengkp_kelemahan">{{old('lap_lengkp_kelemahan') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_kelemahan}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_tinjauan_keluhan">
                                        XII. Tinjauan terhadap Keluhan Pelanggan
                                        @error('lap_lengkp_tinjauan_keluhan')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="editor form-control" placeholder="..."
                                                  name="lap_lengkp_tinjauan_keluhan"
                                                  aria-label=""
                                                  id="lap_lengkp_tinjauan_keluhan">{{old('lap_lengkp_tinjauan_keluhan') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_tinjauan_keluhan}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_pengendalian_penggunaan">
                                        XIII. Pengendalian Penggunaan Tanda Sertifikat Lembaga dan atau Tanda SNI
                                        @error('lap_lengkp_pengendalian_penggunaan')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="editor form-control" placeholder="..."
                                                  name="lap_lengkp_pengendalian_penggunaan"
                                                  aria-label=""
                                                  id="lap_lengkp_pengendalian_penggunaan">{{old('lap_lengkp_pengendalian_penggunaan') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_pengendalian_penggunaan}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_kedalaman_audit">
                                        XIV. Kedalaman Audit Internal dan Tinjauan Manajemen. Verifikasi TK /P audit
                                        sebelumnya (bila ada)
                                        @error('lap_lengkp_kedalaman_audit')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="editor form-control" placeholder="..."
                                                  name="lap_lengkp_kedalaman_audit"
                                                  aria-label=""
                                                  id="lap_lengkp_kedalaman_audit">{{old('lap_lengkp_kedalaman_audit') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_kedalaman_audit}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_pernyataan_kesesuaian">
                                        XV. Pernyataan kesesuaian dan efektifitas pelaksanaan sistem manajemen
                                        @error('lap_lengkp_pernyataan_kesesuaian')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="editor form-control" placeholder="..."
                                                  name="lap_lengkp_pernyataan_kesesuaian"
                                                  aria-label=""
                                                  id="lap_lengkp_pernyataan_kesesuaian">{{old('lap_lengkp_pernyataan_kesesuaian') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_pernyataan_kesesuaian}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_kesimpulan_ketaatan">
                                        XVI. Kesimpulan ketaatan terhadap lingkup sertifikasi
                                        @error('lap_lengkp_kesimpulan_ketaatan')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="editor form-control" placeholder="..."
                                                  name="lap_lengkp_kesimpulan_ketaatan"
                                                  aria-label=""
                                                  id="lap_lengkp_kesimpulan_ketaatan">{{old('lap_lengkp_kesimpulan_ketaatan') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_kesimpulan_ketaatan}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_konfirmasi_tujuan">
                                        XVII. Konfirmasi bahwa tujuan audit telah terpenuhi
                                        @error('lap_lengkp_konfirmasi_tujuan')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="editor form-control" placeholder="..."
                                                  name="lap_lengkp_konfirmasi_tujuan"
                                                  aria-label=""
                                                  id="lap_lengkp_konfirmasi_tujuan">{{old('lap_lengkp_konfirmasi_tujuan') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_konfirmasi_tujuan}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_saran">
                                        XVIII. Saran untuk Tim berikutnya
                                        @error('lap_lengkp_saran')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="editor form-control" placeholder="..."
                                                  name="lap_lengkp_saran"
                                                  aria-label=""
                                                  id="lap_lengkp_saran">{{old('lap_lengkp_saran') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_saran}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_kesimpulan">
                                        XIX. Kesimpulan
                                        @error('lap_lengkp_kesimpulan')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="editor form-control" placeholder="..."
                                                  name="lap_lengkp_kesimpulan"
                                                  aria-label=""
                                                  id="lap_lengkp_kesimpulan">{{old('lap_lengkp_kesimpulan') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_kesimpulan}}</textarea>
                                    </div>
                                </div>

								<div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_verifikasi_oleh">
                                        Nama Persetujuan
                                        @error('lap_lengkp_verifikasi_oleh')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="..." name="lap_lengkp_verifikasi_oleh" id="lap_lengkp_verifikasi_oleh" value="{{old('lap_lengkp_verifikasi_oleh') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_verifikasi_oleh}}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_verifikasi_jabatan">
                                        Jabatan Persetujuan
                                        @error('lap_lengkp_verifikasi_jabatan')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="..." name="lap_lengkp_verifikasi_jabatan" id="lap_lengkp_verifikasi_jabatan" value="{{old('lap_lengkp_verifikasi_jabatan') ?? $data->sis_audit_lap_lengkap?->lap_lengkp_verifikasi_jabatan}}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_lengkp_verifikasi_diajukan">
                                        Simpan sebagai Draft?
                                        @error('lap_lengkp_verifikasi_diajukan')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
									<div class="col-md-8">
									  <div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="lap_lengkp_verifikasi_diajukan" value="ya" @if(isset($data->lap_lengkp_verifikasi_diajukan)) @if($data->lap_lengkp_verifikasi_diajukan == 'ya') checked @endif @endif id="draft1">
										<label class="form-check-label" for="draft1">Tidak</label>
									  </div>
									  <div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="lap_lengkp_verifikasi_diajukan" value="tidak" @if(isset($data->lap_lengkp_verifikasi_diajukan)) @if($data->lap_lengkp_verifikasi_diajukan == 'tidak') checked @endif @endif id="draft2">
										<label class="form-check-label" for="draft2">Ya</label>
									  </div>
										<br>
										<small>Jika diisi tidak, maka akan diajukan ke koordinator sertifikasi untuk disetujui, setelah disetujui maka tidak bisa diedit.
										</small>
									</div>
								</div>

                                <button type="button" class="btn btn-outline-primary btn-block" id="btnSubmit">
                                        <i class="icon icon-feedback icon-fw icon-xl"></i> Simpan
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
    <script src="https://cdn.tiny.cloud/1/hb65btdze8ubxfoabqu7fqjpuzpmx0c4k0je5f883m4l9ajf/tinymce/5/tinymce.min.js"></script>

    <script>
	const swalWithBootstrapButtons = swal.mixin({
            confirmButtonClass: 'btn btn-primary mb-2',
            cancelButtonClass: 'btn btn-warning mr-2 mb-2',
            buttonsStyling: false,
        });
        function initEditor() {
            tinyMCE.init({
                autosave_ask_before_unload: false,
                invalid_elements: "script",
                selector: '.editor',
                plugins: 'autosave link image lists',
                relative_urls: false,
                height: 300,
                placeholder: 'Tuliskan Laporan...',
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
            initEditor();
			
			$('#btnSubmit').click(function(e) {
				$('#btnSubmit').attr('disabled',true)
				
				let $form = $(this).closest('#addForm');
				swalWithBootstrapButtons({
					title: `Simpan Data ?`,
					text: `Proses akan berjalan beberapa saat, mohon bersabar untuk menunggu`,
					type: 'info',
					showCancelButton: true,
					confirmButtonText: 'Simpan',
					cancelButtonText: 'Batal',
					reverseButtons: true
				}).then(async (result) => {
					if (result.value) {
						$form.submit();
					}
					else{
						$('#btnSubmit').attr('disabled',false);
					}
					
				});
				
				e.preventDefault();
			});
        });
    </script>
@endpush
