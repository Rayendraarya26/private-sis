<div class="row" id="vueStepTwo">
    <div class="col-md-4"></div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="step2_jenis_sertifikasi">Jenis Sertifikasi</label>
            <input id="step2_jenis_sertifikasi" name="step2_jenis_sertifikasi" class="form-control" style="width: 100%">
        </div>
    </div>
    <div class="col-md-4"></div>

    <div class="col-md-12" v-if="jenis_sertifikasi_id != null">
        <h3>Kelengkapan Dokumen</h3>
        <table class="table">
            <thead>
            <tr>
                <th>No</th>
                <th>Dokumen</th>
                <th>Upload</th>
                <th>Dokumen Anda</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(dds, idx) in data_dokumen_sertifikasi">
                <td>@{{ idx + 1 }}</td>
                <td>
                    <i class="fad fa-check-circle" style="color: green" v-if="dds.my_document !== null"
                       title="Dokumen sudah di unggah"></i>
                    <i class="fad fa-warning" style="color: red" v-else title="Dokumen belum di unggah"></i>

                    @{{ dds.dt_name }}
                    <span v-if="dds.dt_sample"><a :href="dds.dt_sample">Download Sample</a></span>
                </td>
                <td>
                    <input type="file" :name="'dokumen'+dds.dt_id" :id="'dokumen'+dds.dt_id"
                           @change="uploadDokumen(dds.dt_id)" accept="application/pdf">
                </td>
                <td>
                    <a :href="dds.my_document" v-if="dds.my_document !== null" target="_blank">Download</a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="col-md-12" v-if="jenis_sertifikasi_id != null && jenis_sertifikasi_is_product == 'ya'"
         style="padding-bottom: 20px">
        <div class="row">
            <div class="col-md-4">
                <h3>Data Komoditas</h3>
                <div class="form-group">
                    <label for="step2_komoditi_datas">Komoditi</label><br>
                    <input id="step2_komoditi_datas" name="step2_komoditi_datas" class="form-control"
                           style="width: 100%">
                </div>
                <div class="form-group">
                    <label for="step2_komoditi_sni">No SNI</label>
                    <input id="step2_komoditi_sni" name="step2_komoditi_sni" class="form-control">
                </div>
                <div class="form-group">
                    <label for="step2_komoditi_merk">Merk</label>
                    <input id="step2_komoditi_merk" name="step2_komoditi_merk" class="form-control">
                </div>
                <div class="form-group">
                    <label for="step2_komoditi_tipe">Tipe</label>
                    <input id="step2_komoditi_tipe" name="step2_komoditi_tipe" class="form-control">
                </div>
                <div class="form-group">
                    <label for="step2_komoditi_ukuran">Ukuran</label>
                    <input id="step2_komoditi_ukuran" name="step2_komoditi_ukuran" class="form-control">
                </div>
                <template v-if="jenis_komoditas_form_type == 'add'">
                    <button class="btn btn-success" @click="addKomoditas">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </template>
                <template v-else>
                    <button class="btn btn-primary" @click="updateKomoditi">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <button class="btn btn-danger" @click="calcelUpdateKomoditi">
                        <i class="fas fa-close"></i> Batal
                    </button>
                </template>

            </div>
            <div class="col-md-8">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Komoditi</th>
                        <th>No SNI</th>
                        <th>Merk</th>
                        <th>Tipe</th>
                        <th>Ukuran</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(kom, idx) in komoditas">
                        <tr>
                            <td>@{{ kom.komoditi_nama }}</td>
                            <td>@{{ kom.sni }}</td>
                            <td>@{{ kom.merk }}</td>
                            <td>@{{ kom.tipe }}</td>
                            <td>@{{ kom.ukuran }}</td>
                            <td>
                                <button class="btn btn-xs btn-danger" @click="deleteKomoditi(idx)">
                                    <i class="fad fa-trash"></i> Hapus
                                </button>
                                <button class="btn btn-xs btn-warning" @click="editKomoditi(idx)">
                                    <i class="fad fa-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{--    <button class="btn btn-primary" @click="doTest">--}}
    {{--        Tes--}}
    {{--    </button>--}}
</div>

