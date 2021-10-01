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
                    sertifikat_lama_id: null,
                    sertifikat_lama_text: null,
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
                                    this.sertifikat_lama_id = currentData.value.cust_sert_id
                                    this.sertifikat_lama_text = currentData.value.cust_sert_nomor_sertifikat
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
                            panelWidth: 400,
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
                                {field: 'sert_nama', title: 'Jenis Sertifikat', width: 200, sortable: true,},
                            ]],
                            onSelect: async function (index, row) {
                                // Insert to Index DB
                                const currentaData = await idb.pelanggan_permohonan.where({name: "sertifikat_lama"}).first();
                                let dbData = {name: "sertifikat_lama", value: row}
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
