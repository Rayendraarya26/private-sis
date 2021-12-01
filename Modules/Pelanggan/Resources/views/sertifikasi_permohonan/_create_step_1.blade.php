<div class="row" id="vueStepOne">
    <template v-for="(data, idx) in data_pengajuan">
        <div class="col-md-12" style="text-align: center; padding-top: 20px">
            <h3>Pengajuan @{{ idx + 1 }}</h3>
        </div>
        <div class="col-md-6 col-xs-6" @click="setJenisPermohonan(idx, 'baru')"
             style="cursor: pointer">
            <div
                style="justify-content: center; align-items: center; horiz-align: center">
                <img src="{{asset('images/sertifikasi-asset/pengajuan_baru.jpg')}}"
                     alt="pengajuan baru" class="step1_image">
                <p style="text-align: center">
                    <input type="radio" :id="'step1_jenis_permohonan_baru' + idx"
                           :name="'step1_jenis_permohonan' + idx" value="baru"
                           aria-label="Pengajuan Sertifikat Baru">
                    Pengajuan Sertifikat Baru
                </p>
            </div>
        </div>
        <div class="col-md-6 col-xs-6"
             style="cursor: pointer">
            <div @click="setJenisPermohonan(idx, 'lama')">
                <img src="{{asset('images/sertifikasi-asset/pengajuan_lama.jpg')}}"
                     alt="pengajuan baru" class="step1_image">
                <p style="text-align: center">
                    <input type="radio" :id="'step1_jenis_permohonan_lama' + idx"
                           :name="'step1_jenis_permohonan' + idx" value="lama"
                           aria-label="Pengajuan Sertifikat Baru">
                    Perpanjangan Sertifikat
                </p>
            </div>

            <div class="form-group" v-if="data_pengajuan[idx].jenis_pengajuan == 'lama'">
                <label>Pilih Sertifikat</label>
                <input :id="'step1_sertifikat_lama' + idx" :name="'step1_sertifikat_lama' + idx" class="form-control"
                       aria-label="Pilih Sertifikat"
                       style="width: 100%">
            </div>
        </div>
        <hr style="border: 1px dashed grey;border-radius: 5px; width: 100%">
    </template>

    <div class="col-md-12" style="padding-top: 50px">
        <div style="text-align: center; justify-content: center">
            <button class="btn btn-sm btn-primary custom-cooltipz"
                    aria-label="Tambah Pengajuan Sertifikasi"
                    data-cooltipz-size="large"
                    data-cooltipz-dir="top"
                    @click="pengajuanAdd"
                    :disabled="checkAvailTambah()">
                <i class="fas fa-plus"></i> Pengajuan
            </button>

            <button class="btn btn-sm btn-danger custom-cooltipz"
                    aria-label="Hapus Pengajuan Sertifiaksi"
                    data-cooltipz-size="large"
                    data-cooltipz-dir="top"
                    @click="pengajuanDelete"
                    v-if="data_pengajuan.length > 1"
            >
                <i class="fas fa-minus"></i> Pengajuan
            </button>
        </div>
    </div>
</div>

