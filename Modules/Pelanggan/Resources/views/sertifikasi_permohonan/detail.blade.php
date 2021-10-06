@extends("layouts.layout_app")

@section('title', 'Detail Permohonan Sertifikasi')

@push('css')
    <style>
        body {
            counter-reset: section;
        }

        .data-perusahaan > tbody > tr > td:first-child::before {
            counter-increment: section;
            content: counter(section);
        }

        .data-perusahaan > tbody > tr > td:first-child::before {
            content: counter(section);
        }

        .data-perusahaan > tbody > tr > td:first-child {
            width: 10px;
        }

        .data-perusahaan > tbody > tr > td:nth-child(2) {
            width: 350px;
        }

        #jumlah_karyawan > li {
            padding: 5px
        }
    </style>
@endpush

@section('content')

    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <!-- Card -->
                <div class="dt-card dt-card__full-height">

                    <!-- Card Header -->
                    <div class="dt-card__header">

                        <!-- Card Heading -->
                        <div class="dt-card__heading" style="text-align: center">
                            <h2>
                                @if($dataPemohon->mohon_jenis_status == "baru")
                                    Data Sertifikasi (Pengajuan Baru)
                                @else
                                    Data Sertifikasi (Perpajangan
                                    Sertifikat {{$dataPemohon->sis_pelanggan_sertifikasi?->cust_sert_nomor_referensi}})
                                @endif

                                <br>
                                <small>{{$dataPemohon->master_sertifikasi?->sert_nama}}</small>
                            </h2>
                            <br>
                        </div>
                        <!-- /card heading -->

                    </div>
                    <!-- /card header -->

                    <!-- Card Body -->
                    <div class="dt-card__body">
                        <div class="row no-gutters">
                            <div class="col-md-12">
                                <h4>Kelengkapan Dokumen</h4>

                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Dokumen</th>
                                            <th>Dokumen Anda</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($dataPemohon->sis_permohonan_dokumens as $dok)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$dok->master_jenis_dok_perusahaan->jenis_dok_perusahaan_text}}</td>
                                                <td>
                                                    <a target="_blank" href="{{asset($dok->mohon_dok_filepath)}}">
                                                        <i class="fad fa-download"></i>
                                                        Download
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>

                            @if($dataPemohon->master_sertifikasi?->sert_is_product == "ya")
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
                                            @foreach($dataPemohon->sis_permohonan_komoditis as $kom)
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
                        </div>
                        <!-- /card body -->
                    </div>
                    <!-- /card -->
                </div>
            </div>


            <div class="col-xl-12">
                <!-- Card -->
                <div class="dt-card dt-card__full-height">

                    <!-- Card Header -->
                    <div class="dt-card__header">

                        <!-- Card Heading -->
                        <div class="dt-card__heading" style="text-align: center">
                            <h2>
                                Kondisi Perusahaan
                            </h2>
                            <br>
                        </div>
                        <!-- /card heading -->

                    </div>
                    <!-- /card header -->

                    <!-- Card Body -->
                    <div class="dt-card__body">
                        <div class="row no-gutters">
                            <div class="col-md-12">
                                <h4>Data Perusahaan</h4>

                                <div class="table-responsive">
                                    <table class="table table-striped data-perusahaan">
                                        <tbody>
                                        <tr>
                                            <td></td>
                                            <td>Nama Perusahaan</td>
                                            <td>: {{$dataPemohon->mohon_cust_nama}}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Nomor Akta Pendirian</td>
                                            <td>: {{$dataPemohon->mohon_cust_nomor_akta_pendirian}}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Nama Pemilik</td>
                                            <td>: {{$dataPemohon->mohon_cust_nama_pemilik}}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Nama Pimpinan</td>
                                            <td>: {{$dataPemohon->mohon_cust_nama_pimpinan}}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Nama Wakil Manajemen</td>
                                            <td>: {{$dataPemohon->mohon_cust_nama_wakil_manajemen}}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Telp (Perusahaan)</td>
                                            <td>: {{$dataPemohon->mohon_cust_nomor_telp}}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Fax</td>
                                            <td>: {{$dataPemohon->mohon_cust_nomor_fax}}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Nomer HP (CP)</td>
                                            <td>: {{$dataPemohon->mohon_cust_nomor_hp}}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Badan Hukum</td>
                                            <td>: {{$dataPemohon->master_badan_hukum?->badan_hukum_nama}}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Jenis Perusahaan</td>
                                            <td>: {{$dataPemohon->master_jenis_perusahaan?->jenis_perusahaan_nama}}</td>
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
                                            <td></td>
                                            <td>Negara</td>
                                            <td>: {{$dataPemohon->master_negara?->negara_nama}}</td>
                                        </tr>
                                        @if($dataPemohon->master_negara?->negara_kode == "ID")
                                            <tr>
                                                <td></td>
                                                <td>Provinsi</td>
                                                <td>: {{$dataPemohon->master_provinsi?->prov_nama}}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Kabupaten</td>
                                                <td>: {{$dataPemohon->master_kabupaten?->kab_nama}}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Kecamatan</td>
                                                <td>: {{$dataPemohon->master_kecamatan?->kec_nama}}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td></td>
                                            <td>Alamat</td>
                                            <td>: {{$dataPemohon->mohon_cust_alamat}}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Luas Tanah</td>
                                            <td>: {{$dataPemohon->mohon_cust_luas_tanah}}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Luas Bangunan</td>
                                            <td>: {{$dataPemohon->mohon_cust_luas_bangunan}}</td>
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
                                            <td></td>
                                            <td>Jumlah Shift (dalam sehari)</td>
                                            <td>: {{$dataPemohon->mohon_cust_shif_kerja}}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Jumlah Bagian</td>
                                            <td>: {{$dataPemohon->mohon_cust_jumlah_bagian}}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>
                                                Jumlah Karyawan
                                                <ol id="jumlah_karyawan">
                                                    <li>Manajemen: {{$dataPemohon->mohon_cust_jumlah_manajemen}} orang</li>
                                                    <li>Administrasi: {{$dataPemohon->mohon_cust_jumlah_administrasi}} orang</li>
                                                    <li>Part Time: {{$dataPemohon->mohon_cust_jumlah_part_time}} orang</li>
                                                    <li>
                                                        Operasional:
                                                        <ul>
                                                            <li>Shift 1: {{$dataPemohon->mohon_cust_jumlah_shift_1}} orang</li>
                                                            <li>Shift 2: {{$dataPemohon->mohon_cust_jumlah_shift_2}} orang</li>
                                                            <li>Shift 3: {{$dataPemohon->mohon_cust_jumlah_shift_3}} orang</li>
                                                        </ul>
                                                    </li>
                                                    <li>Non Permanen: {{$dataPemohon->mohon_cust_jumlah_non_permanen}} orang</li>
                                                </ol>
                                            </td>
                                            <td></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                            @if(!empty($dataPemohon->sis_permohonan_pabriks))
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
                                                        @foreach($dataPemohon->sis_permohonan_pabriks as $pabrik)
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
                                            <td></td>
                                            <td>Pertanyaan Tambahan</td>
                                            <td>: <a target="_blank"
                                                     href="{{url(asset($dataPemohon->mohon_pertanyaan_filepath))}}">
                                                    <i class="fad fa-download"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
