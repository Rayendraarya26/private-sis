@push('css')
    <style>
        .komoditi-button {
            padding-top: 15px;
        }

        @media screen and (max-width: 450px) {
            .komoditi-button {
                padding-top: 0;
            }
        }
    </style>
@endpush

<div class="row" id="vueStepTwo">
    <template v-for="(dt, idx) in data_sertifikat">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <br>
            <div style="text-align: center; justify-content: center">
                <h3>Pengajuan @{{ idx + 1 }}</h3>
            </div>
            <div class="form-group">
                <label :for="'step2_jenis_sertifikasi' + idx">
                    Jenis Sertifikasi
                    <br>
                </label>
                <input :id="'step2_jenis_sertifikasi' + idx"
                       :name="'step2_jenis_sertifikasi' + idx"
                       aria-label="Jenis Sertifikasi"
                       class="form-control"
                       style="width: 100%">
                <div style="text-align: center">
                    <small v-if="window.vueStepOne.data_pengajuan[idx].jenis_pengajuan == 'lama'">
                        <i>Re-Sertifikasi @{{ window.vueStepOne.data_pengajuan[idx].sertifikat_lama_text }}</i>
                    </small>
                    <small v-else><i>Sertifikasi baru</i></small>
                </div>
            </div>
        </div>
        <div class="col-md-4"></div>

        <div class="col-md-12" v-if="data_sertifikat[idx].jenis_sertifikasi_id != null">
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
                    <tr v-for="(dds, indexDok) in data_sertifikat[idx].dokumens">
                        <td>@{{ indexDok + 1 }}</td>
                        <td>
                            <i class="fad fa-check-circle" style="color: green" v-if="dds.my_document != null"
                               title="Dokumen sudah di unggah"></i>
                            <i class="fad fa-warning" style="color: red" v-else title="Dokumen belum di unggah"></i>

                            @{{ dds.dt_name }}
                            <x-linked-icon></x-linked-icon>
                            <span v-if="dds.dt_sample"><a :href="dds.dt_sample">Download Sample</a></span>
                        </td>
                        <td>
                            <input type="file" :name="'dokumen' + idx + dds.dt_id" :id="'dokumen' + idx + dds.dt_id"
                                   @change="uploadDokumen(idx, dds.dt_id)" accept="application/pdf">
                        </td>
                        <td>
                            <a :href="dds.my_document" v-if="dds.my_document != null" target="_blank">Download</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-12" v-if="data_sertifikat[idx].jenis_sertifikasi_id != null"
             style="padding-bottom: 20px">
            <div class="row">
                <div class="col-md-12">
                    <h3>Data Komoditas <small aria-label="Setiap perubahan akan tersimpan di storage browser"
                                              class="custom-cooltipz"
                                              data-cooltipz-size="large"
                                              data-cooltipz-dir="right"><i class="fal fa-database"></i></small></h3>
                    <div class="row" v-if="window.vueStepOne.data_pengajuan[idx].jenis_pengajuan == 'baru'">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label :for="'step2_komoditi_datas' + idx">Komoditi</label><br>
                                        <input :id="'step2_komoditi_datas' + idx" :name="'step2_komoditi_datas' + idx"
                                               aria-label="Komoditi"
                                               class="form-control"
                                               style="width: 100%">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label :for="'step2_komoditi_sni' + idx">No SNI</label>
                                        <input :id="'step2_komoditi_sni' + idx" :name="'step2_komoditi_sni' + idx"
                                               class="form-control"
                                               @keyup.enter="addOrUpdateKomoditas(idx)" readonly aria-label="No SNI">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label :for="'step2_komoditi_merk' + idx">Merk</label>
                                        <input :id="'step2_komoditi_merk' + idx" :name="'step2_komoditi_merk' + idx"
                                               class="form-control" aria-label="Merk"
                                               @keyup.enter="addOrUpdateKomoditas(idx)">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label :for="'step2_komoditi_tipe' + idx">Tipe</label>
                                        <input :id="'step2_komoditi_tipe' + idx" :name="'step2_komoditi_tipe' + idx"
                                               class="form-control" aria-label="Tipe"
                                               @keyup.enter="addOrUpdateKomoditas(idx)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label :for="'step2_komoditi_ukuran' + idx">Ukuran</label>
                                        <input :id="'step2_komoditi_ukuran' + idx" :name="'step2_komoditi_ukuran' + idx"
                                               class="form-control" aria-label="Ukuran"
                                               @keyup.enter="addOrUpdateKomoditas(idx)">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label :for="'step2_produksi_tahunan' + idx">Jumlah Produksi/Tahun</label>
                                        <input :id="'step2_produksi_tahunan' + idx"
                                               :name="'step2_produksi_tahunan' + idx"
                                               class="form-control" type="number" minlength="0"
                                               aria-label="Jumlah Produksi/Tahun"
                                               @keyup.enter="addOrUpdateKomoditas(idx)">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label :for="'step2_satuan_produksi' + idx">Satuan Produksi</label>
                                        <input :id="'step2_satuan_produksi' + idx" :name="'step2_satuan_produksi' + idx"
                                               class="form-control"
                                               @keyup.enter="addOrUpdateKomoditas(idx)">
                                    </div>
                                </div>
                                <div class="col-md-12 komoditi-button">
                                    <template v-if="data_sertifikat[idx].jenis_komoditas_form_type == 'add'">
                                        <button class="btn btn-success" @click="addKomoditas(idx)">
                                            <i class="fas fa-plus"></i> Tambah
                                        </button>
                                    </template>
                                    <template v-else>
                                        <button class="btn btn-primary" @click="updateKomoditi(idx)">
                                            <i class="fas fa-save"></i> Simpan
                                        </button>
                                        <button class="btn btn-danger" @click="calcelUpdateKomoditi(idx)">
                                            <i class="fas fa-close"></i> Batal
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
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
                                <th>Produksi Tahunan</th>
                                <th>Satuan Produksi</th>
                                <th v-if="window.vueStepOne.data_pengajuan[idx].jenis_pengajuan == 'baru'">Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-for="kom in data_sertifikat[idx].komoditas">
                                <tr>
                                    <td>@{{ kom.komoditi_nama }}</td>
                                    <td>@{{ kom.sni }}</td>
                                    <td>@{{ kom.merk }}</td>
                                    <td>@{{ kom.tipe }}</td>
                                    <td>@{{ kom.ukuran }}</td>
                                    <td>@{{ kom.produksi_tahunan }}</td>
                                    <td>@{{ kom.satuan_produksi }}</td>
                                    <td v-if="window.vueStepOne.data_pengajuan[idx].jenis_pengajuan == 'baru'">
                                        <button class="btn btn-xs btn-warning" @click="editKomoditi(idx, kom.id)">
                                            <i class="fad fa-pencil"></i> Edit
                                        </button>
                                        <button class="btn btn-xs btn-danger" @click="deleteKomoditi(idx, kom.id)"
                                                :disabled="data_sertifikat[idx].jenis_komoditas_form_type == 'update'">
                                            <i class="fad fa-trash"></i> Hapus
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
        <hr style="border: 1px dashed grey;border-radius: 5px; width: 100%">
    </template>