@push('javascript')
    <script>
        // Vue Step One
        $(document).ready(function () {
            window.vueStepOne = new Vue({
                el: "#vueStepOne",
                data: {
                    data_pengajuan: [],
                },
                mounted() {
                    this.loadIdb();
                },
                methods: {
                    async loadIdb() {
                        this.data_pengajuan = []
                        let currentData     = await idb.pelanggan_permohonan
                            .orderBy('name')
                            .filter(function (pelanggan) {
                                return pelanggan.name.includes('jenis_permohonan_');
                            }).toArray();


                        if (currentData.length > 0) {
                            currentData.map(async (e, idx) => {
                                this.pengajuanAdd();
                                this.data_pengajuan[idx].jenis_pengajuan = e.value
                                await this.setJenisPermohonan(idx, e.value)
                                setTimeout(() => $(".tab-content").height("100%"), 500);
                            })
                        } else {
                            this.pengajuanAdd();
                            setTimeout(() => $(".tab-content").height("100%"), 500);
                        }
                    },
                    pengajuanAdd() {
                        this.data_pengajuan.push({
                            jenis_pengajuan: null,
                            sertifikat_lama_id: null, // riwayat sertifikat
                            sertifikat_lama_text: null, // riwayat sertifikat
                            sertifikat_lama_data: [],

                            master_sertifikat_id: null, // master sertifikat
                            master_sertifikat_text: null, // master sertifikat
                            master_sertifikat_is_product: null, // master sertifikat
                            data_komoditas: [],
                        })
                    },
                    validate() {
                        this.data_pengajuan.map((e, idx) => {
                            let jenis = document.querySelector(`input[name="step1_jenis_permohonan${idx}"]:checked`);
                            console.log(this.data_pengajuan[idx]);
                            if (jenis == null) throw `Pilih Jenis Permohonan (Pengajuan ${idx + 1})`
                            if (this.data_pengajuan[idx].jenis_pengajuan == "lama" && this.data_pengajuan[idx].sertifikat_lama_id == null) throw `Pilih Sertifikat (Pengajuan ${idx + 1})`
                        })
                    },
                    async pengajuanDelete() {
                        let currentData = await idb.pelanggan_permohonan
                            .orderBy('name')
                            .filter(function (pelanggan) {
                                return pelanggan.name.includes('jenis_permohonan_');
                            }).toArray();

                        if (currentData.length > 1 && this.data_pengajuan.length == currentData.length) {
                            let deletedData = currentData[currentData.length - 1]
                            await window.idb.pelanggan_permohonan.where('name').equals(deletedData.name).delete();
                        }
                        await this.loadIdb()
                    },
                    async setJenisPermohonan(pengajuanIndex, tipe) {
                        const currentaData = await idb.pelanggan_permohonan
                            .where({name: "jenis_permohonan_" + pengajuanIndex})
                            .first();

                        let idbStep1 = {
                            name: "jenis_permohonan_" + pengajuanIndex,
                            value: tipe,
                            sertifikat_lama_data: [],
                        }
                        if (currentaData == null) {
                            // set untuk di step 2
                            let idbStep2 = {step2: {komoditas: [], jenis_sertifikasi: null}}
                            idbStep1     = Object.assign(idbStep1, idbStep2);

                            await idb.pelanggan_permohonan.put(idbStep1);
                        } else {
                            if (tipe == 'lama') {
                                idbStep1.sertifikat_lama_data = currentaData.sertifikat_lama_data;
                                // set untuk di step 2 (reset data)
                                let idbStep2                  = {step2: {komoditas: [], jenis_sertifikasi: null}}
                                idbStep1                      = Object.assign(idbStep1, idbStep2);
                            }

                            await idb.pelanggan_permohonan.update(currentaData.id, idbStep1);
                        }

                        this.data_pengajuan[pengajuanIndex].jenis_pengajuan = tipe;
                        if (tipe === "baru") {
                            $("#step1_jenis_permohonan_baru" + pengajuanIndex).prop('checked', true);
                            $("#step1_jenis_permohonan_lama" + pengajuanIndex).prop('checked', false);
                        } else {
                            $("#step1_jenis_permohonan_baru" + pengajuanIndex).prop('checked', false);
                            $("#step1_jenis_permohonan_lama" + pengajuanIndex).prop('checked', true);
                            setTimeout(async () => {
                                // Load to set initial Index DB
                                let sertifikatData = null;
                                if (currentaData != null) {
                                    if (currentaData.sertifikat_lama_data != null) {
                                        sertifikatData = currentaData.sertifikat_lama_data;
                                    }
                                }


                                if (sertifikatData != null) {
                                    this.data_pengajuan[pengajuanIndex].sertifikat_lama_id           = sertifikatData.cust_sert_id
                                    this.data_pengajuan[pengajuanIndex].sertifikat_lama_text         = sertifikatData.cust_sert_nomor_sertifikat
                                    this.data_pengajuan[pengajuanIndex].master_sertifikat_id         = sertifikatData.sert_id;
                                    this.data_pengajuan[pengajuanIndex].master_sertifikat_text       = sertifikatData.sert_nama;
                                    this.data_pengajuan[pengajuanIndex].master_sertifikat_is_product = sertifikatData.sert_is_product;

                                    let komoditas = {
                                        'komoditi_id': sertifikatData.komodt_id,
                                        'komoditi_nama': sertifikatData.komodt_nama,
                                        'sni': sertifikatData.cust_sert_nomor_sni,
                                        'merk': sertifikatData.cust_sert_merk,
                                        'tipe': sertifikatData.cust_sert_tipe,
                                        'ukuran': sertifikatData.cust_sert_ukuran,
                                        'produksi_tahunan': sertifikatData.cust_sert_produksi_tahunan,
                                        'satuan_produksi': sertifikatData.cust_sert_produksi_tahunan_satuan,
                                    };
                                    this.data_pengajuan[pengajuanIndex].data_komoditas.push(komoditas);

                                    await this.setComboSertifikatLama(pengajuanIndex);
                                    $('#step1_sertifikat_lama' + pengajuanIndex).combogrid('setValue', sertifikatData.cust_sert_id)
                                } else {
                                    await this.setComboSertifikatLama(pengajuanIndex);
                                }

                                $(".tab-content").height("100%");
                            }, 500);
                        }
                    },
                    setComboSertifikatLama(pengajuanIndex) {
                        let self = this;
                        let url  = `{{ url("$url/ajax?action=combogrid_sertifikat_lama") }}`
                        if (self.sertifikat_lama_id != null) {
                            url += "&q=" + this.sertifikat_lama_text;
                        }

                        $('#step1_sertifikat_lama' + pengajuanIndex).combogrid({
                            pageSize: '50',
                            // panelWidth: 400,
                            pagination: true,
                            nowrap: false,
                            idField: 'cust_sert_id',
                            textField: 'cust_sert_nomor_sertifikat',
                            editable: true,
                            url: url,
                            method: 'get',
                            mode: 'remote',
                            value: self.sertifikat_lama_text,
                            multiSort: true,
                            fitColumns: false,
                            required: true,
                            columns: [[
                                {field: 'cust_sert_id', hidden: true},
                                {
                                    field: 'cust_sert_nomor_sertifikat',
                                    title: 'No Sertifikat',
                                    width: 120,
                                    sortable: true,
                                },
                                {field: 'cust_sert_expired_date', title: 'Expired', width: 200, sortable: true,},
                                {field: 'sert_nama', title: 'Nama Sertifikat', width: 200, sortable: true,},
                                {field: 'komodt_nama', title: 'Nama Komoditas', width: 200, sortable: true,},
                                {field: 'cust_sert_nomor_sni', title: 'Nomor SNI', width: 120, sortable: true,},
                                {field: 'cust_sert_tipe', title: 'Tipe', width: 90, sortable: true,},
                                {field: 'cust_sert_merk', title: 'Merk', width: 90, sortable: true,},
                            ]],
                            onSelect: async function (index, row) {
                                // Insert to Index DB
                                self.data_pengajuan[pengajuanIndex].sertifikat_lama_id           = row.cust_sert_id;
                                self.data_pengajuan[pengajuanIndex].sertifikat_lama_text         = row.cust_sert_nomor_sertifikat;
                                self.data_pengajuan[pengajuanIndex].master_sertifikat_id         = row.sert_id;
                                self.data_pengajuan[pengajuanIndex].master_sertifikat_text       = row.sert_nama;
                                self.data_pengajuan[pengajuanIndex].master_sertifikat_is_product = row.sert_is_product;

                                self.data_pengajuan[pengajuanIndex].sertifikat_lama_data = row;

                                let komoditas = {
                                    'komoditi_id': row.komodt_id,
                                    'komoditi_nama': row.komodt_nama,
                                    'sni': row.cust_sert_nomor_sni,
                                    'merk': row.cust_sert_merk,
                                    'tipe': row.cust_sert_tipe,
                                    'ukuran': row.cust_sert_ukuran,
                                    'produksi_tahunan': row.cust_sert_produksi_tahunan,
                                    'satuan_produksi': row.cust_sert_produksi_tahunan_satuan,
                                };

                                self.data_pengajuan[pengajuanIndex].data_komoditas = [];
                                self.data_pengajuan[pengajuanIndex].data_komoditas.push(komoditas);


                                const currentaData = await idb.pelanggan_permohonan
                                    .where({name: "jenis_permohonan_" + pengajuanIndex})
                                    .first();

                                if (currentaData != null) {
                                    currentaData.sertifikat_lama_data = row
                                }

                                await idb.pelanggan_permohonan.update(currentaData.id, currentaData);
                            },
                        });
                    },
                    checkAvailTambah() {
                        let avail = true;
                        this.data_pengajuan.map(e => {
                            if (e.jenis_pengajuan == null) avail = false
                        })
                        return !avail
                    }
                }
            })
        })
    </script>
@endpush
