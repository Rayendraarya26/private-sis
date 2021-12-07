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
    <div class="col-md-12" style="padding-bottom: 20px">
        <div class="row">
		
            <div class="col-md-12">
				<div id="form-tambah" style="display:none;">
					<form action="#" id="form_id">
						<div class="form-group form-row" id="sertifikasi_komoditi">
							<label class="col-xl-3 col-form-label text-sm-left" >Pegawai</label>
							<div class="col-xl-8">
								<input class="form-control" id="peg_id" value="">
								<input type="hidden" class="form-control" id="thp1_tim_id">
								<input type="hidden" id="data_update" value="">
								<input type="hidden" class="form-control" id="tipe">
							</div>
						</div>
						
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" >Kode</label>
							<div class="col-xl-8">
								<input type="text" class="form-control" id="kode">
							</div>
						</div>
						
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" >Posisi</label>
							<div class="col-xl-8">
								<select class="form-control" id="posisi">
									<option value=""></option>
									<option value="ketua">Ketua</option>
									<option value="anggota">Anggota</option>
									<option value="observer">Observer</option>
								</select>
							</div>
						</div>
							
						<div class="col-md-12 aksi-button">
							<a href="#" class="btn btn-xs btn-primary" onClick="simpanAction()">
								<i class="fas fa-save"></i> Simpan
							</a>
							<a href="#" class="btn btn-xs btn-danger" onClick="cancelAction()">
								<i class="fas fa-close"></i> Batal
							</a>
						</div>
						<hr/>
				</div>
			</div>
            <div class="col-md-12">
				<div id="ttData" style="width:100%; min-width: 310px"></div>
				<div id="toolbar" style="padding: 10px 0 10px 20px">
					<div class="row">
						@if(authorized("{$module}@create"))
							<div>
								<a href="#" class="btn btn-outline-success btn-xs" onClick="addData()">
									<i class="fas fa-plus"></i> Tambah
								</a>
							</div>
							&nbsp;&nbsp;&nbsp;
						@endif							
						@if(authorized("{$module}@destroy"))
							<div class="datagrid-btn-separator"></div>
							&nbsp;&nbsp;&nbsp;
							<div>
								<a href="#" class="btn btn-outline-danger btn-xs" onClick="confirmDelete()">
									<i class="fas fa-trash"></i> Hapus
								</a>
							</div>
						@endif
					</div>
				</div>
            </div>
			<div class="col-md-12">
				<hr/>
				<template v-if="loading_submit">
					<div class="fa-3x" style="text-align: center">
						<i class="fas fa-spinner fa-spin" style="color: #0390DE"></i>
					</div>
				</template>
				<template v-else>
					<button :disabled="!status_submit"
							:class="{'btn': true, 'btn-primary':status_submit, 'btn-outline-primary':!status_submit,'btn-block':true}"
							@click="submitJadwal">
						<i class="fad fa-disk"></i> Simpan jadwal
					</button>
				</template>
			</div>
		</div>
    </div>
</div>

