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
							{{$dpk->aud_thp1_det_kode_dok}}
						@endif 
					  </td>
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							{{$dpk->aud_thp1_det_judul_dok}}
						@endif 
					  </td>
					  @elseif($dataJadwal->sert_tahap1_jenis == 'pusat')
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							{{$dpk->aud_thp1_det_nilai}}
						@endif 
					  </td>
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							{{$dpk->aud_thp1_det_satuan}}
						@endif 
					  </td>
					  @endif 
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							{{$dpk->aud_thp1_det_hasil_tinjauan}}
						@endif 
					  </td>
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							{{$dpk->aud_thp1_det_keterangan}}
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
						
                    },
                    async setForm() {
                        
                    },
                }
            })
        })
    </script>
@endpush