@push('javascript')
    <script>
        $(document).ready(function () {
            window.vueStepTwo = new Vue({
                el: "#vueStepTwo",
                data: {
                    data_dokumen_sertifikasi: [],

                    jenis_sertifikasi_id: null, // upload to server
                    jenis_sertifikasi_is_product: 'tidak',
                    jenis_sertifikasi_text: "--Pilih Jenis Sertifikasi--",

                    jenis_komoditas_id: null,
                    jenis_komoditas_text: "-- Pilih Komoditas --",
                    jenis_komoditas_form_type: "add",
                    jenis_komoditas_form_edited_idx: null,

                    dokumen_upload: [], // upload to server
                    komoditas: [], // upload to server
                },
                mounted() {
                    setTimeout(() => {
                        const currentStep = $('#smartwizard').smartWizard("getStepIndex");
                        console.log(currentStep)
                        if (currentStep === 1) {
                            this.start();
                        }
                    }, 400)
                },
                methods: {
                    doTest() {
                        console.log()
                    },
                    start() {
                        setTimeout(() => this.setComboDataSertifikasi(), 500)
                    },
                    validate() {

                    },
                    uploadDokumen(id) {
                        self = this;
                        const el = document.querySelector("#dokumen" + id);
                        const doc = el.files[0]
                        const dt_upload = {"id": id, "dokumen": doc};
                        if (dt_upload.dokumen.type !== "application/pdf") {
                            swalWithBootstrapButtons({
                                title: `Validasi`,
                                text: "Dokumen harus bertipe PDF",
                                type: 'warning'
                            })
                            $("#dokumen" + id).val("")
                        }

                        let formData = new FormData();
                        formData.append("sert_dok_id", id)
                        formData.append("file", doc)
                        $.ajax({
                            url: `{{url("$url/ajax?action=upload_document")}}`,
                            type: 'post',
                            processData: false,
                            contentType: false,
                            data: formData,
                            success: function (res) {
                                toastCenter({
                                    type: 'success',
                                    title: res.message
                                })
                                self.getDokumenSertifikasi()
                            },
                            error: function (err) {
                                console.log(err)
                                swalWithBootstrapButtons({
                                    title: `Error`,
                                    text: err.responseJSON.message,
                                    type: 'warning'
                                })
                            }
                        });
                    },
                    resetUploadDokumen() {
                        if (this.data_dokumen_sertifikasi.length > 0) {
                            this.data_dokumen_sertifikasi.map(e => {
                                $("#dokumen" + e.dt_id).val("")
                            });
                            this.dokumen_upload = [];
                        }
                    },
                    resetFormKomoditas() {
                        $("#step2_komoditi_merk").val("");
                        $("#step2_komoditi_sni").val("");
                        $("#step2_komoditi_tipe").val("");
                        $("#step2_komoditi_ukuran").val("");
                        this.jenis_komoditas_form_type = 'add';
                        this.jenis_komoditas_form_edited_idx = null;
                    },
                    validateKomoditas() {
                        let sni = $.trim($("#step2_komoditi_sni").val());
                        let merk = $.trim($("#step2_komoditi_merk").val());
                        let tipe = $.trim($("#step2_komoditi_tipe").val());
                        let ukuran = $.trim($("#step2_komoditi_ukuran").val());
                        if (this.jenis_komoditas_id === null) throw "Pilih Komoditas"
                        if (merk === "") throw "Tuliskan No SNI";
                        if (sni === "") throw "Tuliskan Merk";
                        if (tipe === "") throw "Tuliskan Tipe Komoditas";
                        if (ukuran === "") throw "Tuliskan Ukuran";

                    },
                    addKomoditas() {
                        try {
                            this.validateKomoditas()
                            this.komoditas.push({
                                "komoditi_id": this.jenis_komoditas_id,
                                "komoditi_nama": this.jenis_komoditas_text,
                                "sni": $.trim($("#step2_komoditi_sni").val()),
                                "merk": $.trim($("#step2_komoditi_merk").val()),
                                "tipe": $.trim($("#step2_komoditi_tipe").val()),
                                "ukuran": $.trim($("#step2_komoditi_ukuran").val()),
                            })
                            this.resetFormKomoditas();
                        } catch (message) {
                            swalWithBootstrapButtons({
                                title: `Validasi Komoditas`,
                                text: message,
                                type: 'warning',
                            })
                        }
                    },
                    deleteKomoditi(idx) {
                        swalWithBootstrapButtons({
                            title: `Hapus Komoditi ?`,
                            text: "Anda yakin menghapus komoditi ?",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.value) {
                                this.komoditas.splice(idx, 1);
                            }
                        });
                    },
                    editKomoditi(idx) {
                        let dataKomoditi = this.komoditas[idx];
                        console.log(dataKomoditi);
                        $("#step2_komoditi_merk").val(dataKomoditi.merk);
                        $("#step2_komoditi_sni").val(dataKomoditi.sni);
                        $("#step2_komoditi_tipe").val(dataKomoditi.tipe);
                        $("#step2_komoditi_ukuran").val(dataKomoditi.ukuran);
                        this.setComboDataKomoditas(dataKomoditi.komoditi_nama);
                        $('#step2_komoditi_datas').combogrid('setValue', dataKomoditi.komoditi_nama);

                        this.jenis_komoditas_id = dataKomoditi.komoditi_id;
                        this.jenis_komoditas_text = dataKomoditi.komoditi_nama;
                        this.jenis_komoditas_form_type = "update";
                        this.jenis_komoditas_form_edited_idx = idx;
                    },
                    updateKomoditi() {
                        try {
                            this.validateKomoditas()
                            this.komoditas[this.jenis_komoditas_form_edited_idx] = {
                                "komoditi_id": this.jenis_komoditas_id,
                                "komoditi_nama": this.jenis_komoditas_text,
                                "sni": $.trim($("#step2_komoditi_sni").val()),
                                "merk": $.trim($("#step2_komoditi_merk").val()),
                                "tipe": $.trim($("#step2_komoditi_tipe").val()),
                                "ukuran": $.trim($("#step2_komoditi_ukuran").val()),
                            };
                            this.resetFormKomoditas();

                        } catch (message) {
                            swalWithBootstrapButtons({
                                title: `Validasi Komoditas`,
                                text: message,
                                type: 'warning',
                            })
                        }
                    },
                    calcelUpdateKomoditi() {
                        this.jenis_komoditas_form_type = "add";
                        this.resetFormKomoditas();
                    },
                    getDokumenSertifikasi() {
                        $.get(`{{url("$url/ajax?action=dokumen_sertifikat")}}&sert_id=${this.jenis_sertifikasi_id}`)
                            .then(response => {
                                this.data_dokumen_sertifikasi = response.results
                                $(".tab-content").height("100%");
                            })
                            .fail(err => {
                                console.log(err)
                            })
                    },
                    setComboDataKomoditas(search) {
                        let self = this;
                        let url = `{{ url("$url/ajax?action=combogrid_komoditas") }}`
                        if (search != null) {
                            url += '&q=' + search
                        }

                        $('#step2_komoditi_datas').combogrid({
                            pageSize: '50',
                            panelWidth: 650,
                            pagination: true,
                            idField: 'komodt_id',
                            nowrap: false,
                            textField: 'komodt_nama',
                            editable: true,
                            url: url,
                            method: 'get',
                            mode: 'remote',
                            value: self.jenis_komoditas_text,
                            multiSort: true,
                            fitColumns: true,
                            required: true,
                            columns: [[
                                {field: 'komodt_id', hidden: true},
                                {field: 'komodt_nama', title: 'Nama Komoditas', width: 250, sortable: true,},
                                // {field: 'sert_is_product', title: 'Produk?', width: 100, sortable: true,},
                            ]],
                            onSelect: function (index, row) {
                                self.jenis_komoditas_id = row.komodt_id;
                                self.jenis_komoditas_text = row.komodt_nama;
                            },
                        });
                    },
                    setComboDataSertifikasi() {
                        let self = this;
                        let url = `{{ url("$url/ajax?action=combogrid_sertifikasi") }}`
                        if (self.jenis_sertifikasi_id != null) {
                            url += "&q=" + this.jenis_sertifikasi_text;
                        }

                        $('#step2_jenis_sertifikasi').combogrid({
                            pageSize: '50',
                            panelWidth: 650,
                            pagination: true,
                            idField: 'sert_id',
                            nowrap: false,
                            textField: 'sert_nama',
                            editable: true,
                            url: url,
                            method: 'get',
                            mode: 'remote',
                            value: self.jenis_sertifikasi_text,
                            multiSort: true,
                            fitColumns: true,
                            required: true,
                            columns: [[
                                {field: 'sert_id', hidden: true},
                                {field: 'sert_nama', title: 'Jenis Sertifikasi', width: 250, sortable: true,},
                                // {field: 'sert_is_product', title: 'Produk?', width: 100, sortable: true,},
                            ]],
                            onSelect: function (index, row) {
                                self.jenis_sertifikasi_id = row.sert_id;
                                self.jenis_sertifikasi_text = row.sert_nama;
                                self.jenis_sertifikasi_is_product = row.sert_is_product;
                                self.resetUploadDokumen();
                                self.getDokumenSertifikasi();
                                if (self.jenis_sertifikasi_is_product === "ya") {
                                    setTimeout(() => self.setComboDataKomoditas(), 500)
                                }
                            },
                        });

                        if (self.jenis_sertifikasi_id != null) {
                            $('#step2_jenis_sertifikasi').combogrid('setValue', self.jenis_sertifikasi_id)
                        }
                    }
                }
            })
        })
    </script>
@endpush
