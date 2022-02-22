@extends("layouts.layout_app")

@section('title', 'Input Tim Audit Tahap 2')

@section('content')
    <div class="dt-content">
		<div class="col-xl-12">
			<a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"><i class="fad fa-arrow-left"></i> Kembali</a>
			<a class="btn btn-sm btn-success" href="#" onClick="confirmAjukan()" style="margin-bottom: 20px"><i class="fad fa-check"></i> Ajukan Tim</a>
			<div class="row">
				<div class="col-xl-6">
					<div class="card">
					  <div class="card-body p-0">									
						<table class="table">
							<tbody>
								<tr><td>Jenis Jadwal</td><td>: {{$dataJadwal->jadw_jenis}}</td></tr>
								<tr><td>Tanggal Jadwal</td><td>: {{$dataJadwal->jadw_tanggal_mulai}} s/d {{$dataJadwal->jadw_tanggal_selesai}}</td></tr>
								<tr><td>Nama Perusahaan</td><td>: {{$dataJadwal->cust_nama}}</td></tr>
								<tr><td>Alamat Perusahaan</td><td>: {{$dataJadwal->cust_alamat}}</td></tr>
								<tr><td>No. Referensi</td><td>: {{$dataJadwal->jadw_audit_nomor_referensi}}</td></tr>
								<tr><td>Kode NACE</td><td>: {{$dataJadwal->jadw_audit_kode_nace}}</td></tr>
								<tr><td>EA Code</td><td>: {{$dataJadwal->jadw_audit_kode_ea}}</td></tr>
								<tr><td>Komoditas</td><td>: {{$dataJadwal->komodt_nama}}</td></tr>
							</tbody>
						</table>
					  </div>
					</div>
				</div>
				<div class="col-xl-6">
					<div class="card">
					  <div class="card-body p-0">
							<table class="table">
								<tbody>
									<tr><td>Ruang Lingkup</td><td>: {{$dataJadwal->jadw_audit_ruang_lingkup}}</td></tr>
									<tr><td>Standar Acuan</td><td>: {{$dataJadwal->jadw_audit_standart_acuan}}</td></tr>
									<tr><td>Kegiatan</td><td>: {{$dataJadwal->jadw_audit_kegiatan}}</td></tr>
									<tr><td>Tujuan Audit</td><td>: {{$dataJadwal->jadw_audit_tujuan_audit}}</td></tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="col-xl-12">	
					<div class="dt-card">
						<div class="dt-card__header">
							<div class="dt-card__heading">
								<h3 class="dt-card__title">Data Tim</h3>
							</div>
						</div>
						<div class="dt-card__body">
							<div id="ttData" style="width:100%; min-width: 310px"></div>
							<div id="toolbar" style="padding: 10px 0 10px 20px">
								<div class="row">
									@if(authorized("{$module}@edit"))
										<div>
											<a href="javascript:void(0)" onclick="insert()" class="btn btn-outline-success btn-xs">
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
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection


