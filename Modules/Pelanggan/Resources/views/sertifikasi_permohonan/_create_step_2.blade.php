@push('css')
    <style>
        .komoditi-button {
            padding-top: 30px;
        }

        @media screen and (max-width: 450px) {
            .komoditi-button {
                padding-top: 0;
            }
        }
    </style>
@endpush

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
        <div class="table-responsive">
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
                        <i class="fad fa-check-circle" style="color: green" v-if="dds.my_document != null"
                           title="Dokumen sudah di unggah"></i>
                        <i class="fad fa-warning" style="color: red" v-else title="Dokumen belum di unggah"></i>

                        @{{ dds.dt_name }}
                        <x-linked-icon></x-linked-icon>
                        <span v-if="dds.dt_sample"><a :href="dds.dt_sample">Download Sample</a></span>
                    </td>
                    <td>
                        <input type="file" :name="'dokumen'+dds.dt_id" :id="'dokumen'+dds.dt_id"
                               @change="uploadDokumen(dds.dt_id)" accept="application/pdf">
                    </td>
                    <td>
                        <a :href="dds.my_document" v-if="dds.my_document != null" target="_blank">Download</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-12" v-if="jenis_sertifikasi_id != null && jenis_sertifikasi_is_product == 'ya'"
         style="padding-bottom: 20px">
        <div class="row">
            <div class="col-md-12">
                <h3>Data Komoditas <small aria-label="Setiap perubahan akan tersimpan di storage browser"
                                          class="custom-cooltipz"
                                          data-cooltipz-size="large"
                                          data-cooltipz-dir="right"><i class="fal fa-database"></i></small></h3>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="step2_komoditi_datas">Komoditi</label><br>
                            <input id="step2_komoditi_datas" name="step2_komoditi_datas" class="form-control"
                                   style="width: 100%">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="step2_komoditi_sni">No SNI</label>
                            <input id="step2_komoditi_sni" name="step2_komoditi_sni" class="form-control"
                                   @keyup.enter="addOrUpdateKomoditas">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="step2_komoditi_merk">Merk</label>
                            <input id="step2_komoditi_merk" name="step2_komoditi_merk" class="form-control"
                                   @keyup.enter="addOrUpdateKomoditas">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="step2_komoditi_tipe">Tipe</label>
                            <input id="step2_komoditi_tipe" name="step2_komoditi_tipe" class="form-control"
                                   @keyup.enter="addOrUpdateKomoditas">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="step2_komoditi_ukuran">Ukuran</label>
                            <input id="step2_komoditi_ukuran" name="step2_komoditi_ukuran" class="form-control"
                                   @keyup.enter="addOrUpdateKomoditas">
                        </div>
                    </div>
                    <div class="col-md-2 komoditi-button">
                        <template v-if="jenis_komoditas_form_type == 'add'">
                            <button class="btn btn-success btn-xs" @click="addKomoditas">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        </template>
                        <template v-else>
                            <button class="btn btn-primary btn-xs" @click="updateKomoditi">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <button class="btn btn-danger btn-xs" @click="calcelUpdateKomoditi">
                                <i class="fas fa-close"></i> Batal
                            </button>
                        </template>
                    </div>
                </div>

            </div>
            <div class="col-md-12">
                <div class="table-responsive">
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
                                    <button class="btn btn-xs btn-danger" @click="deleteKomoditi(kom.id)">
                                        <i class="fad fa-trash"></i> Hapus
                                    </button>
                                    <button class="btn btn-xs btn-warning" @click="editKomoditi(kom.id)">
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
    </div>
</div>

