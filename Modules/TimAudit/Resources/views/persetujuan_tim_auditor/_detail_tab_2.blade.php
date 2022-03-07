<div class="row">
    <div class="col-md-12" style="padding-top: 20px">
        <h4>Data Perusahaan</h4>

        <div class="table-responsive">
            <table class="table table-striped data-perusahaan">
                <tbody>
                <tr>
                    <td>Nama Perusahaan</td>
                    <td>: {{$dataPemohon[0]->mohon_cust_nama}}</td>
                </tr>
                <tr>
                    <td>Nomor Akta Pendirian</td>
                    <td>: {{$dataPemohon[0]->mohon_cust_nomor_akta_pendirian}}</td>
                </tr>
                <tr>
                    <td>Nama Pemilik</td>
                    <td>: {{$dataPemohon[0]->mohon_cust_nama_pemilik}}</td>
                </tr>
                <tr>
                    <td>Nama Pimpinan</td>
                    <td>: {{$dataPemohon[0]->mohon_cust_nama_pimpinan}}</td>
                </tr>
                <tr>
                    <td>Nama Wakil Manajemen</td>
                    <td>: {{$dataPemohon[0]->mohon_cust_nama_wakil_manajemen}}</td>
                </tr>
                <tr>
                    <td>Telp (Perusahaan)</td>
                    <td>: {{$dataPemohon[0]->mohon_cust_nomor_telp}}</td>
                </tr>
                <tr>
                    <td>Fax</td>
                    <td>: {{$dataPemohon[0]->mohon_cust_nomor_fax}}</td>
                </tr>
                <tr>
                    <td>Nomer HP (CP)</td>
                    <td>: {{$dataPemohon[0]->mohon_cust_nomor_hp}}</td>
                </tr>
                <tr>
                    <td>Badan Hukum</td>
                    <td>: {{$dataPemohon[0]->master_badan_hukum?->badan_hukum_nama}}</td>
                </tr>
                <tr>
                    <td>Jenis Perusahaan</td>
                    <td>: {{$dataPemohon[0]->master_jenis_perusahaan?->jenis_perusahaan_nama}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>


    <div class="col-md-12">
        <h4>Data Lokasi</h4>

        <div class="table-responsive">
            <table class="table table-striped data-perusahaan">
                <tbody>
                <tr>
                    <td>Negara</td>
                    <td>: {{$dataPemohon[0]->master_negara?->negara_nama}}</td>
                </tr>
                @if(strtolower($dataPemohon[0]->master_negara?->negara_nama) == "indonesia")
                    <tr>
                        <td></td>
                        <td>Provinsi</td>
                        <td>: {{$dataPemohon[0]->master_provinsi?->prov_nama}}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Kabupaten</td>
                        <td>: {{$dataPemohon[0]->master_kabupaten?->kab_nama}}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Kecamatan</td>
                        <td>: {{$dataPemohon[0]->master_kecamatan?->kec_nama}}</td>
                    </tr>
                @endif
                <tr>
                    <td>Alamat</td>
                    <td>: {{$dataPemohon[0]->mohon_cust_alamat}}</td>
                </tr>
                <tr>
                    <td>Luas Tanah</td>
                    <td>: {{$dataPemohon[0]->mohon_cust_luas_tanah}}</td>
                </tr>
                <tr>
                    <td>Luas Bangunan</td>
                    <td>: {{$dataPemohon[0]->mohon_cust_luas_bangunan}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-12">
        <h4>Data Operasional</h4>

        <div class="table-responsive">
            <table class="table table-striped data-perusahaan">
                <tbody>
                <tr>
                    <td>Jumlah Shift (dalam sehari)</td>
                    <td>: {{$dataPemohon[0]->mohon_cust_shif_kerja}}</td>
                </tr>
                <tr>
                    <td>Jumlah Bagian</td>
                    <td>: {{$dataPemohon[0]->mohon_cust_jumlah_bagian}}</td>
                </tr>
                <tr>
                    <td>
                        Jumlah Karyawan
                        <ol id="jumlah_karyawan">
                            <li>Manajemen: {{$dataPemohon[0]->mohon_cust_jumlah_manajemen}} orang</li>
                            <li>Administrasi: {{$dataPemohon[0]->mohon_cust_jumlah_administrasi}} orang</li>
                            <li>Part Time: {{$dataPemohon[0]->mohon_cust_jumlah_part_time}} orang</li>
                            <li>
                                Operasional:
                                <ul>
                                    <li>Shift 1: {{$dataPemohon[0]->mohon_cust_jumlah_shift_1}} orang</li>
                                    <li>Shift 2: {{$dataPemohon[0]->mohon_cust_jumlah_shift_2}} orang</li>
                                    <li>Shift 3: {{$dataPemohon[0]->mohon_cust_jumlah_shift_3}} orang</li>
                                </ul>
                            </li>
                            <li>Non Permanen: {{$dataPemohon[0]->mohon_cust_jumlah_non_permanen}} orang</li>
                        </ol>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>


    @if(!empty($dataPemohon[0]->sis_permohonan_pabriks))
        <div class="col-md-12">
            <h4>Data Pabrik</h4>


            <div class="table-responsive">
                <table class="table table-striped data-perusahaan">
                    <tbody>
                    <tr>
                        <td style="padding: 10px 0 0 0"></td>
                        <td>
                            Detail Lokasi Pabrik :
                            <div class="table-responsive" style="padding-top: 20px">
                                @foreach($dataPemohon[0]->sis_permohonan_pabriks as $pabrik)
                                    <h4 style="text-align: center">Pabrik Ke
                                        - {{$loop->iteration}}</h4>
                                    <table class="table" style="padding-bottom: 20px">
                                        <tbody>
                                        <tr>
                                            <td>Nama Pabrik</td>
                                            <td>: {{$pabrik->mohon_pabrik_nama}}</td>
                                        </tr>
                                        <tr>
                                            <td>Np Telp</td>
                                            <td>: {{$pabrik->mohon_pabrik_nomor_telp}}</td>
                                        </tr>
                                        <tr>
                                            <td>No HP</td>
                                            <td>: {{$pabrik->mohon_pabrik_nomor_hp}}</td>
                                        </tr>
                                        <tr>
                                            <td>Fax</td>
                                            <td>: {{$pabrik->mohon_pabrik_nomor_fax}}</td>
                                        </tr>
                                        <tr>
                                            <td>Provinsi</td>
                                            <td>: {{$pabrik->master_provinsi?->prov_nama}}</td>
                                        </tr>
                                        <tr>
                                            <td>Kabupaten</td>
                                            <td>: {{$pabrik->master_kabupaten?->kab_nama}}</td>
                                        </tr>
                                        <tr>
                                            <td>Kecamatan</td>
                                            <td>: {{$pabrik->master_kecamatan?->kec_nama}}</td>
                                        </tr>
                                        <tr>
                                            <td>KodePos</td>
                                            <td>: {{$pabrik->mohon_pabrik_kode_pos}}</td>
                                        </tr>
                                        <tr>
                                            <td>Alamat Pabrik</td>
                                            <td>: {{$pabrik->mohon_pabrik_alamat}}</td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah Karyawan</td>
                                            <td>: {{$pabrik->mohon_pabrik_jumlah_karyawan}}</td>
                                        </tr>
                                        <tr>
                                            <td>Kegiatan Utama</td>
                                            <td>: {{$pabrik->mohon_pabrik_kegiatan_utama}}</td>
                                        </tr>
                                        <tr>
                                            <td>Luas Tanah</td>
                                            <td>: {{$pabrik->mohon_pabrik_luas_tanah}}</td>
                                        </tr>
                                        <tr>
                                            <td>Luas Bangunan</td>
                                            <td>: {{$pabrik->mohon_pabrik_luas_bangunan}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <br>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif


    <div class="col-md-12">
        <h4>Data Tambahan</h4>

        <div class="table-responsive">
            <table class="table table-striped data-perusahaan">
                <tbody>
                <tr>
                    <td>Pertanyaan Tambahan</td>
                    <td>: <a target="_blank"
                             href="{{url(asset($dataPemohon[0]->mohon_pertanyaan_filepath))}}">
                            <i class="fad fa-download"></i> Unduh
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
