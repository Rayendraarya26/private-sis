<div class="row" id="vueStepTree">
    <div class="col-md-12">
         <h3>File Invoice <small aria-label="Silahkan inputkan file invoice" class="custom-cooltipz" data-cooltipz-size="large" data-cooltipz-dir="right"><i class="fal fa-folder"></i></small></h3>
    </div>
	<div class="col-md-12">
			<div class="form-group form-row" id="data_permohonan">
				<label class="col-xl-3 col-form-label text-sm-left" for="mohon_id" >File Invoice</label>
				<div class="col-xl-8">
					<input type="file" class="form-control" aria-label="File Invoice"
				   @change="validateUploadFileInvoice" accept="application/pdf"
				   name="bill_invoice_file" id="bill_invoice_file">
			<small><span>Upload file harus berjenis PDF</span></small>
				</div>
			</div>
			<div class="col-md-12">
				<template v-if="loading_submit">
					<div class="fa-3x" style="text-align: center">
						<i class="fas fa-spinner fa-spin" style="color: #0390DE"></i>
					</div>
				</template>
				<template v-else>
					<button :disabled="!agreement"
							:class="{'btn': true, 'btn-primary':agreement, 'btn-outline-primary':!agreement,'btn-block':true}"
							@click="submitBilling">
						<i class="fad fa-disk"></i> Simpan
					</button>
				</template>
			</div>
	</div>
</div>

@push('javascript')
    <script>
        $(document).ready(function () {
            window.vueStepTree = new Vue({
                el: "#vueStepTree",
                data: {
                    bill_invoice_file: null,
                    agreement: false,
                    loading_submit: false,
                },
                methods: {
                    validateUploadFileInvoice(event) {
                        let uploaded = event.target.files[0];
                        if (uploaded.type !== "application/pdf") {
                            swalWithBootstrapButtons({
                                title: `Validasi`,
                                text: "File harus bertipe PDF",
                                type: 'warning',
                            })

                            $("#bill_invoice_file").val("")
                        }
						else{
							this.agreement = true
						}
                    },
                    submitBilling() {
                        swalWithBootstrapButtons({
                            title: `Simpan Data ?`,
                            text: `Proses akan berjalan beberapa saat, mohon bersabar untuk menunggu`,
                            type: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Simpan',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(async (result) => {
                            if (result.value) {
								if ($.trim($("#bill_invoice_file").val()) === "") {
									throw "Silahkan Unggah File Invoice";
								}
								else{
									let formData = new FormData();
									// Step 1
									const dataPelanggan = window.vueStepOne.cust_id;
									formData.append("cust_id", dataPelanggan)
									const currentaData = await idb.pelanggan_permohonan.where({name: "billing"}).first();
									if (currentaData != null) {
										formData.append("bill_nomor_billing", currentaData.value.bill_nomor_billing)
										formData.append("bill_billing_date", currentaData.value.bill_billing_date)
										formData.append("bill_due_date", currentaData.value.bill_due_date)
									}
									// Step 2
									const dataItems = window.vueStepTwo.bill_items;
									formData.append("data_billing_item", JSON.stringify(dataItems))

									// Step 3
									const dataInvoiceFile = document.querySelector("#bill_invoice_file").files[0];
									formData.append("bill_invoice_file", dataInvoiceFile)

									
									// Submit Permohonan
									this.loading_submit = true;
									let self = this;
									$.ajax({
										url: `{{action("$module@store")}}`,
										type: 'post',
										processData: false,
										contentType: false,
										data: formData,
										success: async function (res) {
											toastCenter({
												type: 'success',
												title: res.message
											})

											await window.idb.pelanggan_permohonan.clear();
											await window.idb.bill_data_itms.clear();
											setTimeout(() => location.href = "{{url("$url")}}", 1000)
										},
										error: function (xhr) {
											self.loading_submit = false;
											if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
											else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
										}
									});
								}
                            }
                        });
                    },
                }
            })
        });
    </script>
@endpush
