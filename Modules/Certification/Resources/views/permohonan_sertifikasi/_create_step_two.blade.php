<div class="row" id="vueStepTwo">
    <div class="col-md-4"></div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="step2_jenis_sertifikasi">Jenis Sertifikasi</label>
            <input id="step2_jenis_sertifikasi" name="step2_jenis_sertifikasi" class="form-control">
        </div>
    </div>
    <div class="col-md-4"></div>

    <div class="col-md-12" v-if="jenis_sertifikasi_id != null">
        <table class="table">
            <thead>
            <tr>
                <th>No</th>
                <th>Dokumen</th>
                <th>Upload</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(dds, idx) in data_dokumen_sertifikasi">
                <td>@{{ idx + 1 }}</td>
                <td>@{{ dds.dt_name }}
                    <span v-if="dds.dt_sample"><a :href="dds.dt_sample">Download Sample</a></span>
                </td>
                <td>
                    <input type="file" :name="'dokumen'+dds.dt_id" :id="'dokumen'+dds.dt_id"
                           @change="uploadDokumen(dds.dt_id)" accept="application/pdf">
                </td>
            </tr>
            </tbody>
        </table>
        <button @click="doTest">Tes</button>
    </div>
</div>

@push('javascript')
    <script>
        $(document).ready(function () {
            window.vueStepTwo = new Vue({
                el: "#vueStepTwo",
                data: {
                    data_dokumen_sertifikasi: [],

                    jenis_sertifikasi_id: null,
                    jenis_sertifikasi_text: "--Pilih Jenis Sertifikasi--",
                    dokumen_upload: [],
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
                    start() {
                        setTimeout(() => this.setComboDataSertifikasi(), 500)
                    },
                    validate() {

                    },
                    doTest() {
                        console.log(this.dokumen_upload)
                    },
                    uploadDokumen(id) {
                        const el = document.querySelector("#dokumen" + id);
                        const dt_upload = {"id": id, "dokumen": el.files[0]};
                        if (dt_upload.dokumen.type !== "application/pdf") {
                            swalWithBootstrapButtons({
                                title: `Validasi`,
                                text: "Dokumen harus bertipe PDF",
                                type: 'warning'
                            })
                            $("#dokumen" + id).val("")
                        }

                        this.dokumen_upload.push(dt_upload)
                    },
                    resetUploadDokumen() {
                        if (this.data_dokumen_sertifikasi.length > 0) {
                            this.data_dokumen_sertifikasi.map(e => {
                                $("#dokumen" + e.dt_id).val("")
                            });
                            this.dokumen_upload = [];
                        }
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
                                self.resetUploadDokumen();
                                self.getDokumenSertifikasi();
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
