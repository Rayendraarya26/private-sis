		$(function () {
            let dg = $('#ttDataJadwal').datagrid({
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid-jadwal-audit") }}&id_reg={{$data_permohonan['id_reg']}}`,
                toolbar: '#toolbarJadwal',
                rownumbers: false,
                nowrap: false,
                singleSelect: false,
                remoteSort: false,
                remoteFilter: false,
                multiSort: true,
                pagination: false,
                frozenColumns: [[
                    {field: 'ck', checkbox: true, sortable: false},
                    {
                        field: 'action',
                        title: "",
                        width: 80,
                        align: 'center',
                        formatter: function (val, row, index) {
							var btnAksi = ``;
							btnAksi += `<a href="javascript:void(0)" class="btn btn-xs btn-success btn-block" onclick="editModalJadwal(${index})"><i class="fal fa-pencil"></i> Edit</a>`;
                            return `${btnAksi}`;
                        }
                    }
                ]],
                columns: [[
                    {
						field: 'jadwal_awal', title: 'Jadwal Awal', width: 250, sortable: true,
						formatter: function (val, row, index) {
							var date = new Date(val),
								dformat = ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' +  ((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + date.getFullYear();
							return dformat;
						}
					},
                    {
						field: 'jadwal_akhir', title: 'Jadwal Awal', width: 250, sortable: true,
						formatter: function (val, row, index) {
							var date = new Date(val),
								dformat = ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' +  ((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + date.getFullYear();
							return dformat;
						}
					},
                    {field: 'jml_hari', title: 'Jumlah Hari', width: 150, sortable: true},
					{field: 'id_audit', hidden: true},
					{field: 'id_reg', hidden: true},
                ]],
				onBeforeLoad: function () {
                    
                },
                onLoadSuccess: function (data) {
					var opts = $(this).datagrid('options');
					for(var i=0; i<data.rows.length; i++){
						
					}
                },
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                ]);
        });
		
		
        function addModalJadwal() {
            $("#id_audit").val("");
            $("#modalFormJadwalAdd").modal('show');
        }
		
		function editModalJadwal(index) {
			var row = $('#ttDataJadwal').datagrid('getRows')[index];
			$("#edit_id_audit").val(row.id_audit);
			$("#edit_id_reg").val(row.id_reg);
			$("#edit_jml_hari").val(row.jml_hari);
			$("#edit_jadwal_awal").datebox('setValue', `${row.jadwal_awal}`);
			$("#edit_jadwal_akhir").datebox('setValue', `${row.jadwal_akhir}`);
            $("#modalFormJadwalEdit").modal('show');
            $("#modalFormJadwalEditTitle").html(`Edit Jadwal #${row.id_audit}`);
        }
		
		function confirmDeleteJadwal() {
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
					var data = $('#ttDataJadwal').datagrid('getData');
					var opts = $('#ttDataJadwal').datagrid('options');
					for (var i = 0; i < data.rows.length; i++) {
						var tr = opts.finder.getTr($('#ttDataJadwal')[0],i);
						var atLeastOneIsChecked = tr.find('input[type=checkbox]:checked').length > 0;
						if(atLeastOneIsChecked == true){
							idData.push(data.rows[i].id_audit);
						}
					}
                    $.ajax({
                        url: `{{url("$url/destroyJadwal")}}`,
						data: { 'ids[]': idData },
						type: 'DELETE',
                        success: function (response) {
                            toastCenter({
                                type: 'success',
                                title: response.message
                            })

                            let dg = $('#ttDataJadwal');
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