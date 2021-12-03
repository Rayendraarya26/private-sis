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
            <template v-if="loading_submit">
                <div class="fa-3x" style="text-align: center">
                    <i class="fas fa-spinner fa-spin" style="color: #0390DE"></i>
                </div>
            </template>
            <template v-else>
                <button :disabled="!agreement"
                        :class="{'btn': true, 'btn-primary':agreement, 'btn-outline-primary':!agreement,'btn-block':true}"
                        @click="submitPermohonan"
                >
                    <i class="fad fa-save"></i> Simpan
                </button>
            </template>
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
                    loading_submit: false,
                },
                methods: {
                    submitPermohonan() {
                        swalWithBootstrapButtons({
                            title: `Simpan Permohonan ?`,
                            text: `Proses akan berjalan beberapa saat, mohon bersabar untuk menunggu`,
                            type: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Kirim',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(async (result) => {
                            if (result.value) {
                                let formData = new FormData();
                                formData.append("mohon_id", {{$dataPemohon->mohon_id}})

                                // Step 1
                                const dataPengajuan = window.vueStepOne.data_pengajuan;
                                formData.append("data_pengajuan", JSON.stringify(dataPengajuan))

                                // Step 2
                                const dataSertifikasi = window.vueStepTwo.data_sertifikat;
                                formData.append("data_sertifikat", JSON.stringify(dataSertifikasi))

                                // Step 3
                                const dataPertanyaanTambahan = document.querySelector("#step3_pertanyaan_tambahan").files[0];
                                if (dataPertanyaanTambahan != null) {
                                    formData.append("pertanyaan_tambahan", dataPertanyaanTambahan)
                                }


                                // Submit Permohonan
                                this.loading_submit = true;
                                let self = this;
                                $.ajax({
                                    url: `{{action("$module@update")}}`,
                                    type: 'post',
                                    processData: false,
                                    contentType: false,
                                    data: formData,
                                    success: async function (res) {
                                        toastCenter({
                                            type: 'success',
                                            title: res.message
                                        })

                                        setTimeout(() => location.href = "{{url("$url")}}", 1000)
                                    },
                                    error: function (xhr) {
                                        self.loading_submit = false;
                                        if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                                        else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
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
