@extends('layouts.layout_app')

@section('title', 'Edit Billing')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default"
                   href="{{url("$url")}}"
                   style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali
                </a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Edit Billing</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="row">
                            <div class="col-lg-12">
                                @if ($errors->any())
                                    <div class="alert alert-danger" role="alert">
                                        {!! implode('', $errors->all('<li>:message</li>')) !!}
                                    </div>
                                @endif
                                @if(session('message'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('message') }}
                                    </div>
                            @endif

                            <!--
                                    ada 3 cara:
                                    action(): mengarah ke controller
                                    url(): mengarah ke lokasi url
                                    route(): mengarah ke nama route
                                -->
                                <form method="post" action="{{action("$module@update")}}">
                                    <!-- Security CSRF TOKEN -->
                                    @csrf
                                    <input type="hidden" name="tipe" value="data">
                                    <input type="hidden" name="bil_id" value="{{old('bil_id') ?? $data_billing->bill_id}}">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="cust_nama">Nama Pelanggan*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan nama badan hukum ..." type="text" name="cust_nama" id="cust_nama" value="{{old('cust_nama') ?? $data_billing->cust_nama}}" readonly>
                                        </div>
                                    </div>
									
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="bill_nomor_billing">Nomor Billing*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="text" name="bill_nomor_billing" id="bill_nomor_billing" value="{{old('bill_nomor_billing') ?? $data_billing->bill_nomor_billing}}">
                                        </div>
                                    </div>
									
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="bill_billing_date">Tanggal Billing*</label>
                                        <div class="col-sm-3">
                                            <input class="form-control" type="text" name="bill_billing_date" id="bill_billing_date" value="{{old('bill_billing_date') ?? $data_billing->bill_billing_date?->format('Y-m-d')}}">
                                        </div>
                                    </div>
									
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="bill_due_date">Jatuh Tempo*</label>
                                        <div class="col-sm-3">
                                            <input class="form-control" type="text" name="bill_due_date" id="bill_due_date" value="{{old('bill_due_date') ?? $data_billing->bill_due_date?->format('Y-m-d')}}">
                                        </div>
                                    </div>
									
									<div class="form-group form-row">
										<label class="col-xl-3 col-form-label text-sm-left" for="bill_invoice_file" >File Invoice</label>
										<div class="col-xl-8">
											<input type="file" class="form-control" aria-label="File Invoice" accept="application/pdf" name="bill_invoice_file" id="bill_invoice_file">
											<h5>File Lama <a href="{{url($data_billing->bill_invoice_file)}}" target="_blank">Download</a></h5>
											<small><span>Upload file harus berjenis PDF, silahkan isikan kosong jika tidak ingin mengganti</span></small>
										</div>
									</div>
									
									<div class="form-group form-row">
										<div id="ttData" style="width:100%; min-width: 310px"></div>
										<div id="toolbar" style="padding: 10px 0 10px 20px">
											<div class="row">
												@if(authorized("{$module}@edit"))
													<div>
														<a href="#" onclick="insert()" class="btn btn-outline-success btn-xs">
															<i class="fas fa-plus"></i> Tambah
														</a>
													</div>
													&nbsp;&nbsp;&nbsp;
												@endif							
												@if(authorized("{$module}@destroy"))
													<div class="datagrid-btn-separator"></div>
													&nbsp;&nbsp;&nbsp;
													<div>
														<a class="btn btn-outline-danger btn-xs" href="#" onclick="confirmDelete()">
															<i class="fas fa-trash"></i> Hapus
														</a>
													</div>
												@endif
											</div>
										</div>
									</div>
									
                                    <div class="form-buttons-w">
                                        <button class="btn btn-success" type="submit">
                                            <i class="fas fa-save"></i> Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('javascript')
    <script>
		function myformatter(date){
            var y = date.getFullYear();
            var m = date.getMonth()+1;
            var d = date.getDate();
            return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
        }
        function myparser(s){
            if (!s) return new Date();
            var ss = (s.split('-'));
            var y = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var d = parseInt(ss[2],10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                return new Date(y,m-1,d);
            } else {
                return new Date();
            }
        }
		
        $(document).ready(function () {
            $('#bill_due_date').datebox({
				required:true,
				formatter:myformatter,
				parser:myparser,
				value:`{{old('bill_due_date') ?? $data_billing->bill_due_date?->format('Y-m-d')}}`,
			});
			
			$('#bill_billing_date').datebox({
				required:true,
				formatter:myformatter,
				parser:myparser,
				value:`{{old('bill_billing_date') ?? $data_billing->bill_billing_date?->format('Y-m-d')}}`,
			});
			
			let dg = $('#ttData').edatagrid({
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid-billing-items") }}&bill_id={{$data_billing->bill_id}}`,
                rownumbers: false,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
                // fitColumns: true,
                toolbar: '#toolbar',
                saveUrl: '{{url("$url/update")}}',
                updateUrl: '{{url("$url/update")}}',
                destroyUrl: '{{url("$url/update")}}',
                pagination: false,
				onError: function (index, row) {
					$.messager.alert('Informasi', 'Data tidak valid', 'warning');
					$('#ttData').datagrid('reload');
				},
				onLoadSuccess: function (data) {
					var opts = $(this).datagrid('options');
					for (var i = 0; i < data.rows.length; i++) {
					}
				},
				onBeforeEdit: function (index, rows) {
					rows.editing = true;
					$(this).datagrid('refreshRow', index);
					$("input[name='ck']").prop('checked', false);				
				},
				onAfterEdit: function (index, row) {
					/* row.editing = false;
					$(this).datagrid('refreshRow', index); */
					$('#ttData').datagrid('reload');
				},
				onCancelEdit: function (index, row) {
					row.editing = false;
					if (row.itms_bil_id == '') {
						$(this).datagrid('deleteRow', index);
					}
					$(this).datagrid('refreshRow', index);
				},
				onEndEdit:function(index,row){
					var ed_mohon_id = $(this).datagrid('getEditor', {index: index,field: 'mohon_id'});
					var g_mohon_id = $(ed_mohon_id.target).combogrid('grid');	// get datagrid object
					var r_mohon_id = g_mohon_id.datagrid('getSelected');	// get the selected row
					if(r_mohon_id !== null){
						row.mohon_id = r_mohon_id.mohon_id;
					}
					
					var itms_bil_tipe = $(this).datagrid('getEditor', {index: index,field: 'itms_bil_tipe'});
					if(itms_bil_tipe !== null){
						row.itms_bil_tipe = $(itms_bil_tipe.target).combobox('getValue');
					}
				},
				onBeginEdit:function(index,row){
					var editors = $(this).datagrid('getEditors', index);
					var ed_itms_bil_tipe = $(editors[0].target);
					var ed_mohon_id  = $(editors[1].target);
					var ed_cust_sert_id  = $(editors[2].target);
					var ed_itms_bil_desc  = $(editors[3].target);
					var ed_itms_bil_total  = $(editors[4].target);
					
					ed_itms_bil_tipe.combobox('setValue',``);
					if(row.itms_bil_tipe != ''){
						ed_itms_bil_tipe.combobox('setValue',`${row.itms_bil_tipe}`);
					}
					
					ed_itms_bil_tipe.combobox('options').onSelect = function(row_tipe){
						if(row_tipe.id === 'sertifikasi' || row_tipe.id === 're-sertifikasi'){
							if (ed_mohon_id){
								$(ed_mohon_id).combogrid({
									url:`{{ url("$url/ajax?action=combogrid-permohonan") }}&cust_id={{$data_billing->cust_id}}&jenis_status=${row_tipe.id}`,
									required: true,
									value: row.mohon_id,
								})
							}
							
							if (ed_cust_sert_id){
								$(ed_cust_sert_id).combogrid({
									url:``,
									required: false,
								})
							}
						}
						else if(row_tipe.id === 'surveilans'){
							if (ed_cust_sert_id){
								$(ed_cust_sert_id).combogrid({
									url:`{{ url("$url/ajax?action=combogrid-sertifikat") }}&cust_id={{$data_billing->cust_id}}`,
									required: true,
									value: row.cust_sert_id,
								})
							}
							
							if (ed_mohon_id){
								$(ed_mohon_id).combogrid({
									url:``,
									required: false,
								})
							}
						}
						else{
							if (ed_mohon_id){
								$(ed_mohon_id).combogrid({
									url:``,
									required: false,
								})
							}
							
							if (ed_cust_sert_id){
								$(ed_cust_sert_id).combogrid({
									url:``,
									required: false,
								})
							}
						}
					}		
					
					ed_mohon_id.combogrid('options').onSelect = function(index, rowData){
						$(ed_itms_bil_total).numberbox({
														min:0,
														precision:2,
														value:`${rowData.mohon_harga_permohonan}`
													});
						$(ed_itms_bil_desc).textbox({
														value:`${rowData.deskripsi}`
													});
					}					
					
					if(row.is_new === ''){
						var edId = $(this).datagrid('getEditor', {index:index,field:'itms_bil_id'});
						if(edId != null){
							$(edId.target).validatebox('disable');
						}
					}
					
					dgEdit = $(this);
						for(var i=0; i<19; i++){
							var ed = dgEdit.datagrid('getEditors',index)[i];
							if (!ed){return;}
							var t = $(ed.target);
							if (t.hasClass('textbox-f')){
								t = t.textbox('textbox');
							}
							t.bind('keydown', function(e){
								if (e.keyCode == 13){
									dgEdit.datagrid('endEdit', index);
								} else if (e.keyCode == 27){
									dgEdit.datagrid('cancelEdit', index);
								}
							})
						}
				},
                frozenColumns: [[
					{field: 'is_new', hidden: true},
					{field: 'bill_id', title: '', hidden: true},
					{field: 'itms_bil_id', title: '', hidden: true},
                    {field: 'ck', checkbox: true, sortable: false},
                    {
                        field: 'action',
                        title: "Aksi",
                        width: 110,
                        align: 'center',
                        formatter: function (val, row) {
							if (row.editing) {
								@if(authorized("{$module}@edit"))
								var s = '<a onclick="saverow(this)" href="javascript:void(0)" class="btn btn-xs btn-success">Simpan</a>';
								var c = `<a onclick="cancelrow(this)" href="javascript:void(0)" class="btn btn-xs btn-danger">Batal</a>`;
								return '<div class="btn-group">' + s + c + '</div>';
								@endif
							} else {
								@if(authorized("{$module}@edit"))
								var e = `<a  onclick="editrow(this)" href="javascript:void(0)" class="btn btn-xs btn-primary btn-block">Edit</a>`;
								return e;
								@endif
							}
                        }
                    }
                ]],
                columns: [[
                    {field: 'itms_bil_tipe', title: 'Tipe', width: 100, sortable: true,
						editor:{
							type:'combobox',
							options:{
								valueField:'id',
								textField:'name',
								required:true,
								method: 'get',
								url:`{{ url("$url/ajax?action=combobox-tipe") }}`,
							}
						}
					},
                    {field: 'mohon_id', title: 'ID<br/>Permohonan', width: 100, sortable: true,
						editor:{
							type:'combogrid',
							options:{
								pageSize: '50',
								panelWidth: 650,
								pagination: true,
								idField: 'id',
								nowrap: false,
								textField: 'id',
								editable: true,
								method: 'get',
								mode: 'remote',
								multiSort: true,
								fitColumns: true,
								required: false,
								columns: [[
									{field: 'id', hidden: true},
									{field: 'nama', title: 'Permohonan', width: 250, sortable: true,},
								]],
							}
						}
					},
					{field: 'cust_sert_id', title: 'ID<br/>Sertifikat', width: 100, sortable: true,
						editor:{
							type:'combogrid',
							options:{
								pageSize: '50',
								panelWidth: 650,
								pagination: true,
								idField: 'id',
								nowrap: false,
								textField: 'id',
								editable: true,
								method: 'get',
								mode: 'remote',
								multiSort: true,
								fitColumns: true,
								required: false,
								columns: [[
									{field: 'id', hidden: true},
									{field: 'nama', title: 'Nama Sertifikat', width: 250, sortable: true,},
									{field: 'cust_sert_nomor_referensi', title: 'No. Referensi', width: 250, sortable: true,},
									{field: 'cust_sert_tgl_sertifikat_awal', title: 'Tgl. Awal', width: 100, sortable: true,},
									{field: 'cust_sert_tgl_sertifikat_perubahan', title: 'Tgl. Perubahan', width: 100, sortable: true,},
								]],
							}
						}
					},
                    {field: 'itms_bil_desc', title: 'Deskripsi', width: 320, sortable: true,editor: {type: 'textbox', options: {required: true}}},
                    {field: 'itms_bil_total', title: 'Total(Rp.)', width: 100, sortable: true,editor: {type: 'numberbox', options: {required: true}}, align:'right',},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'itms_bil_total', type: 'label'},
                    {field: 'mohon_id', type: 'label'},
                    {field: 'cust_sert_id', type: 'label'},
                ]);
        });
		
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
							idData.push(data.rows[i].itms_bil_id);
						}
					}
                    $.ajax({
                        url: `{{url("$url/delete")}}`,
						data: { 'ids[]': idData, 'tipe': 'data_items' },
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
		
		function getRowIndex(target) {
			var tr = $(target).closest('tr.datagrid-row');
			return parseInt(tr.attr('datagrid-row-index'));
		}

		function editrow(target) {
			$('#ttData').datagrid('beginEdit', getRowIndex(target));
		}
		
		function saverow(target) {
			$('#ttData').edatagrid('endEdit', getRowIndex(target));
			//$('#ttData').datagrid('reload');
		}

		function cancelrow(target) {
			$('#ttData').edatagrid('cancelEdit', getRowIndex(target));
		}

		function insert() {
			var row = $('#ttData').datagrid('getSelected');
			index = 0;
			$('#ttData').datagrid('insertRow', {
				index: index,
				row: {
					is_new:`true`,
					tipe:`data-billing`,
					itms_bil_id:``,
					bill_id:`{{$data_billing->bill_id}}`,
				}
			});
			$('#ttData').datagrid('selectRow', index);
			$('#ttData').datagrid('beginEdit', index);
		}
    </script>
@endpush

