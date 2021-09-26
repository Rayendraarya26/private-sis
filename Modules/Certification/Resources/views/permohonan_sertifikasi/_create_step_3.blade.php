<div class="row" id="vueStepThree">
    <div class="col-md-12">
        <h3>Data Perusahaan</h3>
        <div>
            <ol>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_nama">Nama Perusahaan*</label>
                            <input id="step3_perusahaan_nama" name="step3_perusahaan_nama" class="form-control"
                                   @change="updateDataPemohon('cust_nama',...arguments)">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_akta">Nomor Akta Pendirian*</label>
                            <input id="step3_perusahaan_akta" name="step3_perusahaan_akta" class="form-control"
                                   @change="updateDataPemohon('cust_nomor_akta_pendirian',...arguments)">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_nama_pemilik">Nama Pemilik*</label>
                            <input id="step3_perusahaan_nama_pemilik" name="step3_perusahaan_nama_pemilik"
                                   class="form-control"
                                   @change="updateDataPemohon('cust_nama_pemilik',...arguments)">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_nama_pimpinan">Nama Pimpinan*</label>
                            <input id="step3_perusahaan_nama_pimpinan" name="step3_perusahaan_nama_pimpinan"
                                   class="form-control"
                                   @change="updateDataPemohon('cust_nama_pimpinan',...arguments)">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_nama_wakil">Nama Wakil Manajemen*</label>
                            <input id="step3_perusahaan_nama_wakil" name="step3_perusahaan_nama_wakil"
                                   class="form-control"
                                   @change="updateDataPemohon('cust_nama_wakil_manajemen',...arguments)">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_telp">Telp (Perusahaan)*</label>
                            <input id="step3_perusahaan_telp" name="step3_perusahaan_telp" class="form-control"
                                   @change="updateDataPemohon('cust_nomor_telp',...arguments)">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_fax">Fax*</label>
                            <input id="step3_perusahaan_fax" name="step3_perusahaan_fax" class="form-control"
                                   @change="updateDataPemohon('cust_nomor_fax',...arguments)">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_cp">Nomer HP (CP)*</label>
                            <input id="step3_perusahaan_cp" name="step3_perusahaan_cp" class="form-control"
                                   @change="updateDataPemohon('cust_nomor_hp',...arguments)">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_badan_hukum">Badan Hukum*</label>
                            <select name="step3_perusahaan_badan_hukum" id="step3_perusahaan_badan_hukum"
                                    class="form-control" @change="updateDataPemohon('badan_hukum_id',...arguments)">
                                <option disabled selected>--Pilih Badan Hukum--</option>
                                @foreach($masterBadanHukum as $bh)
                                    <option value="{{$bh->badan_hukum_id}}">{{$bh->badan_hukum_nama}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_jenis">Jenis Perusahaan*</label>
                            <select name="step3_perusahaan_jenis" id="step3_perusahaan_jenis"
                                    class="form-control"
                                    @change="updateDataPemohon('jenis_perusahaan_id',...arguments)">
                                <option disabled selected>--Pilih Jenis Perusahaan--</option>
                                @foreach($masterJenisPerusahaan as $jp)
                                    <option value="{{$jp->jenis_perusahaan_id}}">{{$jp->jenis_perusahaan_nama}}</option>
                                @endforeach
                            </select>
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
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_negara">Negara*</label>
                            <input id="step3_perusahaan_negara" name="step3_perusahaan_negara" class="form-control"
                                   style="width: 100%">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_provinsi">Provinsi*</label>
                            <input id="step3_perusahaan_provinsi" name="step3_perusahaan_provinsi" class="form-control"
                                   style="width: 100%">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_kabupaten">Kabupaten*</label>
                            <input id="step3_perusahaan_kabupaten" name="step3_perusahaan_kabupaten"
                                   class="form-control" style="width: 100%">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_kecamatan">Kecamatan*</label>
                            <input id="step3_perusahaan_kecamatan" name="step3_perusahaan_kecamatan"
                                   class="form-control" style="width: 100%">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_alamat">Alamat*</label>
                            <textarea id="step3_perusahaan_alamat" name="step3_perusahaan_alamat" cols="30"
                                      class="form-control"
                                      @change="updateDataPemohon('cust_alamat',...arguments)"></textarea>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_luas_tanah">Luas Tanah*</label>
                            <input id="step3_perusahaan_luas_tanah" name="step3_perusahaan_luas_tanah"
                                   class="form-control" @change="updateDataPemohon('cust_luas_tanah',...arguments)">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_luas_bangunan">Luas Bangunan*</label>
                            <input id="step3_perusahaan_luas_bangunan" name="step3_perusahaan_luas_bangunan"
                                   class="form-control" @change="updateDataPemohon('cust_luas_bangunan',...arguments)">
                        </div>
                    </div>
                </li>
            </ol>
        </div>
    </div>

    <div class="col-md-12">
        <h3>Data Operasional</h3>
        <div>
            <ol start="18">
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_jml_shift">Jumlah Shift (dalam sehari)* </label>
                            <input id="step3_perusahaan_jml_shift" name="step3_perusahaan_jml_shift"
                                   class="form-control" type="number" min="0"
                                   @change="updateDataPemohon('cust_shif_kerja',...arguments)">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="step3_perusahaan_jml_bagian">Jumlah Bagian* </label>
                            <input id="step3_perusahaan_jml_bagian" name="step3_perusahaan_jml_bagian"
                                   class="form-control" type="number" min="0"
                                   @change="updateDataPemohon('cust_jumlah_bagian',...arguments)">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Jumlah Karyawan* :</label>
                            {{--<input id="step3_perusahaan_" name="step3_perusahaan_" class="form-control">--}}
                        </div>
                    </div>
                    <ol style="margin-top: -20px">
                        <li>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <!-- Input Group -->
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Manajemen</span>
                                        </div>
                                        <input type="number" min="0" class="form-control" aria-label="Manajemen"
                                               placeholder="Berapa orang ?" name="step3_perusahaan_jml_manajemen"
                                               id="step3_perusahaan_jml_manajemen"
                                               @change="updateDataPemohon('cust_jumlah_manajemen',...arguments)">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fad fa-person"></i>&nbsp; Orang
                                            </span>
                                        </div>
                                    </div>
                                    <!-- /input group -->
                                </div>
                            </div>
                            <!-- /form row -->
                        </li>
                        <li>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <!-- Input Group -->
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Administrasi</span>
                                        </div>
                                        <input type="number" min="0" class="form-control" aria-label="Manajemen"
                                               placeholder="Berapa orang ?" id="step3_perusahaan_jml_administrasi"
                                               name="step3_perusahaan_jml_administrasi"
                                               @change="updateDataPemohon('cust_jumlah_administrasi',...arguments)">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fad fa-person"></i>&nbsp; Orang
                                            </span>
                                        </div>
                                    </div>
                                    <!-- /input group -->
                                </div>
                            </div>
                            <!-- /form row -->
                        </li>
                        <li>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <!-- Input Group -->
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Part Time</span>
                                        </div>
                                        <input type="number" min="0" class="form-control" aria-label="Manajemen"
                                               placeholder="Berapa orang ?" id="step3_perusahaan_jml_part_time"
                                               name="step3_perusahaan_jml_part_time"
                                               @change="updateDataPemohon('cust_jumlah_part_time',...arguments)">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fad fa-person"></i>&nbsp; Orang
                                            </span>
                                        </div>
                                    </div>
                                    <!-- /input group -->
                                </div>
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
                                        <div class="col-md-4">
                                            <!-- Input Group -->
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Shift 1</span>
                                                </div>
                                                <input type="number" min="0" class="form-control" aria-label="Shift 1"
                                                       placeholder="Berapa orang ?" id="step3_perusahaan_jml_shift_1"
                                                       name="step3_perusahaan_jml_shift_1"
                                                       @change="updateDataPemohon('cust_jumlah_shift_1',...arguments)">
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
                                        <div class="col-md-4">
                                            <!-- Input Group -->
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Shift 2</span>
                                                </div>
                                                <input type="number" min="0" class="form-control" aria-label="Shift 2"
                                                       placeholder="Berapa orang ?" id="step3_perusahaan_jml_shift_2"
                                                       name="step3_perusahaan_jml_shift_2"
                                                       @change="updateDataPemohon('cust_jumlah_shift_2',...arguments)">
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
                                        <div class="col-md-4">
                                            <!-- Input Group -->
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Shift 3</span>
                                                </div>
                                                <input type="number" min="0" class="form-control" aria-label="Shift 3"
                                                       placeholder="Berapa orang ?" id="step3_perusahaan_jml_shift_3"
                                                       name="step3_perusahaan_jml_shift_3"
                                                       @change="updateDataPemohon('cust_jumlah_shift_3',...arguments)">
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
                            </ul>
                        </li>
                        <li>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <!-- Input Group -->
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Non Permanen</span>
                                        </div>
                                        <input type="number" min="0" class="form-control" aria-label="Non Permanen"
                                               placeholder="Berapa orang ?" id="step3_perusahaan_jml_non_permanen"
                                               name="step3_perusahaan_jml_non_permanen"
                                               @change="updateDataPemohon('cust_jumlah_non_permanen',...arguments)">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fad fa-person"></i>&nbsp; Orang
                                            </span>
                                        </div>
                                    </div>
                                    <!-- /input group -->
                                </div>
                            </div>
                            <!-- /form row -->
                        </li>
                    </ol>
                </li>
            </ol>

        </div>
    </div>
</div>

@push('javascript')
    <script>
        $(document).ready(function () {
            window.vueStepThree = new Vue({
                el: "#vueStepThree",
                data: {},
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
                        if ($.trim($("#step3_perusahaan_provinsi").combogrid('getValue')) === "") throw "Pilih provinsi";
                        if ($.trim($("#step3_perusahaan_kabupaten").combogrid('getValue')) === "") throw "Pilih kabupaten";
                        if ($.trim($("#step3_perusahaan_kecamatan").combogrid('getValue')) === "") throw "Pilih kecamatan";
                    },
                    setComboNegara(search) {
                        let self = this;
                        let url = `{{ url("$url/ajax?action=combogrid_negara") }}`
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
                                console.log(index)
                                console.log(row)
                                self.updateDataPemohonByValue("negara_id", row.negara_id)
                            },
                        });
                    },
                    setComboProvinsi(search) {
                        let self = this;
                        let url = `{{ url("$url/ajax?action=combogrid_provinsi") }}`
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
                                self.updateDataPemohonByValue("kec_id", '-')
                                self.updateDataPemohonByValue("kab_id", '-')
                                $("#step3_perusahaan_kabupaten").combogrid('setValue', null);
                                $("#step3_perusahaan_kecamatan").combogrid('setValue', null);
                                self.setComboKabupaten(row.prov_id)
                            },
                        });
                    },
                    setComboKabupaten(provId, search) {
                        let self = this;
                        let url = `{{ url("$url/ajax?action=combogrid_kabupaten") }}&prov_id=${provId}`
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
                                self.updateDataPemohonByValue("kec_id", '-')
                                $("#step3_perusahaan_kecamatan").combogrid('setValue', null);
                                self.setComboKecamatan(row.kab_id);
                            },
                        });
                    },
                    setComboKecamatan(kabId, search) {
                        let self = this;
                        let url = `{{ url("$url/ajax?action=combogrid_kecamatan") }}&kab_id=${kabId}`
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
                    getDataPemohon() {
                        $.get(`{{url("$url/ajax?action=data_pemohon")}}`)
                            .then(response => {
                                let res = response.results;
                                $("#step3_perusahaan_nama").val(res.cust_nama);
                                $("#step3_perusahaan_akta").val(res.cust_nomor_akta_pendirian);
                                $("#step3_perusahaan_nama_pemilik").val(res.cust_nama_pemilik);
                                $("#step3_perusahaan_nama_pimpinan").val(res.cust_nama_pimpinan);
                                $("#step3_perusahaan_nama_wakil").val(res.cust_nama_wakil_manajemen);
                                $("#step3_perusahaan_telp").val(res.cust_nomor_telp);
                                $("#step3_perusahaan_fax").val(res.cust_nomor_fax);
                                $("#step3_perusahaan_cp").val(res.cust_nomor_hp);
                                $("#step3_perusahaan_badan_hukum").val(res.badan_hukum_id);
                                $("#step3_perusahaan_jenis").val(res.jenis_perusahaan_id);

                                $("#step3_perusahaan_alamat").val(res.cust_alamat);
                                $("#step3_perusahaan_luas_tanah").val(res.cust_luas_tanah);
                                $("#step3_perusahaan_luas_bangunan").val(res.cust_luas_bangunan);

                                $("#step3_perusahaan_jml_shift").val(res.cust_shif_kerja);
                                $("#step3_perusahaan_jml_bagian").val(res.cust_jumlah_bagian);
                                $("#step3_perusahaan_jml_manajemen").val(res.cust_jumlah_manajemen);
                                $("#step3_perusahaan_jml_administrasi").val(res.cust_jumlah_administrasi);
                                $("#step3_perusahaan_jml_part_time").val(res.cust_jumlah_part_time);
                                $("#step3_perusahaan_jml_shift_1").val(res.cust_jumlah_shift_1);
                                $("#step3_perusahaan_jml_shift_2").val(res.cust_jumlah_shift_2);
                                $("#step3_perusahaan_jml_shift_3").val(res.cust_jumlah_shift_3);
                                $("#step3_perusahaan_jml_non_permanen").val(res.cust_jumlah_non_permanen);


                                // Enable Combogrid Negara
                                this.setComboNegara(res.negara_id);
                                this.setComboProvinsi(res.prov_id);
                                this.setComboKabupaten(res.prov_id, res.kab_id);
                                this.setComboKecamatan(res.kab_id, res.kec_id);

                                // Set Provinsi
                                $("#step3_perusahaan_negara").combogrid('setValue', res.negara_id);
                                $("#step3_perusahaan_provinsi").combogrid('setValue', res.prov_id);
                                $("#step3_perusahaan_kabupaten").combogrid('setValue', res.kab_id);
                                $("#step3_perusahaan_kecamatan").combogrid('setValue', res.kec_id);
                            })
                    },
                    updateDataPemohonByValue(parameter, value) {
                        $.post(`{{url("$url/ajax?action=update_data_pemohon")}}`, {parameter, value})
                    },
                    updateDataPemohon(params, event) {
                        $.post(`{{url("$url/ajax?action=update_data_pemohon")}}`, {
                            parameter: params,
                            value: event.target.value
                        })
                    },
                }
            });
        });
    </script>
@endpush
