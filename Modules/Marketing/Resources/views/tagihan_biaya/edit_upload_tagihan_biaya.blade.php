@extends('layouts.layout_app')

@section('title', 'Upload Surat Tagihan Biaya PASKAL')

@section('content')
	<div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default" href="{{url("$url/detail/$dataPermohon->mohon_id?action=detail-permohonan")}}" style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali ke Detail Permohonan
                </a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Upload Surat Tagihan Biaya Pengajuan "#{{$dataPermohon->mohon_id}}"</h3>
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
									
									<input type="hidden" name="tipe" value="update-upload-tagihan-biaya">
									<input type="hidden" name="mohon_id" value="{{$dataPermohon->mohon_id}}">
									<input type="hidden" name="cust_id" value="{{$dataPermohon->cust_id}}">
									<input type="hidden" name="user_id" value="{{$dataPermohon->user_id}}">
									<input type="hidden" name="mohon_cust_nama" value="{{$dataPermohon->user_id}}">
									<input type="hidden" name="mohon_cust_email" value="{{$dataPermohon->mohon_cust_email}}">
									<div class="form-group row">
										<label class="col-form-label col-sm-3" for="mohon_tagihan_biaya_file">Surat Tagihan Biaya *</label>
										<div class="col-sm-8">
											<input accept="application/pdf" class="form-control" type="file" name="mohon_tagihan_biaya_file">
											<small id="" class="form-text">Note: file format berupa *.pdf</small>
											@if($dataPermohon->mohon_tagihan_biaya_file != '')
												<hr/>
												<input type="hidden" name="mohon_tagihan_biaya_file_lama" value="{{$dataPermohon->mohon_tagihan_biaya_file_lama}}">
												<a target="_blank" href="{{url($dataPermohon->mohon_tagihan_biaya_file)}}"><span class="fad fa-download"></span> File Surat Tagihan Biaya lama</a>
											@endif
										</div>
									</div>
									<div class="form-group row">
										<label class="col-form-label col-sm-3" for="mohon_det_harga_permohonan">Total Biaya(Rp.) *</label>
										<div class="col-sm-8">
											<input class="form-control" type="number" name="mohon_det_harga_permohonan" value="{{$dataPermohon->mohon_harga_permohonan}}">
										</div>
									</div>
									<!--
									<div class="form-group row">
										<div class="col-sm-12">
											<table class="table table-bordered  mb-0">
												<thead>
													<tr>
													  <th class="text-uppercase" scope="col">Sertifikasi</th>
													  <th class="text-uppercase" scope="col">Komoditi</th>
													  <th class="text-uppercase" scope="col">Biaya(Rp.)</th>
													</tr>
												</thead>
												<tbody>
													@foreach($dataPermohonKomoditi as $dpk)
													<tr>
													  <td>{{$dpk->sert_nama}}</td>
													  <td>{!! nl2br($dpk->komodt_nama) !!}</td>
													  <td>
														<input class="form-control" id="mohon_det_harga_permohonan{{$dpk->mohon_det_id}}" name="mohon_det_harga_permohonan[{{$dpk->mohon_det_id}}]" value="{{$dpk->mohon_det_harga_permohonan}}" autocomplete="off">
													  </td>
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
									-->									
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
