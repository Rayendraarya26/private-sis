@extends('layouts.layout_app')

@section('title', 'Tambah / Perbarui Laporan Ringkas')

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
                                LAPORAN RINGKAS
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
                                        : {{ $data->jadw_tanggal_mulai }}
                                        s/d {{ $data->jadw_tanggal_selesai }}</td>
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
                                    <td>Observasi</td>
                                    <td>{{$dataLKS['jumlah']['observasi']}}</td>
                                    <td>{{$dataLKS['no_lks']['observasi'] ?: '-'}}</td>
                                    <td>{{$dataLKS['klausul']['observasi'] ?: '-'}}</td>
                                    <td>{{$dataLKS['tgl_pelyelesaian']['observasi'] ?: '-'}}</td>
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
                                    TULIS LAPORAN RINGKAS
                                </h3>
                            </div>
                        </div>
                        <div class="dt-card__body">
                            <div class="col-md-12">
                                <form id="addForm" action="{{ action("$module@processLaporan", $data->jadw_id) }}" method="post" enctype="multipart/form-data">
                                    @csrf

								<div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="jadw_setujui_nama">
                                        Tanda Tangan Nama*
                                        @error('jadw_setujui_nama')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="jadw_setujui_nama" id="jadw_setujui_nama" value="{{old('jadw_setujui_nama') ?? $data->jadw_setujui_nama}}"/>
										<span><small>Pengesahan untuk client/pelanggan</small></span>
                                    </div>
                                </div>

								<div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="jadw_setujui_jabatan">
                                        Tanda Tangan Jabatan*
                                        @error('jadw_setujui_jabatan')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="jadw_setujui_jabatan" id="jadw_setujui_jabatan" value="{{old('jadw_setujui_jabatan') ?? $data->jadw_setujui_jabatan}}"/>
										<span><small>Pengesahan untuk client/pelanggan</small></span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_ringkas_kesimpulan">
                                        Ringkasan Hasil / Kesimpulan*
                                        @error('lap_ringkas_kesimpulan')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" placeholder="Masukkaan uraian..."
                                                  name="lap_ringkas_kesimpulan"
                                                  aria-label="Uraian ketidaksesuaian"
                                                  id="lap_ringkas_kesimpulan">{{old('lap_ringkas_kesimpulan') ?? $data->sis_audit_lap_ringkas?->lap_ringkas_kesimpulan}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="lap_ringkas_rekomendasi">
                                        Rekomendasi*
                                        @error('lap_ringkas_rekomendasi')
                                        <br><span style="color: red">{{$message}}</span>
                                        @enderror
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" placeholder="Masukkaan uraian..."
                                                  name="lap_ringkas_rekomendasi"
                                                  aria-label="Uraian ketidaksesuaian"
                                                  id="lap_ringkas_rekomendasi">{{old('lap_ringkas_rekomendasi') ?? $data->sis_audit_lap_ringkas?->lap_ringkas_rekomendasi}}</textarea>
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
    <script src="https://cdn.tiny.cloud/1/hb65btdze8ubxfoabqu7fqjpuzpmx0c4k0je5f883m4l9ajf/tinymce/5/tinymce.min.js">
    </script>

    <script>
		const swalWithBootstrapButtons = swal.mixin({
            confirmButtonClass: 'btn btn-primary mb-2',
            cancelButtonClass: 'btn btn-warning mr-2 mb-2',
            buttonsStyling: false,
        });
		
		function initRingkasanEditor() {
            tinyMCE.init({
                autosave_ask_before_unload: false,
                invalid_elements: "script",
                selector: '#lap_ringkas_kesimpulan',
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
		
        function initRekomendasiEditor() {
            tinyMCE.init({
                autosave_ask_before_unload: false,
                invalid_elements: "script",
                selector: '#lap_ringkas_rekomendasi',
                plugins: 'autosave link image lists',
                relative_urls: false,
                height: 500,
                placeholder: 'Tuliskan Rekomendasi...',
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
            initRingkasanEditor();
            initRekomendasiEditor();
			
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