</div>

@push('javascript')
    <script>
        $(document).ready(function () {
            window.vueStepTwo = new Vue({
                el: "#vueStepTwo",
                data: {
                    data_sertifikat: [],
                },
                mounted() {
                    // setTimeout(async () => {
                    //     const currentStep = $('#smartwizard').smartWizard("getStepIndex");
                    //     if (currentStep === 1) {
                    //         this.total_pengajuan = window.vueStepOne.sertifikat_lama_data.length
                    //         this.start();
                    //     }
                    // }, 400)
                },
                methods: {
                    start() {
                        this.data_sertifikat = [];
                        setTimeout(async () => {
                            // RENDER DYNAMIC PENGAJUAN
                            window.vueStepOne.data_pengajuan.map(async (pengajuan, idx) => {
                                await this.pengajuanAdd();

                                if (pengajuan.jenis_pengajuan == "lama") {
                                    this.data_sertifikat[idx].jenis_sertifikasi_data       = pengajuan
                                    this.data_sertifikat[idx].jenis_sertifikasi_id         = pengajuan.master_sertifikat_id;
                                    this.data_sertifikat[idx].jenis_sertifikasi_text       = pengajuan.master_sertifikat_text;
                                    this.data_sertifikat[idx].jenis_sertifikasi_is_product = pengajuan.master_sertifikat_is_product;
                                    this.data_sertifikat[idx].komoditas                    = pengajuan.data_komoditas
                                } else {
                                    let dataPermohonanIDB = await idb.pelanggan_permohonan.where({'name': 'jenis_permohonan_' + idx}).first();
                                    if (dataPermohonanIDB.step2.jenis_sertifikasi != null) {
                                        this.data_sertifikat[idx].jenis_sertifikasi_data       = dataPermohonanIDB.step2.jenis_sertifikasi;
                                        this.data_sertifikat[idx].jenis_sertifikasi_id         = dataPermohonanIDB.step2.jenis_sertifikasi.sert_id;
                                        this.data_sertifikat[idx].jenis_sertifikasi_text       = dataPermohonanIDB.step2.jenis_sertifikasi.sert_nama;
                                        this.data_sertifikat[idx].jenis_sertifikasi_is_product = dataPermohonanIDB.step2.jenis_sertifikasi.sert_is_product;
                                        this.data_sertifikat[idx].komoditas                    = dataPermohonanIDB.step2.komoditas;
                                    }
                                }

                                setTimeout(async () => {
                                    await this.setComboDataSertifikasi(idx) // set easyui

                                    if (pengajuan.jenis_pengajuan == "lama") {
                                        let row = {
                                            sert_id: pengajuan.master_sertifikat_id,
                                            sert_nama: pengajuan.master_sertifikat_text,
                                            sert_is_product: pengajuan.master_sertifikat_is_product,
                                        }
                                        await this.comboDataSertifikasiOnSelect(idx, row) // set default value
                                    } else {
                                        let dataPermohonanIDB = await idb.pelanggan_permohonan.where({'name': 'jenis_permohonan_' + idx}).first();
                                        if (dataPermohonanIDB.step2.jenis_sertifikasi != null) {
                                            let row = {
                                                sert_id: dataPermohonanIDB.step2.jenis_sertifikasi.sert_id,
                                                sert_nama: dataPermohonanIDB.step2.jenis_sertifikasi.sert_nama,
                                                sert_is_product: dataPermohonanIDB.step2.jenis_sertifikasi.sert_is_product,
                                            }
                                            await this.comboDataSertifikasiOnSelect(idx, row) // set default value
                                        }
                                    }
                                }, 1000)

                            })

                            setTimeout(() => $(".tab-content").height("100%"), 1500);
                        }, 500)
                    },
                    validate() {
                        this.data_sertifikat.map((e, idx) => {
                            if (e.jenis_sertifikasi_id == null) throw "Pilih Jenis Sertifikasi"
                            if (e.jenis_sertifikasi_is_product === "ya" && e.komoditas.length === 0) throw `Pengajuan ${idx + 1}: Mohon lengkapi data komoditas`;

                            // validasi dokumen
                            e.dokumens.map(dok => {
                                if (dok.my_document == null) throw `Upload ${dok.dt_name}`
                            })
                        })
                    },
                    async pengajuanAdd() {
                        this.data_sertifikat.push({
                            jenis_sertifikasi_data: null,
                            jenis_sertifikasi_id: null, // upload to server
                            jenis_sertifikasi_is_product: 'tidak',
                            jenis_sertifikasi_text: "--Pilih Jenis Sertifikasi--",

                            // setting form input komoditas
                            jenis_komoditas_id: null,
                            jenis_komoditas_text: "-- Pilih Komoditas --",
                            jenis_komoditas_form_type: "add",
                            jenis_komoditas_form_edited_id: null,

                            // data komoditas
                            komoditas: [],

                            // dokumen upload
                            dokumens: [],
                        })
                    },
                    uploadDokumen(pengajuanIndex, id) {
                        self            = this;
                        const el        = document.querySelector("#dokumen" + pengajuanIndex + id);
                        const doc       = el.files[0]
                        const dt_upload = {"id": id, "dokumen": doc};
                        if (dt_upload.dokumen.type !== "application/pdf") {
                            swalWithBootstrapButtons({
                                title: `Validasi`,
                                text: "Dokumen harus bertipe PDF",
                                type: 'warning'
                            })
                            $("#dokumen" + pengajuanIndex + id).val("")
                        }

                        let formData = new FormData();
                        formData.append("sert_dok_id", id)
                        formData.append("file", doc)
                        $.ajax({
                            url: `{{url("$url/ajax?action=upload_dokumen")}}`,
                            type: 'post',
                            processData: false,
                            contentType: false,
                            data: formData,
                            success: function (res) {
                                toastCenter({
                                    type: 'success',
                                    title: res.message
                                })

                                // update ALL kelengkapan dokumen
                                self.data_sertifikat.map((e, idx) => {
                                    self.getDokumenSertifikasi(idx)
                                })

                            },
                            error: function (xhr) {
                                if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                            }
                        });
                    },
                    resetUploadDokumen(pengajuanIndex) {
                        if (this.data_sertifikat[pengajuanIndex].dokumens != null) {
                            this.data_sertifikat[pengajuanIndex].dokumens.map(e => {
                                $("#dokumen" + pengajuanIndex + e.dt_id).val("")
                            });
                        }
                    },
                    async resetFormKomoditas(pengajuanIndex) {
                        $("#step2_komoditi_merk" + pengajuanIndex).val("");
                        $("#step2_komoditi_sni" + pengajuanIndex).val("");
                        $("#step2_komoditi_tipe" + pengajuanIndex).val("");
                        $("#step2_komoditi_ukuran" + pengajuanIndex).val("");
                        $("#step2_produksi_tahunan" + pengajuanIndex).val("");
                        $("#step2_satuan_produksi" + pengajuanIndex).val("");
                        await this.setComboDataKomoditas(pengajuanIndex, null)
                        $("#step2_komoditi_datas" + pengajuanIndex).combogrid('clear');
                        this.data_sertifikat[pengajuanIndex].jenis_komoditas_form_type      = 'add';
                        this.data_sertifikat[pengajuanIndex].jenis_komoditas_form_edited_id = null;

                    },
                    validateKomoditas(pengajuanIndex) {
                        let sni              = $.trim($("#step2_komoditi_sni" + pengajuanIndex).val());
                        let merk             = $.trim($("#step2_komoditi_merk" + pengajuanIndex).val());
                        let tipe             = $.trim($("#step2_komoditi_tipe" + pengajuanIndex).val());
                        let ukuran           = $.trim($("#step2_komoditi_ukuran" + pengajuanIndex).val());
                        let produksi_tahunan = $.trim($("#step2_produksi_tahunan" + pengajuanIndex).val());
                        let satuan_produksi  = $.trim($("#step2_satuan_produksi" + pengajuanIndex).val());
                        if (this.data_sertifikat[pengajuanIndex].jenis_komoditas_id == null) throw `Pilih Komoditas (Pengajuan ${pengajuanIndex + 1})`;
                        if (sni === "") throw `Tuliskan No SNI (Pengajuan ${pengajuanIndex + 1})`;
                        if (merk === "") throw `Tuliskan Merk (Pengajuan ${pengajuanIndex + 1})`;
                        if (tipe === "") throw `Tuliskan Tipe Komoditas (Pengajuan ${pengajuanIndex + 1})`;
                        if (ukuran === "") throw `Tuliskan Ukuran (Pengajuan ${pengajuanIndex + 1})`;
                        if (produksi_tahunan === "") throw `Tuliskan Produksi Tahunan (Pengajuan ${pengajuanIndex + 1})`;
                        if (satuan_produksi === "") throw `Tuliskan Satuan Produksi (Pengajuan ${pengajuanIndex + 1})`;
                    },
                    addOrUpdateKomoditas(idx) {
                        if (this.data_sertifikat[idx].jenis_komoditas_form_type === "add") this.addKomoditas(idx)
                        else this.updateKomoditi(idx)
                    },
                    async addKomoditas(pengajuanIndex) {
                        try {
                            this.validateKomoditas(pengajuanIndex)

                            let newKomoditas = {
                                'id': (Math.random() + 1).toString(36).substring(7),
                                "pengajuan_index": pengajuanIndex,
                                "komoditi_id": this.data_sertifikat[pengajuanIndex].jenis_komoditas_id,
                                "komoditi_nama": $.trim(this.data_sertifikat[pengajuanIndex].jenis_komoditas_text),
                                "sni": $.trim($("#step2_komoditi_sni" + pengajuanIndex).val()),
                                "merk": $.trim($("#step2_komoditi_merk" + pengajuanIndex).val()),
                                "tipe": $.trim($("#step2_komoditi_tipe" + pengajuanIndex).val()),
                                "ukuran": $.trim($("#step2_komoditi_ukuran" + pengajuanIndex).val()),
                                "produksi_tahunan": $.trim($("#step2_produksi_tahunan" + pengajuanIndex).val()),
                                "satuan_produksi": $.trim($("#step2_satuan_produksi" + pengajuanIndex).val()),
                            };

                            // this.komoditas.push(newKomoditas)
                            let dataPermohonanIDB = await idb.pelanggan_permohonan.where({'name': 'jenis_permohonan_' + pengajuanIndex}).first();
                            dataPermohonanIDB.step2.komoditas.push(newKomoditas);

                            await idb.pelanggan_permohonan.update(dataPermohonanIDB.id, dataPermohonanIDB);
                            this.data_sertifikat[pengajuanIndex].komoditas = dataPermohonanIDB.step2.komoditas;
                            await this.resetFormKomoditas(pengajuanIndex);
                        } catch (message) {
                            swalWithBootstrapButtons({
                                title: `Validasi Komoditas`,
                                text: message,
                                type: 'warning',
                            })
                        }
                    },
                    async deleteKomoditi(pengajuanIndex, id) {
                        let dataPermohonanIDB    = await idb.pelanggan_permohonan.where({'name': 'jenis_permohonan_' + pengajuanIndex}).first();
                        let selectedKomoditasIdx = dataPermohonanIDB.step2.komoditas.findIndex(e => e.id == id);
                        let selectedKomoditas    = dataPermohonanIDB.step2.komoditas[selectedKomoditasIdx];

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
                                dataPermohonanIDB.step2.komoditas.splice(selectedKomoditasIdx, 1);
                                await idb.pelanggan_permohonan.update(dataPermohonanIDB.id, dataPermohonanIDB);
                                this.data_sertifikat[pengajuanIndex].komoditas = dataPermohonanIDB.step2.komoditas;
                            }
                        });
                    },
                    async editKomoditi(pengajuanIndex, id) {
                        let dataPermohonanIDB = await idb.pelanggan_permohonan.where({'name': 'jenis_permohonan_' + pengajuanIndex}).first();
                        let selectedKomoditas = dataPermohonanIDB.step2.komoditas.find(e => e.id == id);

                        $("#step2_komoditi_merk" + pengajuanIndex).val(selectedKomoditas.merk);
                        $("#step2_komoditi_sni" + pengajuanIndex).val(selectedKomoditas.sni);
                        $("#step2_komoditi_tipe" + pengajuanIndex).val(selectedKomoditas.tipe);
                        $("#step2_komoditi_ukuran" + pengajuanIndex).val(selectedKomoditas.ukuran);
                        $("#step2_produksi_tahunan" + pengajuanIndex).val(selectedKomoditas.produksi_tahunan);
                        $("#step2_satuan_produksi" + pengajuanIndex).val(selectedKomoditas.satuan_produksi);
                        this.setComboDataKomoditas(pengajuanIndex, selectedKomoditas.komoditi_nama);
                        $('#step2_komoditi_datas' + pengajuanIndex).combogrid('setValue', selectedKomoditas.komoditi_nama);

                        this.data_sertifikat[pengajuanIndex].jenis_komoditas_id             = selectedKomoditas.komoditi_id;
                        this.data_sertifikat[pengajuanIndex].jenis_komoditas_text           = selectedKomoditas.komoditi_nama;
                        this.data_sertifikat[pengajuanIndex].jenis_komoditas_form_type      = "update";
                        this.data_sertifikat[pengajuanIndex].jenis_komoditas_form_edited_id = id;
                    },
                    async updateKomoditi(pengajuanIndex) {
                        try {
                            let id = this.data_sertifikat[pengajuanIndex].jenis_komoditas_form_edited_id;
                            this.validateKomoditas(pengajuanIndex)
                            let dataPermohonanIDB    = await idb.pelanggan_permohonan.where({'name': 'jenis_permohonan_' + pengajuanIndex}).first();
                            let selectedKomoditasIdx = dataPermohonanIDB.step2.komoditas.findIndex(e => e.id == id);
                            let selectedKomoditas    = dataPermohonanIDB.step2.komoditas[selectedKomoditasIdx];

                            dataPermohonanIDB.step2.komoditas[selectedKomoditasIdx] = {
                                "komoditi_id": selectedKomoditas.komoditi_id,
                                "komoditi_nama": selectedKomoditas.komoditi_nama,
                                "sni": $.trim($("#step2_komoditi_sni" + pengajuanIndex).val()),
                                "merk": $.trim($("#step2_komoditi_merk" + pengajuanIndex).val()),
                                "tipe": $.trim($("#step2_komoditi_tipe" + pengajuanIndex).val()),
                                "ukuran": $.trim($("#step2_komoditi_ukuran" + pengajuanIndex).val()),
                                "produksi_tahunan": $.trim($("#step2_produksi_tahunan" + pengajuanIndex).val()),
                                "satuan_produksi": $.trim($("#step2_satuan_produksi" + pengajuanIndex).val()),
                            }
                            await idb.pelanggan_permohonan.update(dataPermohonanIDB.id, dataPermohonanIDB);
                            this.data_sertifikat[pengajuanIndex].komoditas = dataPermohonanIDB.step2.komoditas;
                            await this.resetFormKomoditas(pengajuanIndex);
                        } catch (message) {
                            swalWithBootstrapButtons({
                                title: `Validasi Komoditas`,
                                text: message,
                                type: 'warning',
                            })
                        }
                    },
                    calcelUpdateKomoditi(pengajuanIndex) {
                        this.data_sertifikat[pengajuanIndex].jenis_komoditas_form_type = "add";
                        this.resetFormKomoditas(pengajuanIndex);
                    },
                    getDokumenSertifikasi(pengajuanIndex) {
                        $.get(`{{url("$url/ajax?action=dokumen_sertifikat")}}&sert_id=${this.data_sertifikat[pengajuanIndex].jenis_sertifikasi_id}`)
                            .then(response => {
                                this.data_sertifikat[pengajuanIndex].dokumens = response.results
                                setTimeout(() => {
                                    $(".tab-content").height("100%")
                                    this.$forceUpdate();
                                }, 500)
                            })
                            .fail((xhr) => {
                                if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                            });
                    },
                    setComboDataKomoditas(pengajuanIndex, search) {
                        let self = this;
                        let url  = `{{ url("$url/ajax?action=combogrid_komoditas") }}&is_product=${this.data_sertifikat[pengajuanIndex].jenis_sertifikasi_is_product}`
                        if (search != null) {
                            url += '&q=' + encodeURI(search)
                        }

                        $('#step2_komoditi_datas' + pengajuanIndex).combogrid({
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
                            value: self.data_sertifikat[pengajuanIndex].jenis_komoditas_text,
                            multiSort: true,
                            fitColumns: true,
                            required: true,
                            columns: [[
                                {field: 'komodt_id', hidden: true},
                                {field: 'komodt_nama', title: 'Nama Komoditas', width: 250, sortable: true,},
                                {field: 'komodt_sni', title: 'No SNI', width: 100, sortable: true,},
                            ]],
                            onSelect: function (index, row) {
                                console.log(pengajuanIndex);
                                self.data_sertifikat[pengajuanIndex].jenis_komoditas_id   = row.komodt_id;
                                self.data_sertifikat[pengajuanIndex].jenis_komoditas_text = row.komodt_nama;

                                $("#step2_komoditi_sni" + pengajuanIndex).val(row.komodt_sni)
                            },
                        });
                    },
                    setComboDataSertifikasi(pengajuanIndex) {
                        let self = this;
                        let url  = `{{ url("$url/ajax?action=combogrid_sertifikasi") }}`
                        if (this.data_sertifikat[pengajuanIndex].jenis_sertifikasi_id != null) {
                            url += "&q=" + this.data_sertifikat[pengajuanIndex].jenis_sertifikasi_text;
                        }

                        $('#step2_jenis_sertifikasi' + pengajuanIndex).combogrid({
                            readonly: window.vueStepOne.data_pengajuan[pengajuanIndex].jenis_pengajuan == "lama",
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
                            value: self.data_sertifikat[pengajuanIndex].jenis_sertifikasi_text,
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
                                self.comboDataSertifikasiOnSelect(pengajuanIndex, row)

                                // Update IDB
                                let dataPermohonanIDB                     = await idb.pelanggan_permohonan.where({'name': 'jenis_permohonan_' + pengajuanIndex}).first();
                                dataPermohonanIDB.step2.jenis_sertifikasi = row;
                                await idb.pelanggan_permohonan.update(dataPermohonanIDB.id, dataPermohonanIDB);
                            },
                        });


                        if (self.data_sertifikat[pengajuanIndex].jenis_sertifikasi_id != null) {
                            console.log('set default value to: ', self.data_sertifikat[pengajuanIndex].jenis_sertifikasi_id)
                            $('#step2_jenis_sertifikasi').combogrid('setValue', self.data_sertifikat[pengajuanIndex].jenis_sertifikasi_id)
                        }
                    },
                    async comboDataSertifikasiOnSelect(pengajuanIndex, row) {
                        this.data_sertifikat[pengajuanIndex].jenis_sertifikasi_data       = row
                        this.data_sertifikat[pengajuanIndex].jenis_sertifikasi_id         = row.sert_id;
                        this.data_sertifikat[pengajuanIndex].jenis_sertifikasi_text       = row.sert_nama;
                        this.data_sertifikat[pengajuanIndex].jenis_sertifikasi_is_product = row.sert_is_product;
                        this.resetUploadDokumen(pengajuanIndex);
                        this.getDokumenSertifikasi(pengajuanIndex);

                        setTimeout(async () => {
                            if (window.vueStepOne.data_pengajuan[pengajuanIndex].jenis_pengajuan === "baru") {
                                this.setComboDataKomoditas(pengajuanIndex, null)
                                let dataPermohonanIDB                          = await idb.pelanggan_permohonan.where({'name': 'jenis_permohonan_' + pengajuanIndex}).first();
                                this.data_sertifikat[pengajuanIndex].komoditas = dataPermohonanIDB.step2.komoditas;
                            } else {
                                this.data_sertifikat[pengajuanIndex].komoditas = window.vueStepOne.data_pengajuan[pengajuanIndex].data_komoditas
                            }
                        }, 500)
                    }
                }
            })
        })
    </script>
@endpush
