@extends('layouts.layout_app')

@section('title', 'Upload Pernyataan Persetujuan')

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
                            <h3 class="dt-card__title">Upload Pernyataan Persetujuan Pengajuan Serifikasi "#{{$dataPermohon->mohon_id}}"</h3>
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
									
									<input type="hidden" name="tipe" value="update-persetujuan">
									<input type="hidden" name="mohon_id" value="{{$dataPermohon->mohon_id}}">
									<input type="hidden" name="status_tipe" value="informasi">
									<div class="form-group row">
										<label class="col-form-label col-sm-3" for="mohon_pernyataan_persetujuan_file">Pernyataan Persetujuan *</label>
										<div class="col-sm-8">
											<input class="form-control" type="file" name="mohon_pernyataan_persetujuan_file">
											<small id="" class="form-text">Note: Upload file upload pernyataan persetujuan ; file format berupa *.pdf</small>
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
