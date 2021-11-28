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
    <template v-for="idx in total_pengajuan">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <div class="form-group">
                <label :for="'step2_jenis_sertifikasi' + idx">Jenis Sertifikasi</label>
                <input :id="'step2_jenis_sertifikasi' + idx"
                       :name="'step2_jenis_sertifikasi' + idx"
                       aria-label="Jenis Sertifikasi"
                       class="form-control"
                       style="width: 100%">
            </div>
        </div>
        <div class="col-md-4">
            <br>
            <div style="text-align: right; justify-content: right">
                <button class="btn btn-sm btn-danger custom-cooltipz"
                        aria-label="Hapus Pengajuan Sertifiaksi"
                        data-cooltipz-size="large"
                        data-cooltipz-dir="left"
                        @click="pengajuanDelete(idx)"
                        v-if="idx > 1"
                >
                    <i class="fas fa-minus"></i> Pengajuan
                </button>
            </div>
        </div>

        <div class="col-md-12" v-if="index_setting_sertifikasi[idx].jenis_sertifikasi_id != null">
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
                    <tr v-for="(dds, indexDok) in index_data_dokumen[idx]">
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

        <div class="col-md-12" v-if="index_setting_sertifikasi[idx].jenis_sertifikasi_id != null"
             style="padding-bottom: 20px">
            <div class="row">
                <div class="col-md-12">
                    <h3>Data Komoditas <small aria-label="Setiap perubahan akan tersimpan di storage browser"
                                              class="custom-cooltipz"
                                              data-cooltipz-size="large"
                                              data-cooltipz-dir="right"><i class="fal fa-database"></i></small></h3>
                    <div class="row" v-if="window.vueStepOne.jenis_pengajuan == 'baru'">
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
                                    <template v-if="index_setting_komoditas[idx].jenis_komoditas_form_type == 'add'">
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
                                <th v-if="window.vueStepOne.jenis_pengajuan == 'baru'">Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-for="kom in komoditas[idx]">
                                <tr>
                                    <td>@{{ kom.komoditi_nama }}</td>
                                    <td>@{{ kom.sni }}</td>
                                    <td>@{{ kom.merk }}</td>
                                    <td>@{{ kom.tipe }}</td>
                                    <td>@{{ kom.ukuran }}</td>
                                    <td>@{{ kom.produksi_tahunan }}</td>
                                    <td>@{{ kom.satuan_produksi }}</td>
                                    <td v-if="window.vueStepOne.jenis_pengajuan == 'baru'">
                                        <button class="btn btn-xs btn-danger" @click="deleteKomoditi(idx, kom.id)">
                                            <i class="fad fa-trash"></i> Hapus
                                        </button>
                                        <button class="btn btn-xs btn-warning" @click="editKomoditi(idx, kom.id)">
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
        <hr>
    </template>

    <div class="col-md-12" v-show="window.vueStepOne.jenis_pengajuan == 'baru'">
        <div style="text-align: center; justify-content: center">
            <button class="btn btn-sm btn-primary custom-cooltipz"
                    aria-label="Tambah Pengajuan Sertifikasi"
                    data-cooltipz-size="large"
                    data-cooltipz-dir="top"
                    @click="pengajuanAdd">
                <i class="fas fa-plus"></i> Pengajuan
            </button>
            {{--<button class="btn btn-sm btn-danger custom-cooltipz"
                    aria-label="Hapus Pengajuan Sertifiaksi"
                    data-cooltipz-size="large"
                    data-cooltipz-dir="top"
                    @click="total_pengajuan -= 1">
                <i class="fas fa-minus"></i> Pengajuan
            </button>--}}
        </div>
    </div>
</div>

