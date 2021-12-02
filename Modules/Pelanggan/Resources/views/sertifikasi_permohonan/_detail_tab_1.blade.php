<div class="row">
    <div class="col-md-12" style="padding-top: 20px">
        @foreach($dataPemohon->sis_permohonan_details as $pengajuan)
            <h4>Pengajuan {{$loop->iteration}}</h4>

            <div class="col-md-12">
                <h4>Jenis Pengajuan</h4>
                <ul>
                    @if($pengajuan->mohon_det_jenis_status == "baru")
                        <li>Pengajuan Baru: {{$pengajuan->master_sertifikasi->sert_nama}}</li>
                    @else
                        <li>Re-Sertifikasi: {{$pengajuan->master_sertifikasi->sert_nama}} | {{$pengajuan->sis_pelanggan_sertifikasi?->cust_sert_nomor_referensi}}</li>
                    @endif
                </ul>
            </div>

        <div style="padding-top: 20px"></div>

            @if(count($pengajuan->sis_permohonan_komoditis) > 0)
                <div class="col-md-12">
                    <h4>Data Komoditas</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Komoditi</th>
                                <th>No SNI</th>
                                <th>Merk</th>
                                <th>Tipe</th>
                                <th>Ukuran</th>
                                <th>Jumlah Produksi Tahunan</th>
                                <th>Satuan Produksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pengajuan->sis_permohonan_komoditis as $kom)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$kom->master_komoditi->komodt_nama}}</td>
                                    <td>{{$kom->mohon_kmditi_sni}}</td>
                                    <td>{{$kom->mohon_kmditi_merk}}</td>
                                    <td>{{$kom->mohon_kmditi_tipe}}</td>
                                    <td>{{$kom->mohon_kmditi_ukuran}}</td>
                                    <td>{{$kom->mohon_kmditi_kapasitas_produksi_tahunan}}</td>
                                    <td>{{$kom->mohon_kmditi_kapasitas_produksi_tahunan_satuan}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <hr style="border: 1px dashed grey;border-radius: 5px; width: 100%">
        @endforeach
    </div>
</div>
