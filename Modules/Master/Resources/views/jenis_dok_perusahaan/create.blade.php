@extends('layouts.layout_app')

@section('title', 'Tambah Jenis Dokumen Perusahaan')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default"
                   href="{{url("$url")}}"
                   style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali
                </a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Tambah Jenis Dokumen Perusahaan</h3>
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

								<form action="{{action("$module@store")}}" method="POST" enctype="multipart/form-data">
                                    <!-- Security CSRF TOKEN -->
                                    @csrf
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="jenis_dok_perusahaan_text">Nama Jenis Dokumen Perusahaan*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan nama jenis dokumen perusahaan ..." type="text" name="jenis_dok_perusahaan_text" id="jenis_dok_perusahaan_text" value="{{old('jenis_dok_perusahaan_text')}}">
                                        </div>
                                    </div>
									
									<div class="form-group row">
										<label class="col-form-label col-sm-3" for="jenis_dok_perusahaan_sample_file">Sample File</label>
										<div class="col-sm-8">
											<input class="form-control" type="file" name="jenis_dok_perusahaan_sample_file">
											<small id="" class="form-text">Note: Upload file sample jika diperlukan; file boleh berupa *.zip atau *.pdf, *.docx, *.xlsx</small>
										</div>
									</div>
									
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="jenis_dok_perusahaan_deskripsi">Keterangan</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" name="jenis_dok_perusahaan_deskripsi" id="jenis_dok_perusahaan_deskripsi">{{old('jenis_dok_perusahaan_deskripsi')}}</textarea>
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
