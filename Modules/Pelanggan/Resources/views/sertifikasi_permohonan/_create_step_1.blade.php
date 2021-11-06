<div class="row" id="vueStepOne">
    <div class="col-md-6 col-xs-6" @click="setJenisPermohonan('baru')"
         style="cursor: pointer">
        <div
            style="justify-content: center; align-items: center; horiz-align: center">
            <img src="{{asset('images/sertifikasi-asset/pengajuan_baru.jpg')}}"
                 alt="pengajuan baru" class="step1_image">
            <p style="text-align: center">
                <input type="radio" id="step1_jenis_permohonan_baru"
                       name="step1_jenis_permohonan" value="baru"
                       aria-label="Pengajuan Sertifikat Baru">
                Pengajuan Sertifikat Baru
            </p>
        </div>
    </div>
    <div class="col-md-6 col-xs-6"
         style="cursor: pointer">
        <div @click="setJenisPermohonan('lama')">
            <img src="{{asset('images/sertifikasi-asset/pengajuan_lama.jpg')}}"
                 alt="pengajuan baru" class="step1_image">
            <p style="text-align: center">
                <input type="radio" id="step1_jenis_permohonan_lama"
                       name="step1_jenis_permohonan" value="lama"
                       aria-label="Pengajuan Sertifikat Baru">
                Perpanjangan Sertifikat
            </p>
        </div>

        <div class="form-group" v-if="jenis_pengajuan == 'lama'">
            <label for="step1_sertifikat_lama">Pilih Sertifikat</label>
            <input id="step1_sertifikat_lama" name="step1_sertifikat_lama" class="form-control" style="width: 100%">
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
                    jenis_pengajuan: null,

                    // Jika Re-Sertifikasi
                    sertifikat_lama_id: null, // riwayat sertifikat
                    sertifikat_lama_text: null, // riwayat sertifikat

                    master_sertifikat_id: null, // master sertifikat
                    master_sertifikat_text: null, // master sertifikat
                    master_sertifikat_is_product: null, // master sertifikat

                    data_komoditas: [],
                },
                mounted() {
                    this.loadIdb();
                },
                methods: {
                    validate() {
                        let jenis = document.querySelector('input[name="step1_jenis_permohonan"]:checked');
                        if (jenis == null) throw "Pilih Jenis Permohonan"
                        if (this.jenis_pengajuan == "lama" && this.sertifikat_lama_id == null) throw "Pilih Sertifikat"
                    },
                    async loadIdb() {
                        let currentData = await idb.pelanggan_permohonan
                            .where({name: "jenis_permohonan"})
                            .first()


                        if (currentData != null) {
                            await this.setJenisPermohonan(currentData.value)
                        }
                    },
                    async setJenisPermohonan(tipe) {
                        const currentaData = await idb.pelanggan_permohonan
                            .where({name: "jenis_permohonan"})
                            .first();

                        let dbData = {name: "jenis_permohonan", value: tipe}
                        if (currentaData == null) {
                            await idb.pelanggan_permohonan.put(dbData);
                        } else {
                            await idb.pelanggan_permohonan.update(currentaData.id, dbData);
                        }

                        this.jenis_pengajuan = tipe;
                        if (tipe === "baru") {
                            $("#step1_jenis_permohonan_baru").prop('checked', true);
                            $("#step1_jenis_permohonan_lama").prop('checked', false);
                        } else {
                            $("#step1_jenis_permohonan_baru").prop('checked', false);
                            $("#step1_jenis_permohonan_lama").prop('checked', true);
                            setTimeout(async () => {
                                // Load to set initial Index DB
                                let currentData = await idb.pelanggan_permohonan
                                    .where({name: "sertifikat_lama"})
                                    .first()

                                if (currentData != null) {
                                    this.sertifikat_lama_id           = currentData.value.cust_sert_id
                                    this.sertifikat_lama_text         = currentData.value.cust_sert_nomor_sertifikat
                                    this.master_sertifikat_id         = currentData.value.sert_id;
                                    this.master_sertifikat_text       = currentData.value.sert_nama;
                                    this.master_sertifikat_is_product = currentData.value.sert_is_product;

                                    let komoditas = {
                                        'komoditi_id': currentData.value.komodt_id,
                                        'komoditi_nama': currentData.value.komodt_nama,
                                        'sni': currentData.value.cust_sert_nomor_sni,
                                        'merk': currentData.value.cust_sert_merk,
                                        'tipe': currentData.value.cust_sert_tipe,
                                        'ukuran': currentData.value.cust_sert_ukuran,
                                        'produksi_tahunan': currentData.value.cust_sert_produksi_tahunan,
                                        'satuan_produksi': currentData.value.cust_sert_produksi_tahunan_satuan,
                                    };
                                    this.data_komoditas.push(komoditas);

                                    await this.setComboSertifikatLama();
                                    $('#step1_sertifikat_lama').combogrid('setValue', currentData.value.cust_sert_id)
                                } else {
                                    await this.setComboSertifikatLama();
                                }

                                $(".tab-content").height("100%");
                            }, 500);
                        }
                    },
                    setComboSertifikatLama() {
                        let self = this;
                        let url = `{{ url("$url/ajax?action=combogrid_sertifikat_lama") }}`
                        if (self.sertifikat_lama_id != null) {
                            url += "&q=" + this.sertifikat_lama_text;
                        }

                        $('#step1_sertifikat_lama').combogrid({
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
                                self.sertifikat_lama_id           = row.cust_sert_id;
                                self.sertifikat_lama_text         = row.cust_sert_nomor_sertifikat;
                                self.master_sertifikat_id         = row.sert_id;
                                self.master_sertifikat_text       = row.sert_nama;
                                self.master_sertifikat_is_product = row.sert_is_product;

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

                                self.data_komoditas = [];
                                self.data_komoditas.push(komoditas);

                                const currentaData = await idb.pelanggan_permohonan.where({name: "sertifikat_lama"}).first();
                                let dbData         = {name: "sertifikat_lama", value: row}
                                if (currentaData == null) {
                                    await idb.pelanggan_permohonan.put(dbData);
                                } else {
                                    await idb.pelanggan_permohonan.update(currentaData.id, dbData);
                                }
                            },
                        });
                    },
                }
            })
        })
    </script>
@endpush
