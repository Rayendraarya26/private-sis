<div class="row" id="vueStepFour">
    <div class="col-md-12" style="text-align: center">
        <h2>Pernyataan</h2>
    </div>
    <div class="col-md-4"></div>
    <div class="col-md-4" style="text-align: justify">
        <div style="cursor: pointer" @click="agreement = !agreement">
            <input type="checkbox" name="step4_agreement" id="step4_agreement" aria-label="Agreement"
                   v-model="agreement">
            Saya sudah melakukan pengecekan kembali data yang akan saya kirim, dan saya menyatakan bahwa data yang saya
            isikan benar
        </div>
        <div style="padding-top: 20px">
            <button :disabled="!agreement"
                    :class="{'btn': true, 'btn-primary':agreement, 'btn-outline-primary':!agreement,'btn-block':true}"
                    @click="submitPermohonan"
            >
                <i class="fad fa-paper-plane"></i> Kirim
            </button>
        </div>
    </div>
    <div class="col-md-4"></div>
</div>

@push('javascript')
    <script>
        $(document).ready(function () {
            window.vueStepFour = new Vue({
                el: "#vueStepFour",
                data: {
                    agreement: false,
                },
                methods: {
                    submitPermohonan() {
                        swalWithBootstrapButtons({
                            title: `Kirim Permohonan ?`,
                            text: `Proses akan berjalan beberapa saat, mohon bersabar untuk menunggu`,
                            type: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Kirim',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(async (result) => {
                            if (result.value) {
                                let formData = new FormData();
                                const dataPertanyaanTambahan = document.querySelector("#step3_pertanyaan_tambahan").files[0];
                                const dataPermohonan = await idb.pelanggan_permohonan.where({name: "jenis_permohonan"}).first();
                                const dataKomoditas = await window.idb.pelanggan_permohonan_komoditas.toArray();

                                formData.append("pertanyaan_tambahan", dataPertanyaanTambahan)
                                formData.append("jenis_sertifikasi", dataPermohonan.value)
                                formData.append("data_komoditas", JSON.stringify(dataKomoditas))

                                $.ajax({
                                    url: `{{action("$module@store")}}`,
                                    type: 'post',
                                    processData: false,
                                    contentType: false,
                                    data: formData,
                                    success: function (res) {
                                        toastCenter({
                                            type: 'success',
                                            title: res.message
                                        })
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
                            }
                        });
                    },
                }
            })
        });
    </script>
@endpush
