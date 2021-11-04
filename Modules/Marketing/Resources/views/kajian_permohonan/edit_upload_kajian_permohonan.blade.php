@extends('layouts.layout_app')

@section('title', 'Upload Kajian Permohonan PASKAL')

@section('content')
	<div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default" href="{{url("$url/detail/$dataPermohon->mohon_id?action=verifikasi")}}" style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali ke Detail Permohonan
                </a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Terima Permohonan Pengajuan Serifikasi "#{{$dataPermohon->mohon_id}}"</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="row">
                            <div class="col-lg-12">
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
								<form action="{{action("$module@update")}}" method="POST" enctype="multipart/form-data">
                                    <!-- Security CSRF TOKEN -->
                                    @csrf
									
									<input type="hidden" name="tipe" value="update-upload-kajian-permohonan">
									<input type="hidden" name="mohon_id" value="{{$dataPermohon->mohon_id}}">
									<input type="hidden" name="status_tipe" value="informasi">
									<div class="form-group row">
										<label class="col-form-label col-sm-3" for="mohon_kajian_permohonan_file">Kajian Permohonan *</label>
										<div class="col-sm-8">
											<input accept="application/pdf" class="form-control" type="file" name="mohon_kajian_permohonan_file">
											<small id="" class="form-text">Note: Upload file Kajian Permohonan yang sudah ditanda tangani ; file format berupa *.pdf</small>
											@if($dataPermohon->mohon_kajian_permohonan_paskal_file != '')
												<hr/>
												<a target="_blank" href="{{url($dataPermohon->mohon_kajian_permohonan_paskal_file)}}"><span class="fad fa-download"></span> File Kajian Permohonan PASKAL lama</a>
											@endif
										</div>
									</div>
									
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="mohon_harga_permohonan">Harga Akhir (Rp.) *</label>
                                        <div class="col-sm-8">
											<input type="hidden" id="mohon_kajian_permohonan_file_lama" name="mohon_kajian_permohonan_file_lama" class="form-control" value="{{$dataPermohon->mohon_kajian_permohonan_paskal_file}}"/>
                                            <input type="number" id="mohon_harga_permohonan" name="mohon_harga_permohonan" class="form-control" value="{{old('mohon_harga_permohonan') ?? $dataPermohon->mohon_harga_permohonan}}"/>
                                        </div>
                                    </div>
									
                                    <div class="form-buttons-w">
                                        <button class="btn btn-success" type="submit">
                                            <i class="fas fa-save"></i> Simpan
                                        </button>
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
