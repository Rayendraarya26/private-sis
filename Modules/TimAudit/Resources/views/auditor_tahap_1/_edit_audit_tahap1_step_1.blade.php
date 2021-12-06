<div class="row" id="vueStepOne">
	<div class="col-xl-12">
		<div class="table-responsive">
			<table class="table table-bordered mb-0">
				<thead class="thead-light">
					<tr>
					  <th rowspan="2" scope="col">Klausul</th>
					  <th rowspan="2" scope="col">Persyaratan</th>
					  <th colspan="2" scope="col">Dokumen {{$dataJadwal->cust_nama}}</th>
					  <th rowspan="2" scope="col">Hasil Tinjauan(OK / NO)</th>
					  <th rowspan="2" scope="col">Keterangan</th>
					</tr>
					<tr>
					  @if($dataJadwal->sert_tahap1_jenis == 'sni')
						<th scope="col">Kode Dokumen </th>
					    <th scope="col">Judul Dokumen</th>
					  @elseif($dataJadwal->sert_tahap1_jenis == 'pusat')
						<th scope="col">Nilai </th>
					    <th scope="col">Satuan</th>
					  @endif 
					</tr>
				</thead>
				<tbody>
					@foreach($dataAuditKlausul as $dpk)
					<tr>
					  <th scope="row">{{$dpk->aud_thp1_det_thp1_nomor}}</th>
					  @if($dataJadwal->sert_tahap1_jenis == 'sni')
					  <td>{{$dpk->aud_thp1_det_peryataan}}</td>
					  @elseif($dataJadwal->sert_tahap1_jenis == 'pusat')
					  <td>{{$dpk->aud_thp1_det_persyaratan}}</td>
					  @endif 
					  
					  @if($dataJadwal->sert_tahap1_jenis == 'sni')
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							<input type="text" class="form-control" name="kode_dok[{{$dpk->aud_thp1_det_id}}]" id="kode_dok" placeholder="Kode Dokumen" value="{{$dpk->aud_thp1_det_kode_dok}}">
						@endif 
					  </td>
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							<input type="text" class="form-control" name="judul_dok[{{$dpk->aud_thp1_det_id}}]" id="judul_dok" placeholder="Judul Dokumen" value="{{$dpk->aud_thp1_det_judul_dok}}">
						@endif 
					  </td>
					  @elseif($dataJadwal->sert_tahap1_jenis == 'pusat')
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							<input type="text" class="form-control" name="nilai[{{$dpk->aud_thp1_det_id}}]" id="nilai" placeholder="" value="{{$dpk->aud_thp1_det_nilai}}">
						@endif 
					  </td>
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							<input type="text" class="form-control" name="satuan[{{$dpk->aud_thp1_det_id}}]" id="satuan" placeholder="Satuan" value="{{$dpk->aud_thp1_det_satuan}}">
						@endif 
					  </td>
					  @endif 
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							@if($dpk->aud_thp1_det_hasil_tinjauan == 'ok')
								<div class="col-md-12 col-sm-12">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="hasil_tinjauan_{{$dpk->aud_thp1_det_id}}" id="hasil_tinjauan_ok{{$dpk->aud_thp1_det_id}}" value="ok" checked>
									<label class="form-check-label" for="hasil_tinjauan_ok{{$dpk->aud_thp1_det_id}}">OK</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="hasil_tinjauan_{{$dpk->aud_thp1_det_id}}" id="hasil_tinjauan_no{{$dpk->aud_thp1_det_id}}" value="no">
									<label class="form-check-label" for="hasil_tinjauan_no{{$dpk->aud_thp1_det_id}}">NO</label>
								</div>
								</div>
							@elseif($dpk->aud_thp1_det_hasil_tinjauan == 'no')
							<div class="col-md-12 col-sm-12">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="hasil_tinjauan_{{$dpk->aud_thp1_det_id}}" id="hasil_tinjauan_ok{{$dpk->aud_thp1_det_id}}" value="ok">
									<label class="form-check-label" for="hasil_tinjauan_ok{{$dpk->aud_thp1_det_id}}">OK</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="hasil_tinjauan_{{$dpk->aud_thp1_det_id}}" id="hasil_tinjauan_no{{$dpk->aud_thp1_det_id}}" value="no" checked>
									<label class="form-check-label" for="hasil_tinjauan_no{{$dpk->aud_thp1_det_id}}">NO</label>
								</div>
								</div>
							@else
								<div class="col-md-12 col-sm-12">
									<div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="hasil_tinjauan_{{$dpk->aud_thp1_det_id}}" id="hasil_tinjauan_ok{{$dpk->aud_thp1_det_id}}" value="ok" checked>
										<label class="form-check-label" for="hasil_tinjauan_ok{{$dpk->aud_thp1_det_id}}">OK</label>
									</div>
									<div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="hasil_tinjauan_{{$dpk->aud_thp1_det_id}}" id="hasil_tinjauan_no{{$dpk->aud_thp1_det_id}}" value="no">
										<label class="form-check-label" for="hasil_tinjauan_no{{$dpk->aud_thp1_det_id}}">NO</label>
									</div>
								</div>
							@endif 
						@endif 
					  </td>
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							<textarea type="text" class="form-control" name="keterangan_{{$dpk->aud_thp1_det_id}}" id="keterangan" placeholder="Keterangan">{{$dpk->aud_thp1_det_keterangan}}</textarea>
						@endif 
					  </td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>

@push('javascript')
    <script>
        $(document).ready(function () {
            window.vueStepOne = new Vue({
                el: "#vueStepOne",
                data: {
                    aud_thp1_id: `{{$dataJadwal->aud_thp1_id}}`,
                    aud_thp1_id: `{{$dataJadwal->aud_thp1_id}}`,
                },
                mounted() {
                    this.setForm();
                },
                methods: {
                    validate() {
						@foreach($dataAuditKlausul as $dpk)
							@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
								if($('input[name="hasil_tinjauan_{{$dpk->aud_thp1_det_id}}').is(':checked')) { 
					
								}
								else{
									throw "Pilih Hasil Tinjauan untuk Klausul {{$dpk->aud_thp1_det_thp1_nomor}}"
								}
								@if($dataJadwal->sert_tahap1_jenis == 'sni')
									if ($('input[name="kode_dok[{{$dpk->aud_thp1_det_id}}]').val() == '') throw "Kode Dokumen untuk Klausul {{$dpk->aud_thp1_det_thp1_nomor}} masih kosong" 
									if ($('input[name="judul_dok[{{$dpk->aud_thp1_det_id}}]').val() == '') throw "Judul Dokumen untuk Klausul {{$dpk->aud_thp1_det_thp1_nomor}} masih kosong" 
								@elseif($dataJadwal->sert_tahap1_jenis == 'pusat')
									if ($('input[name="nilai[{{$dpk->aud_thp1_det_id}}]').val() == '') throw "Nilai untuk Klausul {{$dpk->aud_thp1_det_thp1_nomor}} masih kosong" 
									if ($('input[name="satuan[{{$dpk->aud_thp1_det_id}}]').val() == '') throw "Satuan untuk Klausul {{$dpk->aud_thp1_det_thp1_nomor}} masih kosong" 
								@endif
							@endif
						@endforeach
						
                    },
                    async setForm() {
                        
                    },
                }
            })
        })
    </script>
@endpush