@push('javascript')
    <script>
		$(document).ready(function () {
            window.vueStepTwo = new Vue({
                el: "#vueStepTwo",
                data: {	
                    status_submit: true,
                    loading_submit: false,
                },
                mounted() {
                    setTimeout(() => {
                        const currentStep = $('#smartwizard').smartWizard("getStepIndex");
                        if (currentStep === 1) {
                            this.start();
                        }
                    }, 400)
                },
                methods: {
					async submitJadwal() {
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
								let formData = new FormData();
								// Step 1
								formData.append("tipe", 'update-jadwal')
								formData.append("aud_thp1_id", `{{$dataJadwal->aud_thp1_id}}`);
								formData.append("aud_thp1_tanggal_mulai", window.vueStepOne.aud_thp1_tanggal_mulai);
								formData.append("aud_thp1_tanggal_selesai", window.vueStepOne.aud_thp1_tanggal_selesai);
								formData.append("aud_thp1_tujuan", window.vueStepOne.aud_thp1_tujuan);
								formData.append("aud_thp1_standart_acuan", window.vueStepOne.aud_thp1_standart_acuan);

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
                    async start() {
                        setTimeout(async () => {
							$(".tab-content").height("100%");
							$('#ttData').datagrid({
								method: 'get',
								width: $(".tab-content").width()-20,
								height: document.documentElement.scrollHeight - 300,
								url: `{{ url("$url/ajax?action=datagrid-jadwal-tim") }}&aud_thp1_id={{$dataJadwal->aud_thp1_id}}`,
								rownumbers: false,
								nowrap: false,
								singleSelect: false,
								remoteFilter: true,
								multiSort: true,
								// fitColumns: true,
								toolbar: '#toolbar',
								pagination: true,
								pageSize: 50,
								clientPaging: false,
								frozenColumns: [[
									{field: 'ck', checkbox: true, sortable: false},
									{
										field: 'action',
										title: "Aksi",
										width: 80,
										align: 'center',
										formatter: function (val, row, index) {
											@if(authorized("{$module}@edit"))
											var e = `<a onClick="editItem(${index})" href="javascript:void(0)" class="btn btn-xs btn-primary btn-block">Edit</a>`;
											return e;
											@endif
										}
									}
								]],
								columns: [[
									{field: 'peg_nip', title: 'NIP', width: 120, sortable: true},
									{field: 'peg_nama', title: 'Nama', width: 250, sortable: true},
									{field: 'thp1_tim_kode', title: 'Kode', width: 100, sortable: true},
									{field: 'thp1_tim_posisi', title: 'Posisi', width: 150, sortable: true},
								]],
							});
						}, 1000);					
                    },
                }
            });
			
			
        });
		
		function setForm(){
			const thp1_tim_id = $('#thp1_tim_id').val();
			$('#peg_id').combogrid({
				pageSize: '50',
				panelWidth: 600,
				pagination: true,
				nowrap: false,
				idField: 'peg_id',
				textField: 'peg_nama',
				editable: true,
				url:'{{ url("$url/ajax?action=combogrid-pegawai") }}',
				method: 'get',
				mode: 'remote',
				multiSort: true,
				fitColumns: false,
				required: true,
				columns: [[
					{field: 'peg_id', hidden: true},
					{field: 'peg_nip', title: 'NIP', width: 200, sortable: true,},
					{field: 'peg_nama', title: 'Nama', width: 390, sortable: true,},
				]],
				onSelect: function (index, row) {
					$("#kode").val(`${row.peg_kode}`);
				},
			});
			$('#tipe').val(`update-tim`);
			$('#kode').val(``);
			$('#posisi').val(``);
				
			if(thp1_tim_id != ''){
				let data_update = $('#data_update').val();
				const obj = JSON.parse(`${data_update}`);
				$('#peg_id').combogrid('setValue',`${obj.peg_id}`);
				$('#kode').val(`${obj.thp1_tim_kode}`);
				$('#posisi').val(`${obj.thp1_tim_posisi}`);
			}
		}
		
		function simpanAction() {
			$.messager.progress(); 
			let formDataItem = new FormData();
			formDataItem.append("aud_thp1_id", `{{$dataJadwal->aud_thp1_id}}`);
			formDataItem.append("thp1_tim_id", $("#thp1_tim_id").val());
			formDataItem.append("tipe", $("#tipe").val());
			formDataItem.append("peg_id", $('#peg_id').combogrid('getValue'));
			formDataItem.append("kode", $('#kode').val());
			formDataItem.append("posisi", $('#posisi').val());
			
			$.ajax({
				url: `{{action("$module@update")}}`,
				type: 'post',
				processData: false,
				contentType: false,
				data: formDataItem,
				success: async function (res) {
					toastCenter({
						type: 'success',
						title: res.message
					})

					$('#ttData').datagrid('reload');
					$.messager.progress('close');
					cancelAction();
				},
				error: function (xhr) {
					if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
					else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
					$.messager.progress('close');
				}
			});
		}
		
		function cancelAction() {
			setTimeout(async () => {
				$('#form_id').trigger("reset");
				$('#thp1_tim_id').val("");
				$('#data_update').val("update-tim");
				$('#tipe').val("");
				$("#form-tambah").hide();
				$(".tab-content").height("100%");
			}, 500);	
			return false;
		}
		
		function editItem(index) {
			setTimeout(async () => {
				$('#form_id').trigger("reset");
				$("#form-tambah").show();
				$(".tab-content").height("100%");
				
				var row = $('#ttData').datagrid('getRows')[index];
				const myJSON = JSON.stringify(row); 
				
				$('#thp1_tim_id').val(`${row.thp1_tim_id}`);
				$('#data_update').val(`${myJSON}`);
				$('#tipe').val("update-tim");
				setForm();
			}, 500);
		}
		
		function addData() {
			setTimeout(async () => {
				$('#form_id').trigger("reset");
				$("#form-tambah").show();
				$(".tab-content").height("100%");
				$('#thp1_tim_id').val("");
				$('#data_update').val("");
				$('#tipe').val("update-tim");
				setForm();
			}, 500);						
		}
		
		function confirmDelete() {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-success mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Menghapus Data ?`,
                text: "Menghapus data bersifat permanen dan tidak dapat di kembalikan",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
					var idData = []; 
					var data = $('#ttData').datagrid('getData');
					var opts = $('#ttData').datagrid('options');
					for (var i = 0; i < data.rows.length; i++) {
						var tr = opts.finder.getTr($('#ttData')[0],i);
						var atLeastOneIsChecked = tr.find('input[type=checkbox]:checked').length > 0;
						if(atLeastOneIsChecked == true){
							idData.push(data.rows[i].thp1_tim_id);
						}
					}
                    $.ajax({
                        url: `{{url("$url/delete")}}`,
						data: { 'ids[]': idData, 'tipe': 'data-tim' },
						type: 'DELETE',
                        success: function (response) {
                            toastCenter({
                                type: 'success',
                                title: response.message
                            })

                            let dg = $('#ttData');
                            dg.datagrid('reload');
                        },
                        error: function (err) {
                            if (err.responseJSON.message) {
                                toastCenter({
                                    type: 'error',
                                    title: err.responseJSON.message
                                })
                            }
                        }
                    });
                }
            });
        }
		
		
    </script>
@endpush
