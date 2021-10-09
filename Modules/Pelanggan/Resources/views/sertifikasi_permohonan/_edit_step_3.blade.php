@push('css')
    <style>
        .tabel_pabrik {
            width: 100%;
        }

        .tabel_pabrik td:first-child {
            width: 20%;
        }
    </style>
@endpush

<div class="row" id="vueStepThree">
    <div class="col-md-12">
        <h3>Data Perusahaan</h3>
        <div>
            <ol>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="step3_perusahaan_nama">
                                    Nama Perusahaan*
                                    <x-linked-icon></x-linked-icon>
                                </label>
                                <input id="step3_perusahaan_nama" name="step3_perusahaan_nama" class="form-control"
                                       @change="updateDataPemohon('cust_nama','mohon_cust_nama',...arguments)">
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="step3_perusahaan_akta">
                                    Nomor Akta Pendirian*
                                    <x-linked-icon></x-linked-icon>
                                </label>
                                <input id="step3_perusahaan_akta" name="step3_perusahaan_akta" class="form-control"
                                       @change="updateDataPemohon('cust_nomor_akta_pendirian','mohon_cust_nomor_akta_pendirian',...arguments)">
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="step3_perusahaan_nama_pemilik">
                                    Nama Pemilik*
                                    <x-linked-icon></x-linked-icon>
                                </label>
                                <input id="step3_perusahaan_nama_pemilik" name="step3_perusahaan_nama_pemilik"
                                       class="form-control"
                                       @change="updateDataPemohon('cust_nama_pemilik','mohon_cust_nama_pemilik',...arguments)">
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="step3_perusahaan_nama_pimpinan">
                                    Nama Pimpinan*
                                    <x-linked-icon></x-linked-icon>
                                </label>
                                <input id="step3_perusahaan_nama_pimpinan" name="step3_perusahaan_nama_pimpinan"
                                       class="form-control"
                                       @change="updateDataPemohon('cust_nama_pimpinan','mohon_cust_nama_pimpinan',...arguments)">
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="step3_perusahaan_nama_wakil">
                                    Nama Wakil Manajemen*
                                    <x-linked-icon></x-linked-icon>
                                </label>
                                <input id="step3_perusahaan_nama_wakil" name="step3_perusahaan_nama_wakil"
                                       class="form-control"
                                       @change="updateDataPemohon('cust_nama_wakil_manajemen','mohon_cust_nama_wakil_manajemen',...arguments)">
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="step3_perusahaan_telp">
                                    Telp (Perusahaan)*
                                    <x-linked-icon></x-linked-icon>
                                </label>
                                <input id="step3_perusahaan_telp" name="step3_perusahaan_telp" class="form-control"
                                       @change="updateDataPemohon('cust_nomor_telp','mohon_cust_nomor_telp',...arguments)">
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="step3_perusahaan_fax">
                                    Fax*
                                    <x-linked-icon></x-linked-icon>
                                </label>
                                <input id="step3_perusahaan_fax" name="step3_perusahaan_fax" class="form-control"
                                       @change="updateDataPemohon('cust_nomor_fax','mohon_cust_nomor_fax',...arguments)">
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="step3_perusahaan_cp">
                                    Nomer HP (CP)*
                                    <x-linked-icon></x-linked-icon>
                                </label>
                                <input id="step3_perusahaan_cp" name="step3_perusahaan_cp" class="form-control"
                                       @change="updateDataPemohon('cust_nomor_hp','mohon_cust_nomor_hp',...arguments)">
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="step3_perusahaan_badan_hukum">
                                    Badan Hukum*
                                    <x-linked-icon></x-linked-icon>
                                </label>
                                <select name="step3_perusahaan_badan_hukum" id="step3_perusahaan_badan_hukum"
                                        class="form-control" @change="updateDataPemohon('badan_hukum_id',...arguments)">
                                    <option disabled selected>--Pilih Badan Hukum--</option>
                                    @foreach($masterBadanHukum as $bh)
                                        <option value="{{$bh->badan_hukum_id}}">{{$bh->badan_hukum_nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="step3_perusahaan_jenis">
                                    Jenis Perusahaan*
                                    <x-linked-icon></x-linked-icon>
                                </label>
                                <select name="step3_perusahaan_jenis" id="step3_perusahaan_jenis"
                                        class="form-control"
                                        @change="updateDataPemohon('jenis_perusahaan_id',...arguments)">
                                    <option disabled selected>--Pilih Jenis Perusahaan--</option>
                                    @foreach($masterJenisPerusahaan as $jp)
                                        <option
                                            value="{{$jp->jenis_perusahaan_id}}">{{$jp->jenis_perusahaan_nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </li>
            </ol>
        </div>
    </div>

    <div class="col-md-12">
        <h3>Data Lokasi</h3>
        <div>
            <ol start="11">
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="step3_perusahaan_negara">
                                    Negara*
                                    <x-linked-icon></x-linked-icon>
                                </label>
                                <input id="step3_perusahaan_negara" name="step3_perusahaan_negara" class="form-control"
                                       style="width: 100%">
                            </div>
                        </div>
                    </div>
                </li>
                <template v-if="is_indonesia">
                    <li>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="step3_perusahaan_provinsi">
                                        Provinsi*
                                        <x-linked-icon></x-linked-icon>
                                    </label>
                                    <input id="step3_perusahaan_provinsi" name="step3_perusahaan_provinsi"
                                           class="form-control"
                                           style="width: 100%">
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="step3_perusahaan_kabupaten">
                                        Kabupaten*
                                        <x-linked-icon></x-linked-icon>
                                    </label>
                                    <input id="step3_perusahaan_kabupaten" name="step3_perusahaan_kabupaten"
                                           class="form-control" style="width: 100%">
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="step3_perusahaan_kecamatan">
                                        Kecamatan*
                                        <x-linked-icon></x-linked-icon>
                                    </label>
                                    <input id="step3_perusahaan_kecamatan" name="step3_perusahaan_kecamatan"
                                           class="form-control" style="width: 100%">
                                </div>
                            </div>
                        </div>
                    </li>
                </template>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="step3_perusahaan_alamat">
                                    Alamat*
                                    <x-linked-icon></x-linked-icon>
                                </label>
                                <textarea id="step3_perusahaan_alamat" name="step3_perusahaan_alamat" cols="30"
                                          class="form-control"
                                          @change="updateDataPemohon('cust_alamat','mohon_cust_alamat',...arguments)"></textarea>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="step3_perusahaan_luas_tanah">
                                    Luas Tanah*
                                    <x-linked-icon></x-linked-icon>
                                </label>
                                <input id="step3_perusahaan_luas_tanah" name="step3_perusahaan_luas_tanah"
                                       class="form-control"
                                       @change="updateDataPemohon('cust_luas_tanah','mohon_cust_luas_tanah',...arguments)">
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="step3_perusahaan_luas_bangunan">
                                    Luas Bangunan*
                                    <x-linked-icon></x-linked-icon>
                                </label>
                                <input id="step3_perusahaan_luas_bangunan" name="step3_perusahaan_luas_bangunan"
                                       class="form-control"
                                       @change="updateDataPemohon('cust_luas_bangunan','mohon_cust_luas_bangunan',...arguments)">
                            </div>
                        </div>
                    </div>
                </li>
            </ol>
        </div>
    </div>

    <div class="col-md-12">
        <h3>Data Operasional</h3>
        <div>
            <ol :start="is_indonesia ? 18 : 15">
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="step3_perusahaan_jml_shift">
                                    Jumlah Shift (dalam sehari)*
                                    <x-linked-icon></x-linked-icon>
                                </label>
                                <input id="step3_perusahaan_jml_shift" name="step3_perusahaan_jml_shift"
                                       class="form-control" type="number" min="0"
                                       @change="updateDataPemohon('cust_shif_kerja','mohon_cust_shif_kerja',...arguments)">
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="step3_perusahaan_jml_bagian">
                                    Jumlah Bagian*
                                    <x-linked-icon></x-linked-icon>
                                </label>
                                <input id="step3_perusahaan_jml_bagian" name="step3_perusahaan_jml_bagian"
                                       class="form-control" type="number" min="0"
                                       @change="updateDataPemohon('cust_jumlah_bagian','mohon_cust_jumlah_bagian',...arguments)">
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Jumlah Karyawan* :</label>
                                {{--<input id="step3_perusahaan_" name="step3_perusahaan_" class="form-control">--}}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <ol style="margin-top: -20px">
                                <li>
                                    <div class="form-row">
                                        <div class="col-md-8">
                                            <!-- Input Group -->
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Manajemen</span>
                                                </div>
                                                <input type="number" min="0" class="form-control" aria-label="Manajemen"
                                                       placeholder="Berapa orang ?"
                                                       name="step3_perusahaan_jml_manajemen"
                                                       id="step3_perusahaan_jml_manajemen"
                                                       @change="updateDataPemohon('cust_jumlah_manajemen','mohon_cust_jumlah_manajemen',...arguments)">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fad fa-person"></i>&nbsp; Orang</span>
                                                </div>
                                            </div>
                                            <!-- /input group -->
                                        </div>
                                        <x-linked-icon></x-linked-icon>
                                    </div>
                                    <!-- /form row -->
                                </li>
                                <li>
                                    <div class="form-row">
                                        <div class="col-md-8">
                                            <!-- Input Group -->
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Administrasi</span>
                                                </div>
                                                <input type="number" min="0" class="form-control" aria-label="Manajemen"
                                                       placeholder="Berapa orang ?"
                                                       id="step3_perusahaan_jml_administrasi"
                                                       name="step3_perusahaan_jml_administrasi"
                                                       @change="updateDataPemohon('cust_jumlah_administrasi','mohon_cust_jumlah_administrasi',...arguments)">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fad fa-person"></i>&nbsp; Orang</span>
                                                </div>
                                            </div>
                                            <!-- /input group -->
                                        </div>
                                        <x-linked-icon></x-linked-icon>
                                    </div>
                                    <!-- /form row -->
                                </li>
                                <li>
                                    <div class="form-row">
                                        <div class="col-md-8">
                                            <!-- Input Group -->
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Part Time</span>
                                                </div>
                                                <input type="number" min="0" class="form-control" aria-label="Manajemen"
                                                       placeholder="Berapa orang ?" id="step3_perusahaan_jml_part_time"
                                                       name="step3_perusahaan_jml_part_time"
                                                       @change="updateDataPemohon('cust_jumlah_part_time','mohon_cust_jumlah_part_time',...arguments)">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fad fa-person"></i>&nbsp; Orang</span>
                                                </div>
                                            </div>
                                            <!-- /input group -->
                                        </div>
                                        <x-linked-icon></x-linked-icon>
                                    </div>
                                    <!-- /form row -->
                                </li>
                                <li style="padding-top: 20px; padding-bottom: 20px">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="step3_perusahaan_">Operasional* :</label>
                                        </div>
                                    </div>

                                    <ul style="margin-top: -20px">
                                        <li>
                                            <div class="form-row">
                                                <div class="col-md-8">
                                                    <!-- Input Group -->
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Shift 1</span>
                                                        </div>
                                                        <input type="number" min="0" class="form-control"
                                                               aria-label="Shift 1"
                                                               placeholder="Berapa orang ?"
                                                               id="step3_perusahaan_jml_shift_1"
                                                               name="step3_perusahaan_jml_shift_1"
                                                               @change="updateDataPemohon('cust_jumlah_shift_1','mohon_cust_jumlah_shift_1',...arguments)">
                                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fad fa-person"></i>&nbsp; Orang
                                            </span>
                                                        </div>
                                                    </div>
                                                    <!-- /input group -->
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-row">
                                                <div class="col-md-8">
                                                    <!-- Input Group -->
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Shift 2</span>
                                                        </div>
                                                        <input type="number" min="0" class="form-control"
                                                               aria-label="Shift 2"
                                                               placeholder="Berapa orang ?"
                                                               id="step3_perusahaan_jml_shift_2"
                                                               name="step3_perusahaan_jml_shift_2"
                                                               @change="updateDataPemohon('cust_jumlah_shift_2','mohon_cust_jumlah_shift_2',...arguments)">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><i class="fad fa-person"></i>&nbsp; Orang</span>
                                                        </div>
                                                    </div>
                                                    <!-- /input group -->
                                                </div>
                                                <x-linked-icon></x-linked-icon>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-row">
                                                <div class="col-md-8">
                                                    <!-- Input Group -->
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Shift 3</span>
                                                        </div>
                                                        <input type="number" min="0" class="form-control"
                                                               aria-label="Shift 3"
                                                               placeholder="Berapa orang ?"
                                                               id="step3_perusahaan_jml_shift_3"
                                                               name="step3_perusahaan_jml_shift_3"
                                                               @change="updateDataPemohon('cust_jumlah_shift_3','mohon_cust_jumlah_shift_3',...arguments)">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><i class="fad fa-person"></i>&nbsp; Orang</span>
                                                        </div>
                                                    </div>
                                                    <!-- /input group -->
                                                </div>
                                                <x-linked-icon></x-linked-icon>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <div class="form-row">
                                        <div class="col-md-8">
                                            <!-- Input Group -->
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Non Permanen</span>
                                                </div>
                                                <input type="number" min="0" class="form-control"
                                                       aria-label="Non Permanen"
                                                       placeholder="Berapa orang ?"
                                                       id="step3_perusahaan_jml_non_permanen"
                                                       name="step3_perusahaan_jml_non_permanen"
                                                       @change="updateDataPemohon('cust_jumlah_non_permanen','mohon_cust_jumlah_non_permanen',...arguments)">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fad fa-person"></i>&nbsp; Orang</span>
                                                </div>
                                            </div>
                                            <!-- /input group -->
                                        </div>
                                        <x-linked-icon></x-linked-icon>
                                    </div>
                                    <!-- /form row -->
                                </li>
                            </ol>
                        </div>
                    </div>
                </li>
            </ol>
        </div>
    </div>
    <div class="col-md-12" style="padding-top: 20px">
        <h3>Data Pabrik</h3>
        <div>
            <ol :start="is_indonesia ? 22 : 19">
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Detail Lokasi Pabrik* :</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <ol>
                                <li style="padding-left: 10px; padding-bottom: 50px" v-for="(n, idx) in data_pabrik">
                                    <div class="table-responsive">
                                        <table class="tabel_pabrik">
                                            <tr>
                                                <td>Nama Pabrik
                                                    <x-linked-icon></x-linked-icon>
                                                </td>
                                                <td>
                                                    <input :id="'step3_pabrik_nama_'+n.mohon_pabrik_id"
                                                           :value="n.mohon_pabrik_nama"
                                                           :name="'step3_pabrik_nama_'+n.mohon_pabrik_id" type="text"
                                                           class="form-control" aria-label="Nama Pabrik"
                                                           @change="updateDataPabrik(n.mohon_pabrik_id, 'pabrik_nama','mohon_pabrik_nama',...arguments)">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Np Telp
                                                    <x-linked-icon></x-linked-icon>
                                                </td>
                                                <td>
                                                    <input :id="'step3_pabrik_telp_'+n.mohon_pabrik_id"
                                                           :value="n.mohon_pabrik_nomor_telp"
                                                           :name="'step3_pabrik_telp_'+n.mohon_pabrik_id" type="text"
                                                           class="form-control" aria-label="Np Telp"
                                                           @change="updateDataPabrik(n.mohon_pabrik_id, 'pabrik_nomor_telp','mohon_pabrik_nomor_telp',...arguments)">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>No HP
                                                    <x-linked-icon></x-linked-icon>
                                                </td>
                                                <td>
                                                    <input :id="'step3_pabrik_hp_'+n.mohon_pabrik_id"
                                                           :value="n.mohon_pabrik_nomor_hp"
                                                           :name="'step3_pabrik_hp_'+n.mohon_pabrik_id"
                                                           type="text" class="form-control" aria-label="No HP"
                                                           @change="updateDataPabrik(n.mohon_pabrik_id, 'pabrik_nomor_hp','mohon_pabrik_nomor_hp',...arguments)">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Fax
                                                    <x-linked-icon></x-linked-icon>
                                                </td>
                                                <td>
                                                    <input :id="'step3_pabrik_fax_'+n.mohon_pabrik_id"
                                                           :value="n.mohon_pabrik_nomor_fax"
                                                           :name="'step3_pabrik_fax_'+n.mohon_pabrik_id"
                                                           type="text" class="form-control" aria-label="Fax"
                                                           @change="updateDataPabrik(n.mohon_pabrik_id, 'pabrik_nomor_fax','mohon_pabrik_nomor_fax',...arguments)">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Provinsi
                                                    <x-linked-icon></x-linked-icon>
                                                </td>
                                                <td>
                                                    <input style="width: 100%"
                                                           :id="'step3_pabrik_provinsi_'+n.mohon_pabrik_id"
                                                           :name="'step3_pabrik_provinsi_'+n.mohon_pabrik_id"
                                                           type="text" class="form-control" aria-label="Provinsi">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Kabupaten
                                                    <x-linked-icon></x-linked-icon>
                                                </td>
                                                <td>
                                                    <input style="width: 100%"
                                                           :id="'step3_pabrik_kabupaten_'+n.mohon_pabrik_id"
                                                           :name="'step3_pabrik_kabupaten_'+n.mohon_pabrik_id"
                                                           type="text" class="form-control" aria-label="Kabupaten">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Kecamatan
                                                    <x-linked-icon></x-linked-icon>
                                                </td>
                                                <td>
                                                    <input style="width: 100%"
                                                           :id="'step3_pabrik_kecamatan_'+n.mohon_pabrik_id"
                                                           :name="'step3_pabrik_kecamatan_'+n.mohon_pabrik_id"
                                                           type="text" class="form-control" aria-label="Kecamatan">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>KodePos
                                                    <x-linked-icon></x-linked-icon>
                                                </td>
                                                <td>
                                                    <input :id="'step3_pabrik_kode_pos_'+n.mohon_pabrik_id"
                                                           :value="n.mohon_pabrik_kode_pos"
                                                           :name="'step3_pabrik_kode_pos_'+n.mohon_pabrik_id"
                                                           type="text" class="form-control" aria-label="KodePos"
                                                           @change="updateDataPabrik(n.mohon_pabrik_id, 'pabrik_kode_pos','mohon_pabrik_kode_pos',...arguments)">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Alamat Pabrik
                                                    <x-linked-icon></x-linked-icon>
                                                </td>
                                                <td>
                                                    <textarea class="form-control"
                                                              :id="'step3_pabrik_alamat_'+n.mohon_pabrik_id"
                                                              :name="'step3_pabrik_alamat_'+n.mohon_pabrik_id"
                                                              @change="updateDataPabrik(n.mohon_pabrik_id, 'pabrik_alamat','mohon_pabrik_alamat',...arguments)"
                                                              aria-label="Alamat Pabrik">@{{ n.mohon_pabrik_alamat }}</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Jumlah Karyawan
                                                    <x-linked-icon></x-linked-icon>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                           :value="n.mohon_pabrik_jumlah_karyawan"
                                                           :id="'step3_pabrik_jml_karyawan_'+n.mohon_pabrik_id"
                                                           :name="'step3_pabrik_jml_karyawan_'+n.mohon_pabrik_id"
                                                           @change="updateDataPabrik(n.mohon_pabrik_id, 'pabrik_jumlah_karyawan','mohon_pabrik_jumlah_karyawan',...arguments)"
                                                           aria-label="Jumlah Karyawan">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Kegiatan Utama
                                                    <x-linked-icon></x-linked-icon>
                                                </td>
                                                <td>
                                                    <textarea class="form-control"
                                                              :id="'step3_pabrik_kegiatan_utama_'+n.mohon_pabrik_id"
                                                              :name="'step3_pabrik_kegiatan_utama_'+n.mohon_pabrik_id"
                                                              @change="updateDataPabrik(n.mohon_pabrik_id, 'pabrik_kegiatan_utama','mohon_pabrik_kegiatan_utama',...arguments)"
                                                              aria-label="Kegiatan Utama">@{{ n.mohon_pabrik_kegiatan_utama }}</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Luas Tanah
                                                    <x-linked-icon></x-linked-icon>
                                                </td>
                                                <td>
                                                    <input :id="'step3_pabrik_luas_tanah_'+n.mohon_pabrik_id"
                                                           :value="n.mohon_pabrik_luas_tanah"
                                                           :name="'step3_pabrik_luas_tanah_'+n.mohon_pabrik_id"
                                                           @change="updateDataPabrik(n.mohon_pabrik_id, 'pabrik_luas_tanah','mohon_pabrik_luas_tanah',...arguments)"
                                                           type="text" class="form-control" aria-label="Luas Tanah">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Luas Bangunan
                                                    <x-linked-icon></x-linked-icon>
                                                </td>
                                                <td>
                                                    <input :id="'step3_pabrik_luas_bangunan_'+n.mohon_pabrik_id"
                                                           :value="n.mohon_pabrik_luas_bangunan"
                                                           :name="'step3_pabrik_luas_bangunan_'+n.mohon_pabrik_id"
                                                           @change="updateDataPabrik(n.mohon_pabrik_id, 'pabrik_luas_bangunan','mohon_pabrik_luas_bangunan',...arguments)"
                                                           type="text" class="form-control" aria-label="Luas Bangunan">
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div style="float:right; padding-top: 10px">
                                        <button class="btn btn-danger btn-xs" @click="delDataPabrik(n.mohon_pabrik_id)">
                                            <i class="fas fa-minus"></i> Delete
                                        </button>
                                    </div>
                                </li>
                            </ol>

                            <button class="btn btn-primary btn-xs" @click="addDataPabrik">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </li>
            </ol>
        </div>
    </div>
    <div class="col-md-12" style="padding-top: 20px">
        <h3>Petanyaan Tambahan</h3>
        <div>
            <ol :start="is_indonesia ? 23 : 20">
                <li>Lengkapi kuesioner berikut dan upload kembali dibawah
                    <a href="{{asset('files/requirement_pengajuan/pertanyaan.docx')}}">(unduh form kuesioner)</a>
                    <br><br>
                    <a href="{{asset($dataPemohon->mohon_pertanyaan_filepath)}}" target="_blank">
                        <i class="fad fa-download"></i> Unduh Formulir yang sudah anda upload
                    </a>
                </li>
                <br/>
                <input type="file" class="form-control" aria-label="Pertanyaan Tambahan"
                       @change="validateUploadPertanyaanTambahan" accept="application/pdf"
                       name="step3_pertanyaan_tambahan" id="step3_pertanyaan_tambahan">
                <small><span>Upload ulang jika ingin memperbarui (*format: PDF)</span></small>
            </ol>
        </div>
    </div>
</div>

@push('javascript')
    <script>
        $(document).ready(function () {
            window.vueStepThree = new Vue({
                el: "#vueStepThree",
                data: {
                    is_indonesia: true,
                    data_pabrik: [],
                },
                mounted() {
                    setTimeout(() => {
                        const currentStep = $('#smartwizard').smartWizard("getStepIndex");
                        if (currentStep === 2) {
                            this.start();
                        }
                    }, 500)
                },
                methods: {
                    start() {
                        this.getDataPemohon();
                        this.getDataPabrik();
                    },
                    validate() {
                        if ($.trim($("#step3_perusahaan_nama").val()) === "") throw "Inputkan Nama Perusahaan";
                        if ($.trim($("#step3_perusahaan_akta").val()) === "") throw "Inputkan Nomer Akta";
                        if ($.trim($("#step3_perusahaan_nama_pemilik").val()) === "") throw "Inputkan Nama Pemilik";
                        if ($.trim($("#step3_perusahaan_nama_pimpinan").val()) === "") throw "Inputkan Nama Pimpinan";
                        if ($.trim($("#step3_perusahaan_nama_wakil").val()) === "") throw "Inputkan Nama Wakil";
                        if ($.trim($("#step3_perusahaan_telp").val()) === "") throw "Inputkan Telp Perusahaan";
                        if ($.trim($("#step3_perusahaan_fax").val()) === "") throw "Inputkan Nomor Fax";
                        if ($.trim($("#step3_perusahaan_cp").val()) === "") throw "Inputkan Narahubung (CP)";
                        if ($.trim($("#step3_perusahaan_badan_hukum").val()) === "") throw "Pilih Badang Hubung";
                        if ($.trim($("#step3_perusahaan_jenis").val()) === "") throw "Pilih Jenis Perusahaan";
                        if ($.trim($("#step3_perusahaan_alamat").val()) === "") throw "Inputkan Alamat";
                        if ($.trim($("#step3_perusahaan_luas_tanah").val()) === "") throw "Inputkan Luas Tanah";
                        if ($.trim($("#step3_perusahaan_luas_bangunan").val()) === "") throw "Inputkan Luas Bangunan";
                        if ($.trim($("#step3_perusahaan_jml_shift").val()) === "") throw "Inputkan Jumlah Shift";
                        if ($.trim($("#step3_perusahaan_jml_bagian").val()) === "") throw "Inputkan Jumlah Bagian";
                        if ($.trim($("#step3_perusahaan_jml_manajemen").val()) === "") throw "Inputkan Jumlah Manajemen";
                        if ($.trim($("#step3_perusahaan_jml_administrasi").val()) === "") throw "Inputkan Jumlah Administrasi";
                        if ($.trim($("#step3_perusahaan_jml_part_time").val()) === "") throw "Inputkan Jumlah Part Time";
                        if ($.trim($("#step3_perusahaan_jml_shift_1").val()) === "") throw "Inputkan Jumlah Shift Ke 1";
                        if ($.trim($("#step3_perusahaan_jml_shift_2").val()) === "") throw "Inputkan Jumlah Shift Ke 2";
                        if ($.trim($("#step3_perusahaan_jml_shift_3").val()) === "") throw "Inputkan Jumlah Shift Ke 3";
                        if ($.trim($("#step3_perusahaan_jml_non_permanen").val()) === "") throw "Inputkan Jumlah Non Permanen";

                        if ($.trim($("#step3_perusahaan_negara").combogrid('getValue')) === "") throw "Pilih negara";
                        if (this.is_indonesia) {
                            if ($.trim($("#step3_perusahaan_provinsi").combogrid('getValue')) === "") throw "Pilih provinsi";
                            if ($.trim($("#step3_perusahaan_kabupaten").combogrid('getValue')) === "") throw "Pilih kabupaten";
                            if ($.trim($("#step3_perusahaan_kecamatan").combogrid('getValue')) === "") throw "Pilih kecamatan";
                        }

                        if (this.data_pabrik.length > 0) {
                            this.data_pabrik.map(e => {
                                console.log(e)
                                let pabrikNama = $.trim(e.pabrik_nama);
                                if ($.trim(e.mohon_pabrik_nama) === "" || $.trim(e.mohon_pabrik_nama) == null) throw "Inputkan Nama Pabrik";
                                if ($.trim(e.mohon_pabrik_nomor_telp) === "" || $.trim(e.mohon_pabrik_nomor_telp) == null) throw `${pabrikNama}: Inputkan Nomor Telp`;
                                if ($.trim(e.mohon_pabrik_nomor_fax) === "" || $.trim(e.mohon_pabrik_nomor_fax) == null) throw `${pabrikNama}: Inputkan Fax`;
                                if ($.trim(e.mohon_pabrik_nomor_hp) === "" || $.trim(e.mohon_pabrik_nomor_hp) == null) throw `${pabrikNama}: Inputkan Nomor HP`;
                                if ($.trim(e.prov_id) === "" || $.trim(e.prov_id) == null) throw `${pabrikNama}: Pilih Provinsi`;
                                if ($.trim(e.kab_id) === "" || $.trim(e.kab_id) == null) throw `${pabrikNama}: Pilih Kabupaten`;
                                if ($.trim(e.kec_id) === "" || $.trim(e.kec_id) == null) throw `${pabrikNama}: Pilih Kecamatan`;
                                if ($.trim(e.mohon_pabrik_alamat) === "" || $.trim(e.mohon_pabrik_alamat) == null) throw `${pabrikNama}: Inputkan Alamat Pabrik`;
                                if ($.trim(e.mohon_pabrik_kode_pos) === "" || $.trim(e.mohon_pabrik_kode_pos) == null) throw `${pabrikNama}: Inputkan Kode Pos`;
                                if ($.trim(e.mohon_pabrik_jumlah_karyawan) === "" || $.trim(e.mohon_pabrik_jumlah_karyawan) == null) throw `${pabrikNama}: Inputkan Jumlah Karyawan`;
                                if ($.trim(e.mohon_pabrik_kegiatan_utama) === "" || $.trim(e.mohon_pabrik_kegiatan_utama) == null) throw `${pabrikNama}: Inputkan Kegiatan Utama`;
                                if ($.trim(e.mohon_pabrik_luas_tanah) === "" || $.trim(e.mohon_pabrik_luas_tanah) == null) throw `${pabrikNama}: Inputkan Luas Tanah`;
                                if ($.trim(e.mohon_pabrik_luas_bangunan) === "" || $.trim(e.mohon_pabrik_luas_bangunan) == null) throw `${pabrikNama}: Inputkan Luas Bangunan`;
                            })
                        }
                    },
                    validateUploadPertanyaanTambahan(event) {
                        let uploaded = event.target.files[0];
                        if (uploaded.type !== "application/pdf") {
                            swalWithBootstrapButtons({
                                title: `Validasi`,
                                text: "File pertanyaan harus bertipe PDF",
                                type: 'warning',
                            })

                            $("#step3_pertanyaan_tambahan").val("")
                        }
                    },
                    setComboNegara(search) {
                        let self = this;
                        let url  = `{{ url("$url/ajax?action=combogrid_negara") }}`
                        if (search != null) {
                            url += '&q=' + search
                        }

                        $('#step3_perusahaan_negara').combogrid({
                            pageSize: '50',
                            panelWidth: 400,
                            pagination: true,
                            idField: 'negara_id',
                            nowrap: false,
                            textField: 'negara_nama',
                            editable: true,
                            url: url,
                            method: 'get',
                            mode: 'remote',
                            // value: self.jenis_sertifikasi_text,
                            multiSort: true,
                            fitColumns: true,
                            required: true,
                            columns: [[
                                {field: 'negara_id', hidden: true},
                                {field: 'negara_nama', title: 'Negara', width: 250, sortable: true,},
                                // {field: 'negara_kode', title: 'Kode Negara', width: 100, sortable: true,},
                            ]],
                            onSelect: function (index, row) {
                                self.is_indonesia = row.negara_nama.toLowerCase() == "indonesia"
                                self.updateDataPemohonByValue("negara_id", row.negara_id)

                                if (self.is_indonesia) {
                                    setTimeout(() => self.setComboProvinsi(), 500)
                                } else {
                                    self.updateDataPemohonByValue("prov_id", '--')
                                    self.updateDataPemohonByValue("kec_id", '--')
                                    self.updateDataPemohonByValue("kab_id", '--')
                                }
                            },
                        });
                    },
                    setComboProvinsi(search) {
                        let self = this;
                        let url  = `{{ url("$url/ajax?action=combogrid_provinsi") }}`
                        if (search != null) {
                            url += '&q=' + search
                        }

                        $('#step3_perusahaan_provinsi').combogrid({
                            pageSize: '50',
                            panelWidth: 400,
                            pagination: true,
                            idField: 'prov_id',
                            nowrap: false,
                            textField: 'prov_nama',
                            editable: true,
                            url: url,
                            method: 'get',
                            mode: 'remote',
                            // value: self.jenis_sertifikasi_text,
                            multiSort: true,
                            fitColumns: true,
                            required: true,
                            columns: [[
                                {field: 'prov_id', hidden: true},
                                {field: 'prov_nama', title: 'Provinsi', width: 250, sortable: true,},
                            ]],
                            onSelect: function (index, row) {
                                self.updateDataPemohonByValue("prov_id", row.prov_id)
                                self.updateDataPemohonByValue("kec_id", '--')
                                self.updateDataPemohonByValue("kab_id", '--')
                                try {
                                    $("#step3_perusahaan_kecamatan").combogrid('clear');
                                    $("#step3_perusahaan_kabupaten").combogrid('clear');
                                } catch (e) {
                                    console.log(e)
                                }
                                self.setComboKabupaten(row.prov_id)
                            },
                        });
                    },
                    setComboKabupaten(provId, search) {
                        let self = this;
                        let url  = `{{ url("$url/ajax?action=combogrid_kabupaten") }}&prov_id=${provId}`
                        if (search != null) {
                            url += '&q=' + search
                        }

                        $('#step3_perusahaan_kabupaten').combogrid({
                            pageSize: '50',
                            panelWidth: 400,
                            pagination: true,
                            idField: 'kab_id',
                            nowrap: false,
                            textField: 'kab_nama',
                            editable: true,
                            url: url,
                            method: 'get',
                            mode: 'remote',
                            // value: self.jenis_sertifikasi_text,
                            multiSort: true,
                            fitColumns: true,
                            required: true,
                            columns: [[
                                {field: 'kab_id', hidden: true},
                                {field: 'kab_nama', title: 'Kabupaten', width: 250, sortable: true,},
                            ]],
                            onSelect: function (index, row) {
                                self.updateDataPemohonByValue("kab_id", row.kab_id)
                                self.updateDataPemohonByValue("kec_id", '--')
                                try {
                                    $("#step3_perusahaan_kecamatan").combogrid('clear');
                                } catch (e) {
                                    console.log(e)
                                }
                                self.setComboKecamatan(row.kab_id);
                            },
                        });
                    },
                    setComboKecamatan(kabId, search) {
                        let self = this;
                        let url  = `{{ url("$url/ajax?action=combogrid_kecamatan") }}&kab_id=${kabId}`
                        if (search != null) {
                            url += '&q=' + search
                        }

                        $('#step3_perusahaan_kecamatan').combogrid({
                            pageSize: '50',
                            panelWidth: 400,
                            pagination: true,
                            idField: 'kec_id',
                            nowrap: false,
                            textField: 'kec_nama',
                            editable: true,
                            url: url,
                            method: 'get',
                            mode: 'remote',
                            // value: self.jenis_sertifikasi_text,
                            multiSort: true,
                            fitColumns: true,
                            required: true,
                            columns: [[
                                {field: 'kec_id', hidden: true},
                                {field: 'kec_nama', title: 'Kecamatan', width: 250, sortable: true,},
                            ]],
                            onSelect: function (index, row) {
                                self.updateDataPemohonByValue("kec_id", row.kec_id)
                            },
                        });
                    },
                    setComboPabrikProvinsi(pabrikId, search) {
                        let self = this;
                        let url  = `{{ url("$url/ajax?action=combogrid_provinsi") }}`
                        if (search != null) {
                            url += '&q=' + search
                        }

                        $('#step3_pabrik_provinsi_' + pabrikId).combogrid({
                            pageSize: '50',
                            panelWidth: 400,
                            pagination: true,
                            idField: 'prov_id',
                            nowrap: false,
                            textField: 'prov_nama',
                            editable: true,
                            url: url,
                            method: 'get',
                            mode: 'remote',
                            multiSort: true,
                            fitColumns: true,
                            required: true,
                            columns: [[
                                {field: 'prov_id', hidden: true},
                                {field: 'prov_nama', title: 'Provinsi', width: 250, sortable: true,},
                            ]],
                            onSelect: function (index, row) {
                                // update vue model
                                let idx                       = self.data_pabrik.findIndex(e => e.mohon_pabrik_id === pabrikId)
                                self.data_pabrik[idx].prov_id = row.prov_id
                                self.data_pabrik[idx].kab_id  = null
                                self.data_pabrik[idx].kec_id  = null

                                // update server
                                self.updateDataPabrikByValue(pabrikId, "prov_id", row.prov_id)
                                self.updateDataPabrikByValue(pabrikId, "kec_id", '--')
                                self.updateDataPabrikByValue(pabrikId, "kab_id", '--')
                                $("#step3_pabrik_kabupaten_" + pabrikId).combogrid('clear');
                                $("#step3_pabrik_kecamatan_" + pabrikId).combogrid('clear');
                                self.setComboPabrikKabupaten(pabrikId, row.prov_id)
                            },
                        });
                    },
                    setComboPabrikKabupaten(pabrikId, provId, search) {
                        let self = this;
                        let url  = `{{ url("$url/ajax?action=combogrid_kabupaten") }}&prov_id=${provId}`
                        if (search != null) {
                            url += '&q=' + search
                        }

                        $('#step3_pabrik_kabupaten_' + pabrikId).combogrid({
                            pageSize: '50',
                            panelWidth: 400,
                            pagination: true,
                            idField: 'kab_id',
                            nowrap: false,
                            textField: 'kab_nama',
                            editable: true,
                            url: url,
                            method: 'get',
                            mode: 'remote',
                            multiSort: true,
                            fitColumns: true,
                            required: true,
                            columns: [[
                                {field: 'kab_id', hidden: true},
                                {field: 'kab_nama', title: 'Kabupaten', width: 250, sortable: true,},
                            ]],
                            onSelect: function (index, row) {
                                // update vue model
                                let idx                      = self.data_pabrik.findIndex(e => e.mohon_pabrik_id === pabrikId)
                                self.data_pabrik[idx].kab_id = row.kab_id
                                self.data_pabrik[idx].kec_id = null

                                // update server
                                self.updateDataPabrikByValue(pabrikId, "kab_id", row.kab_id)
                                self.updateDataPabrikByValue(pabrikId, "kec_id", '--')
                                $("#step3_pabrik_kecamatan_" + pabrikId).combogrid('clear');
                                self.setComboPabrikKecamatan(pabrikId, row.kab_id);
                            },
                        });
                    },
                    setComboPabrikKecamatan(pabrikId, kabId, search) {
                        let self = this;
                        let url  = `{{ url("$url/ajax?action=combogrid_kecamatan") }}&kab_id=${kabId}`
                        if (search != null) {
                            url += '&q=' + search
                        }

                        $('#step3_pabrik_kecamatan_' + pabrikId).combogrid({
                            pageSize: '50',
                            panelWidth: 400,
                            pagination: true,
                            idField: 'kec_id',
                            nowrap: false,
                            textField: 'kec_nama',
                            editable: true,
                            url: url,
                            method: 'get',
                            mode: 'remote',
                            multiSort: true,
                            fitColumns: true,
                            required: true,
                            columns: [[
                                {field: 'kec_id', hidden: true},
                                {field: 'kec_nama', title: 'Kecamatan', width: 250, sortable: true,},
                            ]],
                            onSelect: function (index, row) {
                                // update vue model
                                let idx                      = self.data_pabrik.findIndex(e => e.mohon_pabrik_id === pabrikId)
                                self.data_pabrik[idx].kec_id = row.kec_id

                                // update server
                                self.updateDataPabrikByValue(pabrikId, "kec_id", row.kec_id)
                            },
                        });
                    },
                    getDataPemohon() {
                        $.get(`{!! url("$url/ajax?action=permohonan_kondisi_perusahaan&mohon_id=" . $dataPemohon->mohon_id) !!}`)
                            .then(response => {
                                let res = response.results;
                                $("#step3_perusahaan_nama").val(res.mohon_cust_nama);
                                $("#step3_perusahaan_akta").val(res.mohon_cust_nomor_akta_pendirian);
                                $("#step3_perusahaan_nama_pemilik").val(res.mohon_cust_nama_pemilik);
                                $("#step3_perusahaan_nama_pimpinan").val(res.mohon_cust_nama_pimpinan);
                                $("#step3_perusahaan_nama_wakil").val(res.mohon_cust_nama_wakil_manajemen);
                                $("#step3_perusahaan_telp").val(res.mohon_cust_nomor_telp);
                                $("#step3_perusahaan_fax").val(res.mohon_cust_nomor_fax);
                                $("#step3_perusahaan_cp").val(res.mohon_cust_nomor_hp);
                                $("#step3_perusahaan_badan_hukum").val(res.badan_hukum_id);
                                $("#step3_perusahaan_jenis").val(res.jenis_perusahaan_id);

                                $("#step3_perusahaan_alamat").val(res.mohon_cust_alamat);
                                $("#step3_perusahaan_luas_tanah").val(res.mohon_cust_luas_tanah);
                                $("#step3_perusahaan_luas_bangunan").val(res.mohon_cust_luas_bangunan);

                                $("#step3_perusahaan_produksi_tahunan").val(res.mohon_cust_kapasitas_produksi_tahunan);
                                $("#step3_perusahaan_produksi_tahunan_satuan").val(res.mohon_cust_kapasitas_produksi_tahunan_satuan);
                                $("#step3_perusahaan_jml_shift").val(res.mohon_cust_shif_kerja);
                                $("#step3_perusahaan_jml_bagian").val(res.mohon_cust_jumlah_bagian);
                                $("#step3_perusahaan_jml_manajemen").val(res.mohon_cust_jumlah_manajemen);
                                $("#step3_perusahaan_jml_administrasi").val(res.mohon_cust_jumlah_administrasi);
                                $("#step3_perusahaan_jml_part_time").val(res.mohon_cust_jumlah_part_time);
                                $("#step3_perusahaan_jml_shift_1").val(res.mohon_cust_jumlah_shift_1);
                                $("#step3_perusahaan_jml_shift_2").val(res.mohon_cust_jumlah_shift_2);
                                $("#step3_perusahaan_jml_shift_3").val(res.mohon_cust_jumlah_shift_3);
                                $("#step3_perusahaan_jml_non_permanen").val(res.mohon_cust_jumlah_non_permanen);


                                // Enable Combogrid Negara
                                this.setComboNegara(res.negara_id);
                                this.is_indonesia = res.negara_nama.toLowerCase() == "indonesia"
                                if (this.is_indonesia) {
                                    this.setComboProvinsi(res.prov_id);
                                    this.setComboKabupaten(res.prov_id, res.kab_id);
                                    this.setComboKecamatan(res.kab_id, res.kec_id);
                                }

                                // Set Provinsi
                                $("#step3_perusahaan_negara").combogrid('setValue', res.negara_id);
                                if (this.is_indonesia) {
                                    $("#step3_perusahaan_provinsi").combogrid('setValue', res.prov_id);
                                    $("#step3_perusahaan_kabupaten").combogrid('setValue', res.kab_id);
                                    $("#step3_perusahaan_kecamatan").combogrid('setValue', res.kec_id);
                                }
                            })
                            .fail((xhr) => {
                                if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                            });
                    },
                    getDataPabrik() {
                        $.get(`{!! url("$url/ajax?action=permohonan_pabrik_data&mohon_id="  . $dataPemohon->mohon_id) !!}`)
                            .then(response => {
                                this.data_pabrik = response.results
                                setTimeout(() => {
                                    if (this.data_pabrik.length > 0) {
                                        this.data_pabrik.map(e => {
                                            this.setComboPabrikProvinsi(e.mohon_pabrik_id, e.prov_id)
                                            this.setComboPabrikKabupaten(e.mohon_pabrik_id, e.prov_id, e.kab_id);
                                            this.setComboPabrikKecamatan(e.mohon_pabrik_id, e.kab_id, e.kec_id);
                                            $("#step3_pabrik_provinsi_" + e.mohon_pabrik_id).combogrid('setValue', e.prov_id);
                                            $("#step3_pabrik_kabupaten_" + e.mohon_pabrik_id).combogrid('setValue', e.kab_id);
                                            $("#step3_pabrik_kecamatan_" + e.mohon_pabrik_id).combogrid('setValue', e.kec_id);
                                        })
                                    }
                                    $(".tab-content").height("100%");
                                }, 500)
                            })
                            .fail((xhr) => {
                                if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                            });
                    },
                    addDataPabrik() {
                        $.post(`{!! url("$url/ajax?action=permohonan_pabrik_add&mohon_id=" . $dataPemohon->mohon_id) !!}`)
                            .then(() => {
                                this.getDataPabrik()
                            })
                    },
                    delDataPabrik(pabrikId) {
                        let dtPabrik = this.data_pabrik.find(e => e.mohon_pabrik_id === pabrikId)
                        swalWithBootstrapButtons({
                            title: `Hapus Pabrik: ${dtPabrik.pabrik_nama} ?`,
                            text: `Menghapus data bersifat permanen dan tidak dapat dikembalikan`,
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.value) {
                                $.post(`{{url("$url/ajax?action=permohonan_pabrik_delete")}}`, {
                                    "mohon_pabrik_id": pabrikId,
                                    "mohon_id": {{$dataPemohon->mohon_id}}
                                })
                                    .then(() => {
                                        this.getDataPabrik()
                                    })
                                    .fail((xhr) => {
                                        if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                        else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                                    });
                            }
                        });

                    },
                    updateDataPabrikByValue(pabrikId, parameter, value) {
                        $.post(`{!! url("$url/ajax?action=permohonan_pabrik_update&mohon_id=" . $dataPemohon->mohon_id) !!}`, {
                            mohon_pabrik_id: pabrikId,
                            parameter_pelanggan: parameter,
                            parameter_permohonan: parameter,
                            value
                        })
                            .fail((xhr) => {
                                if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                            });
                    },
                    updateDataPabrik(pabrikId, paramsPelanggan, paramsPermohonan, event) {
                        // Khusus Nama Pabrik
                        if (paramsPermohonan === "mohon_pabrik_nama") {
                            let idx                           = this.data_pabrik.findIndex(e => e.mohon_pabrik_id === pabrikId)
                            this.data_pabrik[idx].pabrik_nama = event.target.value
                        }

                        $.post(`{!! url("$url/ajax?action=permohonan_pabrik_update&mohon_id=" . $dataPemohon->mohon_id) !!}`, {
                            mohon_pabrik_id: pabrikId,
                            parameter_pelanggan: paramsPelanggan,
                            parameter_permohonan: paramsPermohonan,
                            value: event.target.value
                        }).fail((xhr) => {
                            if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                            else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                        });
                    },
                    updateDataPemohonByValue(parameter, value) {
                        $.post(`{!! url("$url/ajax?action=permohonan_update_kondisi_perusahaan&mohon_id=" . $dataPemohon->mohon_id) !!}`, {
                            parameter_main: parameter,
                            parameter_permohonan: parameter,
                            value
                        }).fail((xhr) => {
                            if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                            else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                        });
                    },
                    updateDataPemohon(paramsMain, paramsPermohonan, event) {
                        $.post(`{!! url("$url/ajax?action=permohonan_update_kondisi_perusahaan&mohon_id=" . $dataPemohon->mohon_id) !!}`, {
                            parameter_main: paramsMain,
                            parameter_permohonan: paramsPermohonan,
                            value: event.target.value
                        }).fail((xhr) => {
                            if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                            else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                        });
                    },
                }
            });
        });
    </script>
@endpush