@push('javascript')
    <script>
        $(document).ready(function () {
            window.vueStepTwo = new Vue({
                el: "#vueStepTwo",
                data: {
                    data_dokumen_sertifikasi: [],

                    jenis_sertifikasi_data: null,
                    jenis_sertifikasi_id: null, // upload to server
                    jenis_sertifikasi_is_product: 'tidak',
                    jenis_sertifikasi_text: "--Pilih Jenis Sertifikasi--",

                    jenis_komoditas_id: null,
                    jenis_komoditas_text: "-- Pilih Komoditas --",
                    jenis_komoditas_form_type: "add",
                    jenis_komoditas_form_edited_id: null,

                    dokumen_upload: [], // upload to server
                    komoditas: [], // upload to server
                },
                mounted() {
                    setTimeout(() => {
                        const currentStep = $('#smartwizard').smartWizard("getStepIndex");
                        if (currentStep === 1) {
                            this.start();
                        }
                    }, 400)
                },
                methods: {
                    start() {
                        setTimeout(async () => {
                            await this.setComboDataSertifikasi()

                            // Load to set initial Index DB
                            let currentData = await idb.pelanggan_permohonan
                                .where({name: "jenis_sertifikasi"})
                                .first()

                            console.log(currentData)

                            if (currentData != null) {
                                $('#step2_jenis_sertifikasi').combogrid('setValue', currentData.value.sert_id)
                                this.comboDataSertifikasiOnSelect(currentData.value)
                            }
                        }, 500)
                    },
                    validate() {
                        if (this.jenis_sertifikasi_id == null) throw "Pilih Jenis Sertifikasi"

                        // validate kelengkapan dokumen
                        this.data_dokumen_sertifikasi.map(e => {
                            if (e.my_document == null) throw `Upload ${e.dt_name}`
                        })

                        // validate komoditas (jika diperlukan)
                        if (this.jenis_sertifikasi_is_product === "ya") {
                            console.log(this.komoditas.length)
                            if (this.komoditas.length === 0) throw "Mohon isikan data komoditas"
                        }
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
                            error: function (xhr) {
                                if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
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
                        this.jenis_komoditas_form_edited_id = null;
                    },
                    validateKomoditas() {
                        let sni = $.trim($("#step2_komoditi_sni").val());
                        let merk = $.trim($("#step2_komoditi_merk").val());
                        let tipe = $.trim($("#step2_komoditi_tipe").val());
                        let ukuran = $.trim($("#step2_komoditi_ukuran").val());
                        if (this.jenis_komoditas_id == null) throw "Pilih Komoditas"
                        if (sni === "") throw "Tuliskan No SNI";
                        if (merk === "") throw "Tuliskan Merk";
                        if (tipe === "") throw "Tuliskan Tipe Komoditas";
                        if (ukuran === "") throw "Tuliskan Ukuran";
                    },
                    addOrUpdateKomoditas() {
                        if (this.jenis_komoditas_form_type === "add") this.addKomoditas()
                        else this.updateKomoditi()
                    },
                    async addKomoditas() {
                        try {
                            this.validateKomoditas()

                            let newKomoditas = {
                                "komoditi_id": this.jenis_komoditas_id,
                                "komoditi_nama": $.trim(this.jenis_komoditas_text),
                                "sni": $.trim($("#step2_komoditi_sni").val()),
                                "merk": $.trim($("#step2_komoditi_merk").val()),
                                "tipe": $.trim($("#step2_komoditi_tipe").val()),
                                "ukuran": $.trim($("#step2_komoditi_ukuran").val()),
                            };

                            // this.komoditas.push(newKomoditas)
                            await idb.pelanggan_permohonan_komoditas.put(newKomoditas);
                            this.komoditas = await window.idb.pelanggan_permohonan_komoditas.toArray()
                            this.resetFormKomoditas();
                        } catch (message) {
                            swalWithBootstrapButtons({
                                title: `Validasi Komoditas`,
                                text: message,
                                type: 'warning',
                            })
                        }
                    },
                    async deleteKomoditi(id) {
                        let selectedKomoditas = await window.idb.pelanggan_permohonan_komoditas.get(id);
                        swalWithBootstrapButtons({
                            title: `Hapus Komoditi ?`,
                            text: `Anda yakin menghapus komoditi ${selectedKomoditas.komoditi_nama} ?`,
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(async (result) => {
                            if (result.value) {
                                await window.idb.pelanggan_permohonan_komoditas.where('id').equals(id).delete();
                                this.komoditas = await window.idb.pelanggan_permohonan_komoditas.toArray()
                            }
                        });
                    },
                    async editKomoditi(id) {
                        let selectedKomoditas = await window.idb.pelanggan_permohonan_komoditas.get(id);
                        $("#step2_komoditi_merk").val(selectedKomoditas.merk);
                        $("#step2_komoditi_sni").val(selectedKomoditas.sni);
                        $("#step2_komoditi_tipe").val(selectedKomoditas.tipe);
                        $("#step2_komoditi_ukuran").val(selectedKomoditas.ukuran);
                        this.setComboDataKomoditas(selectedKomoditas.komoditi_nama);
                        $('#step2_komoditi_datas').combogrid('setValue', selectedKomoditas.komoditi_nama);

                        this.jenis_komoditas_id = selectedKomoditas.komoditi_id;
                        this.jenis_komoditas_text = selectedKomoditas.komoditi_nama;
                        this.jenis_komoditas_form_type = "update";
                        this.jenis_komoditas_form_edited_id = id;
                    },
                    async updateKomoditi() {
                        try {
                            this.validateKomoditas()
                            await window.idb.pelanggan_permohonan_komoditas.update(this.jenis_komoditas_form_edited_id, {
                                "komoditi_id": this.jenis_komoditas_id,
                                "komoditi_nama": this.jenis_komoditas_text,
                                "sni": $.trim($("#step2_komoditi_sni").val()),
                                "merk": $.trim($("#step2_komoditi_merk").val()),
                                "tipe": $.trim($("#step2_komoditi_tipe").val()),
                                "ukuran": $.trim($("#step2_komoditi_ukuran").val()),
                            });
                            this.komoditas = await window.idb.pelanggan_permohonan_komoditas.toArray()
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
                                setTimeout(() => $(".tab-content").height("100%"), 500)
                            })
                            .fail((xhr) => {
                                if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                            });
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
                            panelWidth: 400,
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
                            onLoadSuccess: async function () {

                            },
                            onSelect: async function (index, row) {
                                self.comboDataSertifikasiOnSelect(row)

                                // Insert to Index DB
                                const currentaData = await idb.pelanggan_permohonan.where({name: "jenis_sertifikasi"}).first();
                                let dbData = {name: "jenis_sertifikasi", value: row}
                                if (currentaData == null) {
                                    await idb.pelanggan_permohonan.put(dbData);
                                } else {
                                    await idb.pelanggan_permohonan.update(currentaData.id, dbData);
                                }
                            },
                        });

                        if (self.jenis_sertifikasi_id != null) {
                            $('#step2_jenis_sertifikasi').combogrid('setValue', self.jenis_sertifikasi_id)
                        }
                    },
                    comboDataSertifikasiOnSelect(row) {
                        this.jenis_sertifikasi_data = row
                        this.jenis_sertifikasi_id = row.sert_id;
                        this.jenis_sertifikasi_text = row.sert_nama;
                        this.jenis_sertifikasi_is_product = row.sert_is_product;
                        this.resetUploadDokumen();
                        this.getDokumenSertifikasi();

                        if (this.jenis_sertifikasi_is_product === "ya") {
                            setTimeout(async () => {
                                this.setComboDataKomoditas()
                                this.komoditas = await window.idb.pelanggan_permohonan_komoditas.toArray()
                            }, 500)
                        }
                    }
                }
            })
        })
    </script>
@endpush
