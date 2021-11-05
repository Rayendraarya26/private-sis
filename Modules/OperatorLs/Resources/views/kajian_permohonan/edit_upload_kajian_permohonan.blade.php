@extends('layouts.layout_app')

@section('title', 'Upload Kajian Permohonan PJT')

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
											<input class="form-control" type="file" name="mohon_kajian_permohonan_file">
											<small id="" class="form-text">Note: Upload file Kajian Permohonan yang sudah ditanda tangani;</small>
											@if($dataPermohon->mohon_kajian_permohonan_pjt_file != '')
												<hr/>
												<a target="_blank" href="{{url($dataPermohon->mohon_kajian_permohonan_pjt_file)}}"><span class="fad fa-download"></span> File Kajian Permohonan PJT lama</a>
											@endif
										</div>
									</div>
									
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="mohon_perlu_tahap1">Perlu Proses Tahap 1?*</label>
                                        <div class="col-sm-8">
											<input type="hidden" id="mohon_kajian_permohonan_file_lama" name="mohon_kajian_permohonan_file_lama" class="form-control" value="{{$dataPermohon->mohon_kajian_permohonan_paskal_file}}"/>
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="mohon_perlu_tahap1" id="mohon_perlu_tahap1" value="ya" {{old('mohon_perlu_tahap1') == "ya" || $dataPermohon->mohon_perlu_tahap1 == "ya" ? "checked" :""}} >
												<label class="form-check-label" for="mohon_perlu_tahap1">Ya</label>
											</div>
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="mohon_perlu_tahap1" id="mohon_perlu_tahap1" value="tidak" {{old('mohon_perlu_tahap1') == "tidak"  || $dataPermohon->mohon_perlu_tahap1 == "tidak" ? "checked" :""}}>
												<label class="form-check-label" for="mohon_perlu_tahap1">Tidak</label>
											</div>
                                        </div>
                                    </div>
									<div class="form-group row">
										<div class="table-responsive col-xl-12 col-md-12 col-12">
											<table class="table table-bordered  mb-0">
												<thead>
													<tr>
													  <th class="text-uppercase" scope="col">Komoditi(Merk, Type, Ukuran, Kapasitas Produksi/tahun)</th>
													  <th class="text-uppercase" scope="col">SNI</th>
													  <th class="text-uppercase" scope="col">Ruang Lingkup</th>
													  <th class="text-uppercase" scope="col">NACE</th>
													  <th class="text-uppercase" scope="col">EA</th>
													</tr>
												</thead>
												<tbody>
													@foreach($dataPermohonKomoditi as $dpk)
													<tr>
													  <td>
														Komoditi : {{$dpk->komodt_nama}}<hr/>
														Merk : {{$dpk->mohon_kmditi_merk}}<hr/>
														Type : {{$dpk->mohon_kmditi_tipe}}<hr/>
														Ukuran : {{$dpk->mohon_kmditi_ukuran}}<hr/>
														Kapasitas Produksi/tahun : {{$dpk->mohon_kmditi_kapasitas_produksi_tahunan}} {{$dpk->mohon_kmditi_kapasitas_produksi_tahunan_satuan}}<hr/>
													  </td>
													  <td>{{$dpk->mohon_kmditi_sni}}</td>
													  <td><input id="mohon_kmditi_ruang_lingkup_{{$dpk->mohon_kmditi_id}}" name="mohon_kmditi_ruang_lingkup[{{$dpk->mohon_kmditi_id}}]"></td>
													  <td><input id="mohon_kmditi_nace_{{$dpk->mohon_kmditi_id}}" name="mohon_kmditi_nace[{{$dpk->mohon_kmditi_id}}][]"></td>
													  <td><input id="mohon_kmditi_ea_{{$dpk->mohon_kmditi_id}}" name="mohon_kmditi_ea[{{$dpk->mohon_kmditi_id}}]"></td>
													</tr>
													@endforeach
												</tbody>
											</table>
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
@push("javascript")
	<script>
		$(document).ready(function () {
			@foreach($dataPermohonKomoditi as $dpk)
			$('#mohon_kmditi_nace_{{$dpk->mohon_kmditi_id}}').combobox({
				editable: false,
				url: `{{ url("$url/ajax?action=combobox-kode-nace") }}`,
				width: 200,
				multiple: true,
				method: 'get',
				valueField:'nama',
				textField:'nama',
			});
			
			$('#mohon_kmditi_ruang_lingkup_{{$dpk->mohon_kmditi_id}}').combobox({
				editable: false,
				url: `{{ url("$url/ajax?action=combobox-kode-ruang-lingkup") }}`,
				width: 200,
				method: 'get',
				valueField:'nama',
				textField:'nama',
			});
			
			$('#mohon_kmditi_ea_{{$dpk->mohon_kmditi_id}}').combobox({
				editable: false,
				url: `{{ url("$url/ajax?action=combobox-kode-ea") }}`,
				width: 200,
				method: 'get',
				valueField:'nama',
				textField:'nama',
			});
			
			$('#mohon_kmditi_nace_{{$dpk->mohon_kmditi_id}}').combobox('setValue', [
			@if ($dpk->mohon_kmditi_nace != "")
				@foreach(explode(';', $dpk->mohon_kmditi_nace) as $val)
					`{{$val}}`,
				@endforeach
			@endif]);
			
			$('#mohon_kmditi_ea_{{$dpk->mohon_kmditi_id}}').combobox('setValue', '{{$dpk->mohon_kmditi_ea}}');
			
			$('#mohon_kmditi_ruang_lingkup_{{$dpk->mohon_kmditi_id}}').combobox('setValue', '{{$dpk->mohon_kmditi_ruang_lingkup}}');
			@endforeach
        });
    </script>
@endpush