@push("javascript")
    <script>
        $(function () {
            let dg = $('#ttData').edatagrid({
				method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid-jadwal-tim") }}&jadw_id={{$dataJadwal->jadw_id}}`,
                rownumbers: false,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
				pagination: false,
                clientPaging: false,
                // fitColumns: true,
                toolbar: '#toolbar',
                saveUrl: '{{url("$url/update")}}',
                updateUrl: '{{url("$url/update")}}',
                destroyUrl: '{{url("$url/update")}}',
				onError: function (index, row) {
					$.messager.alert('Informasi', 'Data tidak valid', 'warning');
					$('#ttData').datagrid({url: `{{ url("$url/ajax?action=datagrid-jadwal-tim") }}&jadw_id={{$dataJadwal->jadw_id}}`});
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
					if (row.jadw_tim_id == '') {
						$(this).datagrid('deleteRow', index);
					}
					$(this).datagrid('refreshRow', index);
				},
				onEndEdit:function(index,row){
					var ed_peg_id = $(this).datagrid('getEditor', {index: index,field: 'peg_nama'});
					var g_peg_id = $(ed_peg_id.target).combogrid('grid');	// get datagrid object
					var r_peg_id = g_peg_id.datagrid('getSelected');	// get the selected row
					if(r_peg_id !== null){
						row.peg_id = r_peg_id.peg_id;
						row.peg_nama = r_peg_id.peg_nama;
					}
					
					var itms_jadw_tim_posisi = $(this).datagrid('getEditor', {index: index,field: 'jadw_tim_posisi'});
					if(itms_jadw_tim_posisi !== null){
						row.jadw_tim_posisi = $(itms_jadw_tim_posisi.target).combobox('getValue');
					}
				},
				onBeginEdit:function(index,row){
					var editors = $(this).datagrid('getEditors', index);
					var ed_cb_posisi = $(editors[0].target);
					var ed_cb_peg = $(editors[1].target);
					var ed_kode = $(editors[2].target);
					
					ed_cb_posisi.combobox('options').onSelect = function(rData){
						var peg_id = ``;
						if(typeof row.peg_id !== 'undefined'){
							peg_id = `${row.peg_id}`;
						}
						ed_cb_peg.combogrid({
							url:`{{ url("$url/ajax?action=combogrid-pegawai") }}&posisi=${rData.id}&jadw_id={{$dataJadwal->jadw_id}}`,
							value:`${peg_id}`
						});  
					}
					
					ed_cb_peg.combogrid('options').onSelect = function(index, rowData){
						$(ed_kode).textbox({value:`${rowData.peg_kode}`});
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
                    {field: 'ck', checkbox: true, sortable: false},
                    {
                        field: 'action',
                        title: "",
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
					{field: 'is_new', hidden: true},
					{field: 'tipe', hidden: true},
					{field: 'jadw_id', hidden: true},
					{field: 'jadw_tim_id', hidden: true},
					{field: 'peg_id', hidden: true},
					{field: 'jadw_tim_posisi', title: 'Posisi', width: 100, sortable: true,
						editor:{
							type:'combobox',
							options:{
								valueField:'id',
								textField:'name',
								required:true,
								method: 'get',
								url:`{{ url("$url/ajax?action=combobox-posisi") }}`,
							}
						}
					},
                    {field: 'peg_nama', title: 'Nama', width: 300, sortable: true,
						editor:{
							type:'combogrid',
							options:{
								pageSize: '50',
								panelWidth: 650,
								pagination: true,
								idField: 'peg_id',
								nowrap: false,
								textField: 'peg_nama',
								editable: true,
								method: 'get',
								mode: 'remote',
								multiSort: true,
								fitColumns: true,
								required: false,
								columns: [[
									{field: 'peg_id', hidden: true},
									{field: 'peg_kode', hidden: true},
									{field: 'peg_nip', title: 'NIP', width: 100, sortable: true,},
									{field: 'peg_nama', title: 'Nama Pegawai', width: 250, sortable: true,},
									{field: 'peg_telp', title: 'Telp', width: 100, sortable: true,},
								]],
							}
						}
					},
                    {field: 'jadw_tim_kode', title: 'Kode', width: 100, sortable: true,editor: {type: 'textbox', options: {required: true}}},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'jadw_tim_posisi', type: 'textbox'},
                    {field: 'jadw_tim_kode', type: 'textbox'},
                ]);
        });
		
		function confirmAjukan() {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-success mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Ajukan Data ?`,
                text: "Mengajukan Tim Audit akan memberikan informasi ke client untuk memberikan informasi tim, apakah diterima atau tidak?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ajukan',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
				$.messager.progress(); 
                if (result.value) {
                    $.ajax({
                        url: `{{url("$url/update")}}`,
						data: { 
							'jadw_id': `{{$dataJadwal->jadw_id}}`,
							'cust_id': `{{$dataJadwal->cust_id}}`,
							'jadw_tanggal_mulai': `{{$dataJadwal->jadw_tanggal_mulai}}`,
							'jadw_tanggal_selesai': `{{$dataJadwal->jadw_tanggal_selesai}}`,
							'tipe': 'ajukan-tim' 
						},
						type: 'POST',
                        success: function (response) {
							$.messager.progress('close');
                            toastCenter({
                                type: 'success',
                                title: response.message
                            })
							setTimeout(() => location.href = "{{url("$url")}}", 100)
                        },
                        error: function (err) {
                            if (err.responseJSON.message) {
                                toastCenter({
                                    type: 'error',
                                    title: err.responseJSON.message
                                })
                            }
							$.messager.progress('close');
                        }
                    });
                }
            });
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
							idData.push(data.rows[i].jadw_tim_id);
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
					tipe:`data-tim`,
					jadw_tim_id:``,
					jadw_id:`{{$dataJadwal->jadw_id}}`,
				}
			});
			$('#ttData').datagrid('selectRow', index);
			$('#ttData').datagrid('beginEdit', index);
		}
    </script>
@endpush
