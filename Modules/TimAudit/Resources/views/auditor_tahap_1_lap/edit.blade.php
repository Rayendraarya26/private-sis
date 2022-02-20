@extends("layouts.layout_app")

@section('title', 'Laporan Audit Tahap 1')
@push('css')
    <style>
        .komoditi-button {
            padding-top: 15px;
        }
		
		.label-form {
            font-weight:normal;
        }
		
        @media screen and (max-width: 450px) {
            .komoditi-button {
                padding-top: 0;
            }
        }
    </style>
@endpush
@section('content')
    <div class="dt-content">
        <div class="row">
			<a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"><i class="fad fa-arrow-left"></i> Kembali</a>
            <div class="col-md-12">
                <div class="dt-card">
                    <div class="dt-card__body">
                        <div class="row" id="vueStepTwo">
							
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
							
							<div class="col-md-12" style="padding-bottom: 20px">
							<form id="addForm" action="{{ action("$module@update") }}" method="post" enctype="multipart/form-data">
                                @csrf
								<div class="row">
									<div class="col-md-12">
										<input type="hidden" name="cust_id" value="{{$dataJadwal->cust_id}}">
										<input type="hidden" name="aud_thp1_id" value="{{$dataJadwal->aud_thp1_id}}">
										<input type="hidden" name="sert_id" value="{{$dataJadwal->sert_id}}">
										<input type="hidden" name="mohon_id" value="{{$dataJadwal->mohon_id}}">
										@if($dataJadwal->aud_thp1_lap_revisi_note != '')
										<div class="form-group row" style="color:red;">
											<label class="col-form-label col-sm-3">
												V. Revisi Verifikasi Laporan
											</label>
											<div class="col-sm-8">
												{!! $dataJadwal->aud_thp1_lap_revisi_note ?? '-' !!}
											</div>
										</div>
										@endif
										
										<div class="form-group">
											<label class="label-form">V. Audit kecukupan informasi terdokumentasi: *</label>
											<textarea class="editor form-control" name="kolom_v" id="kolom_v">@if(isset($dataJadwal->aud_thp1_kolom_v)) {{$dataJadwal->aud_thp1_kolom_v}} @endif</textarea>
										</div>
										<div class="form-group">
											<label class="label-form">VI. Kondisi Lapangan</label>
											<textarea class="editor form-control" name="kolom_vi" id="kolom_vi">@if(isset($dataJadwal->aud_thp1_kolom_vi)) {{$dataJadwal->aud_thp1_kolom_vi}} @endif</textarea>
										</div>
										<div class="form-group">
											<label class="label-form">VII. Status dan pemahaman persyaratan standar</label>
											<textarea class="editor form-control" name="kolom_vii" id="kolom_vii">@if(isset($dataJadwal->aud_thp1_kolom_vii)) {{$dataJadwal->aud_thp1_kolom_vii}} @endif</textarea>
										</div>
										<div class="form-group">
											<label class="label-form">VIII. Informasi yang diperlukan yang berkenaan dengan (lingkup sistem manajemen K3, proses dan lokasi perusahaan, identifikasi bahaya dan risiko dan perundang-undangan/peraturan K3, dari operasi perusahaan dan risiko) tersedia.</label>
											<textarea class="editor form-control" name="kolom_viii" id="kolom_viii">@if(isset($dataJadwal->aud_thp1_kolom_viii)) {{$dataJadwal->aud_thp1_kolom_viii}} @endif</textarea>
										</div>
										<div class="form-group">
											<label class="label-form">IX. Sumber daya yang tersedia</label>
											<textarea class="editor form-control" name="kolom_ix" id="kolom_ix">@if(isset($dataJadwal->aud_thp1_kolom_ix)) {{$dataJadwal->aud_thp1_kolom_ix}} @endif</textarea>
										</div>
										<div class="form-group">
											<label class="label-form">X. Konfirmasi program audit sertifikasi tahap 2</label>
											<textarea class="editor form-control" name="kolom_x" id="kolom_x">@if(isset($dataJadwal->aud_thp1_kolom_x)) {{$dataJadwal->aud_thp1_kolom_x}} @endif</textarea>
										</div>
										<div class="form-group">
											<label class="label-form">XI. Informasi pelaksanaan audit internal dan kaji ulang manajemen</label>
											<textarea class="editor form-control" name="kolom_xi" id="kolom_xi">@if(isset($dataJadwal->aud_thp1_kolom_xi)) {{$dataJadwal->aud_thp1_kolom_xi}} @endif</textarea>
										</div>
										<div class="form-group">
											<label class="label-form">XII. Kesimpulan</label>
											<textarea class="editor form-control" name="kolom_xii" id="kolom_xii">@if(isset($dataJadwal->aud_thp1_kolom_xii)) {{$dataJadwal->aud_thp1_kolom_xii}} @endif</textarea>
										</div>
										
										<div class="form-group row">
											<label class="col-form-label col-sm-3" for="aud_thp1_verifikasi_oleh">
												Nama Persetujuan
												@error('aud_thp1_verifikasi_oleh')
												<br><span style="color: red">{{$message}}</span>
												@enderror
											</label>
											<div class="col-sm-8">
												<input type="text" class="form-control" placeholder="..." name="aud_thp1_verifikasi_oleh" id="aud_thp1_verifikasi_oleh" value="{{old('aud_thp1_verifikasi_oleh') ?? $dataJadwal->aud_thp1_verifikasi_oleh}}">
											</div>
										</div>

										<div class="form-group row">
											<label class="col-form-label col-sm-3" for="aud_thp1_verifikasi_jabatan">
												Jabatan Persetujuan
												@error('aud_thp1_verifikasi_jabatan')
												<br><span style="color: red">{{$message}}</span>
												@enderror
											</label>
											<div class="col-sm-8">
												<input type="text" class="form-control" placeholder="..." name="aud_thp1_verifikasi_jabatan" id="aud_thp1_verifikasi_jabatan" value="{{old('aud_thp1_verifikasi_jabatan') ?? $dataJadwal->aud_thp1_verifikasi_jabatan}}">
											</div>
										</div>

										<div class="form-group row">
											<label class="col-form-label col-sm-3" for="aud_thp1_verifikasi_diajukan">
												Simpan sebagai Draft?
												@error('aud_thp1_verifikasi_diajukan')
												<br><span style="color: red">{{$message}}</span>
												@enderror
											</label>
											<div class="col-md-8">
											  <div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="aud_thp1_verifikasi_diajukan" value="ya" @if(isset($dataJadwal->aud_thp1_verifikasi_diajukan)) @if($dataJadwal->aud_thp1_verifikasi_diajukan == 'ya') checked @endif @endif id="draft1">
												<label class="form-check-label" for="draft1">Tidak</label>
											  </div>
											  <div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="aud_thp1_verifikasi_diajukan" value="tidak" @if(isset($dataJadwal->aud_thp1_verifikasi_diajukan)) @if($dataJadwal->aud_thp1_verifikasi_diajukan == 'tidak') checked @endif @endif id="draft2">
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
									</div>
								</div>
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
			
        $(document).ready(function () {
            initEditor();
			
			
        });
    </script>
@endpush