@push('javascript')
    <script>
        $(document).ready(function () {
            window.vueStepTwo = new Vue({
                el: "#vueStepTwo",
                data: {
                    total_pengajuan: 1,
                    index_data_dokumen: [
                        [],
                        [],
                    ],
                    index_setting_sertifikasi: [
                        {},
                        {
                            jenis_sertifikasi_data: null,
                            jenis_sertifikasi_id: null, // upload to server
                            jenis_sertifikasi_is_product: 'tidak',
                            jenis_sertifikasi_text: "--Pilih Jenis Sertifikasi--",
                        }
                    ],
                    index_setting_komoditas: [
                        {}, // dimulai dari index ke 1
                        {
                            jenis_komoditas_id: null,
                            jenis_komoditas_text: "-- Pilih Komoditas --",
                            jenis_komoditas_form_type: "add",
                            jenis_komoditas_form_edited_id: null,
                        }
                    ],

                    komoditas: [], // upload to server
                },
                mounted() {
                    setTimeout(async () => {
                        const currentStep = $('#smartwizard').smartWizard("getStepIndex");
                        if (currentStep === 1) {
                            this.total_pengajuan = window.vueStepOne.sertifikat_lama_data.length
                            this.start();
                        }
                    }, 400)
                },
                methods: {
                    start() {
                        console.log('start...')
                        setTimeout(async () => {
                            console.log('running...')
                            console.log(this.total_pengajuan)
                            for (let i = 1; i == this.total_pengajuan; i++) {
                                console.log(i);
                                let currentData;
                                if (window.vueStepOne.jenis_pengajuan == "lama") {
                                    currentData = {
                                        value: {
                                            sert_id: window.vueStepOne.master_sertifikat_id,
                                            sert_nama: window.vueStepOne.master_sertifikat_text,
                                            sert_is_product: window.vueStepOne.master_sertifikat_is_product,
                                        },
                                    };

                                } else { // pengajuan baru
                                    // Load to set initial Index DB
                                    currentData = await idb.pelanggan_permohonan
                                        .where({name: "jenis_sertifikasi_" + i})
                                        .first()
                                }

                                await this.setComboDataSertifikasi(i)

                                if (currentData != null) {
                                    $('#step2_jenis_sertifikasi' + i).combogrid('setValue', currentData.value.sert_id)
                                    this.comboDataSertifikasiOnSelect(i, currentData.value)
                                }
                            }

                            setTimeout(() => this.$forceUpdate(), 2000);
                        }, 500)
                    },
                    validate() {
                        // New Validate
                        console.log(this.index_data_dokumen);
                        for (let i = 1; i == this.total_pengajuan; i++) {
                            if (this.index_setting_sertifikasi[i].jenis_sertifikasi_id == null) throw "Pilih Jenis Sertifikasi"

                            this.index_data_dokumen[i].map(e => {
                                if (e.my_document == null) throw `Upload ${e.dt_name}`
                            })

                            if (this.index_setting_sertifikasi[i].jenis_sertifikasi_is_product === "ya") {
                                if (this.komoditas[i].length === 0) throw "Mohon lengkapi data komoditas"
                            }
                        }
                    },
                    pengajuanAdd() {
                        this.total_pengajuan += 1;
                        this.index_setting_sertifikasi.push({
                            jenis_sertifikasi_data: null,
                            jenis_sertifikasi_id: null, // upload to server
                            jenis_sertifikasi_is_product: 'tidak',
                            jenis_sertifikasi_text: "--Pilih Jenis Sertifikasi--",
                        })
                        this.index_setting_komoditas.push({
                            jenis_komoditas_id: null,
                            jenis_komoditas_text: "-- Pilih Komoditas --",
                            jenis_komoditas_form_type: "add",
                            jenis_komoditas_form_edited_id: null,
                        })

                        setTimeout(() => this.setComboDataSertifikasi(this.total_pengajuan), 500);
                        setTimeout(() => this.$forceUpdate(), 2000);
                    },
                    pengajuanDelete(idx) {
                        this.total_pengajuan -= 1;
                        this.index_setting_komoditas[idx]   = {};
                        this.index_setting_sertifikasi[idx] = {};
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
                                self.getDokumenSertifikasi(pengajuanIndex)
                            },
                            error: function (xhr) {
                                if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                            }
                        });
                    },
                    resetUploadDokumen(pengajuanIndex) {
                        if (this.index_data_dokumen[pengajuanIndex] != null) {
                            this.index_data_dokumen[pengajuanIndex].map(e => {
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
                        this.index_setting_komoditas[pengajuanIndex].jenis_komoditas_form_type      = 'add';
                        this.index_setting_komoditas[pengajuanIndex].jenis_komoditas_form_edited_id = null;

                    },
                    validateKomoditas(pengajuanIndex) {
                        let sni              = $.trim($("#step2_komoditi_sni" + pengajuanIndex).val());
                        let merk             = $.trim($("#step2_komoditi_merk" + pengajuanIndex).val());
                        let tipe             = $.trim($("#step2_komoditi_tipe" + pengajuanIndex).val());
                        let ukuran           = $.trim($("#step2_komoditi_ukuran" + pengajuanIndex).val());
                        let produksi_tahunan = $.trim($("#step2_produksi_tahunan" + pengajuanIndex).val());
                        let satuan_produksi  = $.trim($("#step2_satuan_produksi" + pengajuanIndex).val());
                        if (this.index_setting_komoditas[pengajuanIndex].jenis_komoditas_id == null) throw `Pilih Komoditas (Pengajuan ${pengajuanIndex})`;
                        if (sni === "") throw `Tuliskan No SNI (Pengajuan ${pengajuanIndex})`;
                        if (merk === "") throw `Tuliskan Merk (Pengajuan ${pengajuanIndex})`;
                        if (tipe === "") throw `Tuliskan Tipe Komoditas (Pengajuan ${pengajuanIndex})`;
                        if (ukuran === "") throw `Tuliskan Ukuran (Pengajuan ${pengajuanIndex})`;
                        if (produksi_tahunan === "") throw `Tuliskan Produksi Tahunan (Pengajuan ${pengajuanIndex})`;
                        if (satuan_produksi === "") throw `Tuliskan Satuan Produksi (Pengajuan ${pengajuanIndex})`;
                    },
                    addOrUpdateKomoditas(idx) {
                        if (this.index_setting_komoditas[idx].jenis_komoditas_form_type === "add") this.addKomoditas(idx)
                        else this.updateKomoditi(idx)
                    },
                    async addKomoditas(pengajuanIndex) {
                        try {
                            this.validateKomoditas(pengajuanIndex)

                            let newKomoditas = {
                                "pengajuan_index": pengajuanIndex,
                                "komoditi_id": this.index_setting_komoditas[pengajuanIndex].jenis_komoditas_id,
                                "komoditi_nama": $.trim(this.index_setting_komoditas[pengajuanIndex].jenis_komoditas_text),
                                "sni": $.trim($("#step2_komoditi_sni" + pengajuanIndex).val()),
                                "merk": $.trim($("#step2_komoditi_merk" + pengajuanIndex).val()),
                                "tipe": $.trim($("#step2_komoditi_tipe" + pengajuanIndex).val()),
                                "ukuran": $.trim($("#step2_komoditi_ukuran" + pengajuanIndex).val()),
                                "produksi_tahunan": $.trim($("#step2_produksi_tahunan" + pengajuanIndex).val()),
                                "satuan_produksi": $.trim($("#step2_satuan_produksi" + pengajuanIndex).val()),
                            };

                            // this.komoditas.push(newKomoditas)
                            await idb.pelanggan_permohonan_komoditas.put(newKomoditas);
                            this.komoditas[pengajuanIndex] = await window.idb.pelanggan_permohonan_komoditas.where({"pengajuan_index": pengajuanIndex}).toArray();
                            await this.resetFormKomoditas(pengajuanIndex);

                            this.$forceUpdate();
                        } catch (message) {
                            swalWithBootstrapButtons({
                                title: `Validasi Komoditas`,
                                text: message,
                                type: 'warning',
                            })
                        }
                    },
                    async deleteKomoditi(pengajuanIndex, id) {
                        let selectedKomoditas = await window.idb.pelanggan_permohonan_komoditas.get({
                            'pengajuan_index': pengajuanIndex,
                            'id': id
                        });
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
                                this.komoditas[pengajuanIndex] = await window.idb.pelanggan_permohonan_komoditas.where({"pengajuan_index": pengajuanIndex}).toArray();
                                this.$forceUpdate();
                            }
                        });
                    },
                    async editKomoditi(pengajuanIndex, id) {
                        let selectedKomoditas = await window.idb.pelanggan_permohonan_komoditas.get({
                            'pengajuan_index': pengajuanIndex,
                            'id': id
                        });

                        console.log(selectedKomoditas);
                        $("#step2_komoditi_merk" + pengajuanIndex).val(selectedKomoditas.merk);
                        $("#step2_komoditi_sni" + pengajuanIndex).val(selectedKomoditas.sni);
                        $("#step2_komoditi_tipe" + pengajuanIndex).val(selectedKomoditas.tipe);
                        $("#step2_komoditi_ukuran" + pengajuanIndex).val(selectedKomoditas.ukuran);
                        $("#step2_produksi_tahunan" + pengajuanIndex).val(selectedKomoditas.produksi_tahunan);
                        $("#step2_satuan_produksi" + pengajuanIndex).val(selectedKomoditas.satuan_produksi);
                        this.setComboDataKomoditas(pengajuanIndex, selectedKomoditas.komoditi_nama);
                        $('#step2_komoditi_datas' + pengajuanIndex).combogrid('setValue', selectedKomoditas.komoditi_nama);

                        this.index_setting_komoditas[pengajuanIndex].jenis_komoditas_id             = selectedKomoditas.komoditi_id;
                        this.index_setting_komoditas[pengajuanIndex].jenis_komoditas_text           = selectedKomoditas.komoditi_nama;
                        this.index_setting_komoditas[pengajuanIndex].jenis_komoditas_form_type      = "update";
                        this.index_setting_komoditas[pengajuanIndex].jenis_komoditas_form_edited_id = id;

                        this.$forceUpdate();
                    },
                    async updateKomoditi(pengajuanIndex) {
                        try {
                            this.validateKomoditas(pengajuanIndex)
                            let selectedDataIdx = this.index_setting_komoditas[pengajuanIndex];
                            await window.idb.pelanggan_permohonan_komoditas.update(selectedDataIdx.jenis_komoditas_form_edited_id, {
                                "komoditi_id": selectedDataIdx.jenis_komoditas_id,
                                "komoditi_nama": selectedDataIdx.jenis_komoditas_text,
                                "sni": $.trim($("#step2_komoditi_sni" + pengajuanIndex).val()),
                                "merk": $.trim($("#step2_komoditi_merk" + pengajuanIndex).val()),
                                "tipe": $.trim($("#step2_komoditi_tipe" + pengajuanIndex).val()),
                                "ukuran": $.trim($("#step2_komoditi_ukuran" + pengajuanIndex).val()),
                                "produksi_tahunan": $.trim($("#step2_produksi_tahunan" + pengajuanIndex).val()),
                                "satuan_produksi": $.trim($("#step2_satuan_produksi" + pengajuanIndex).val()),
                            });
                            console.log(selectedDataIdx.jenis_komoditas_text)
                            this.komoditas[pengajuanIndex] = await window.idb.pelanggan_permohonan_komoditas.where({"pengajuan_index": pengajuanIndex}).toArray();
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
                        this.index_setting_komoditas[pengajuanIndex].jenis_komoditas_form_type = "add";
                        this.resetFormKomoditas(pengajuanIndex);
                    },
                    getDokumenSertifikasi(pengajuanIndex) {
                        $.get(`{{url("$url/ajax?action=dokumen_sertifikat")}}&sert_id=${this.index_setting_sertifikasi[pengajuanIndex].jenis_sertifikasi_id}`)
                            .then(response => {
                                this.index_data_dokumen[pengajuanIndex] = response.results
                                setTimeout(() => $(".tab-content").height("100%"), 500)
                            })
                            .fail((xhr) => {
                                if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                            });
                    },
                    setComboDataKomoditas(pengajuanIndex, search) {
                        let self = this;
                        let url  = `{{ url("$url/ajax?action=combogrid_komoditas") }}&is_product=${this.index_setting_sertifikasi[pengajuanIndex].jenis_sertifikasi_is_product}`
                        if (search != null) {
                            url += '&q=' + search
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
                            value: self.index_setting_komoditas[pengajuanIndex].jenis_komoditas_text,
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
                                self.index_setting_komoditas[pengajuanIndex].jenis_komoditas_id   = row.komodt_id;
                                self.index_setting_komoditas[pengajuanIndex].jenis_komoditas_text = row.komodt_nama;

                                $("#step2_komoditi_sni" + pengajuanIndex).val(row.komodt_sni)
                            },
                        });
                    },
                    setComboDataSertifikasi(pengajuanIndex) {
                        let self = this;
                        let url  = `{{ url("$url/ajax?action=combogrid_sertifikasi") }}`
                        if (this.index_setting_sertifikasi[pengajuanIndex].jenis_sertifikasi_id != null) {
                            url += "&q=" + this.index_setting_sertifikasi[pengajuanIndex].jenis_sertifikasi_text;
                        }

                        $('#step2_jenis_sertifikasi' + pengajuanIndex).combogrid({
                            readonly: window.vueStepOne.jenis_pengajuan == "lama",
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
                            value: self.index_setting_sertifikasi[pengajuanIndex].jenis_sertifikasi_text,
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

                                // Insert to Index DB
                                const currentaData = await idb.pelanggan_permohonan.where({
                                    name: "jenis_sertifikasi_" + pengajuanIndex,
                                }).first();

                                let dbData = {
                                    name: "jenis_sertifikasi_" + pengajuanIndex,
                                    value: row
                                }

                                if (currentaData == null) {
                                    await idb.pelanggan_permohonan.put(dbData);
                                } else {
                                    await idb.pelanggan_permohonan.update(currentaData.id, dbData);
                                }

                                setTimeout(() => self.$forceUpdate(), 1000);
                            },
                        });

                        if (self.index_setting_sertifikasi[pengajuanIndex].jenis_sertifikasi_id != null) {
                            $('#step2_jenis_sertifikasi').combogrid('setValue', self.index_setting_sertifikasi[pengajuanIndex].jenis_sertifikasi_id)
                        }
                    },
                    comboDataSertifikasiOnSelect(pengajuanIndex, row) {
                        this.index_setting_sertifikasi[pengajuanIndex].jenis_sertifikasi_data       = row
                        this.index_setting_sertifikasi[pengajuanIndex].jenis_sertifikasi_id         = row.sert_id;
                        this.index_setting_sertifikasi[pengajuanIndex].jenis_sertifikasi_text       = row.sert_nama;
                        this.index_setting_sertifikasi[pengajuanIndex].jenis_sertifikasi_is_product = row.sert_is_product;
                        this.resetUploadDokumen(pengajuanIndex);
                        this.getDokumenSertifikasi(pengajuanIndex);

                        if (window.vueStepOne.jenis_pengajuan === "baru") {
                            setTimeout(async () => {
                                this.setComboDataKomoditas(pengajuanIndex, null)
                                let dataKomoditasIDB = await window.idb.pelanggan_permohonan_komoditas.where({"pengajuan_index": pengajuanIndex}).toArray();
                                if (dataKomoditasIDB.length > 0) {
                                    this.komoditas[pengajuanIndex] = dataKomoditasIDB
                                    console.log(this.komoditas)
                                }
                            }, 500)
                        }
                    }
                }
            })
        })
    </script>
@endpush
