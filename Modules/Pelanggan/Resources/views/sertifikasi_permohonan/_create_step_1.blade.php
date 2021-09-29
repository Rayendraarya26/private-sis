<div class="row" id="vueStepOne">
    <div class="col-md-6" @click="setJenisPermohonan('baru')"
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
    <div class="col-md-6" @click="setJenisPermohonan('lama')"
         style="cursor: pointer">
        <img src="{{asset('images/sertifikasi-asset/pengajuan_lama.jpg')}}"
             alt="pengajuan baru" class="step1_image">
        <p style="text-align: center">
            <input type="radio" id="step1_jenis_permohonan_lama"
                   name="step1_jenis_permohonan" value="lama"
                   aria-label="Pengajuan Sertifikat Baru">
            Perpanjangan Sertifikat
        </p>
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
                },
                mounted() {
                    this.loadIdb();
                },
                methods: {
                    validate() {
                        let jenis = document.querySelector('input[name="step1_jenis_permohonan"]:checked');
                        if (jenis == null) throw "Pilih Jenis Permohonan"
                    },
                    async loadIdb() {
                        let currentData = await idb.pelanggan_permohonan
                            .where({name: "jenis_permohonan"})
                            .first()

                        console.log(currentData);

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
                        }
                    }
                }
            })
        })
    </script>
@endpush
