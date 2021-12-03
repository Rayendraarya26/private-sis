<div class="row" id="vueStepOne">
    <template v-for="(data, idx) in data_pengajuan">
        <div class="col-md-12" style="text-align: center; padding-top: 20px">
            <h3>Pengajuan @{{ idx + 1 }}</h3>
        </div>
        <div class="col-md-6 col-xs-6">
            <div style="justify-content: center; align-items: center; horiz-align: center">
                <img src="{{asset('images/sertifikasi-asset/pengajuan_baru.jpg')}}"
                     alt="pengajuan baru" class="step1_image">
                <p style="text-align: center">
                    <input type="radio" :id="'step1_jenis_permohonan_baru' + idx"
                           :name="'step1_jenis_permohonan' + idx" value="baru"
                           aria-label="Pengajuan Sertifikat Baru" disabled>
                    Pengajuan Sertifikat Baru
                </p>
            </div>
        </div>
        <div class="col-md-6 col-xs-6">
            <div>
                <img src="{{asset('images/sertifikasi-asset/pengajuan_lama.jpg')}}"
                     alt="pengajuan baru" class="step1_image">
                <p style="text-align: center">
                    <input type="radio" :id="'step1_jenis_permohonan_lama' + idx"
                           :name="'step1_jenis_permohonan' + idx" value="lama"
                           aria-label="Pengajuan Sertifikat Baru" disabled>
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
                        @foreach($dataPemohon->sis_permohonan_details as $key => $permohonan)

                            this.data_pengajuan.push({
                            mohon_det_id: `{{$permohonan?->mohon_det_id}}`,
                            jenis_pengajuan: `{{$permohonan?->mohon_det_jenis_status}}`,
                            sertifikat_lama_id: `{{$permohonan?->sis_pelanggan_sertifikasi?->cust_sert_id}}`, // cust_sert_id
                            sertifikat_lama_text: `{{$permohonan->sis_pelanggan_sertifikasi?->cust_sert_nomor_sertifikat}}`, // cust_sert_nomor_sertifikat
                            sertifikat_lama_data: [],

                            master_sertifikat_id: `{{$permohonan?->master_sertifikasi?->sert_id}}`, // sert_id
                            master_sertifikat_text: `{{$permohonan?->master_sertifikasi?->sert_nama}}`, // sert_nama
                            master_sertifikat_is_product: `{{$permohonan?->master_sertifikasi?->sert_is_product}}`, // sert_is_product
                            data_komoditas: [],
                        })

                        @foreach($permohonan->sis_permohonan_komoditis as $komoditi)
                        @php
                            $name = 'komoditas_'.$permohonan->mohon_det_id . $loop->index;
                        @endphp
                        let {{$name}} = {
                            'komoditi_id': `{{ $komoditi->master_komoditi->komodt_id }}`,
                            'komoditi_nama': `{{ $komoditi->master_komoditi->komodt_nama }}`,
                            'sni': `{{ $komoditi->mohon_kmditi_sni }}`,
                            'merk': `{{ $komoditi->mohon_kmditi_merk }}`,
                            'tipe': `{{ $komoditi->mohon_kmditi_tipe }}`,
                            'ukuran': `{{ $komoditi->mohon_kmditi_ukuran }}`,
                            'produksi_tahunan': `{{ $komoditi->mohon_kmditi_kapasitas_produksi_tahunan }}`,
                            'satuan_produksi': `{{ $komoditi->mohon_kmditi_kapasitas_produksi_tahunan_satuan }}`,
                        };

                        this.data_pengajuan[{{$key}}].data_komoditas.push({{$name}})

                        @endforeach

                        setTimeout(async () => {
                            await this.setJenisPermohonan({{$key}}, `{{$permohonan->mohon_det_jenis_status}}`) // lama|baru
                        }, 1000)
                        @endforeach
                        setTimeout(() => $(".tab-content").height("100%"), 2000);
                    },

                    validate() {
                        this.data_pengajuan.map((e, idx) => {
                            let jenis = document.querySelector(`input[name="step1_jenis_permohonan${idx}"]:checked`);
                            if (jenis == null) throw `Pilih Jenis Permohonan (Pengajuan ${idx + 1})`
                            if (this.data_pengajuan[idx].jenis_pengajuan == "lama" && this.data_pengajuan[idx].sertifikat_lama_id == null) throw `Pilih Sertifikat (Pengajuan ${idx + 1})`
                        })
                    },
                    async setJenisPermohonan(pengajuanIndex, tipe) {
                        this.data_pengajuan[pengajuanIndex].jenis_pengajuan = tipe;
                        if (tipe === "baru") {
                            $("#step1_jenis_permohonan_baru" + pengajuanIndex).prop('checked', true);
                            $("#step1_jenis_permohonan_lama" + pengajuanIndex).prop('checked', false);
                        } else {
                            $("#step1_jenis_permohonan_baru" + pengajuanIndex).prop('checked', false);
                            $("#step1_jenis_permohonan_lama" + pengajuanIndex).prop('checked', true);
                            setTimeout(async () => {
                                await this.setComboSertifikatLama(pengajuanIndex);
                                $('#step1_sertifikat_lama' + pengajuanIndex).combogrid('setValue', this.data_pengajuan[pengajuanIndex].sertifikat_lama_id)
                                $(".tab-content").height("100%");
                            }, 500);
                        }
                    },
                    setComboSertifikatLama(pengajuanIndex) {
                        let self = this;
                        let url  = `{{ url("$url/ajax?action=combogrid_sertifikat_lama") }}`
                        if (this.data_pengajuan[pengajuanIndex].sertifikat_lama_id != null) {
                            url += "&q=" + this.data_pengajuan[pengajuanIndex].sertifikat_lama_text;
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
                        });
                    },
                }
            })
        })
    </script>
@endpush
