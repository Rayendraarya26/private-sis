@extends("layouts.layout_app")

@section('title', 'Detail Penugasan')

@section('content')
    <div class="dt-content">
		<div class="col-xl-12">
			<a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"><i class="fad fa-arrow-left"></i> Kembali</a>
			<div class="row">
				<div class="col-xl-12">
					<div class="card">
					  <div class="card-body p-0">									
						<table class="table">
							<tbody>
								<tr><td>Jenis Jadwal</td><td>: {{$dataJadwal->jadw_jenis}}</td></tr>
								<tr><td>Tanggal Jadwal</td><td>: {{$dataJadwal->jadw_tanggal_mulai?->format("d M Y")}} s/d {{$dataJadwal->jadw_tanggal_selesai?->format("d M Y")}}</td></tr>
								<tr><td>Nama Perusahaan</td><td>: {{$dataJadwal->cust_nama}}</td></tr>
								<tr><td>Alamat Perusahaan</td><td>: {{$dataJadwal->cust_alamat}}</td></tr>
								<tr><td>No. Referensi</td><td>: {{$dataJadwal->jadw_audit_nomor_referensi}}</td></tr>
								<tr><td>Kode NACE</td><td>: {{$dataJadwal->jadw_audit_kode_nace}}</td></tr>
								<tr><td>EA Code</td><td>: {{$dataJadwal->jadw_audit_kode_ea}}</td></tr>
								<tr><td>Komoditas</td><td>: {{$dataJadwal->komodt_nama}}</td></tr><tr><td>Ruang Lingkup</td><td>: {{$dataJadwal->jadw_audit_ruang_lingkup}}</td></tr>
								<tr><td>Standar Acuan</td><td>: {{$dataJadwal->jadw_audit_standart_acuan}}</td></tr>
								<tr><td>Kegiatan</td><td>: {{$dataJadwal->jadw_audit_kegiatan}}</td></tr>
								<tr><td>Tujuan Audit</td><td>: {{$dataJadwal->jadw_audit_tujuan_audit}}</td></tr>
								<tr><td>Posisi</td><td>: {{$dataJadwal->jadw_tim_posisi}}</td></tr>
								<tr><td></td><td><a href="{{ url("$dataJadwal->jadw_file_surat_tugas") }}" target="_blank"><i class="fad fa-download"></i> Download Surat Tugas</a></td></tr>
							</tbody>
						</table>
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
			
        });
    </script>
@endpush
