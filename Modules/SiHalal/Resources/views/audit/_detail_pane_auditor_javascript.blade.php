		$(function () {
            let dg = $('#ttDataAuditor').datagrid({
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid-auditor-audit") }}&id_reg={{$data_permohonan['id_reg']}}`,
                toolbar: '#toolbarAuditor',
                rownumbers: false,
                nowrap: false,
                singleSelect: false,
                remoteSort: false,
                remoteFilter: false,
                multiSort: true,
                pagination: false,
                frozenColumns: [[
                    {field: 'ck', checkbox: true, sortable: false},
                ]],
                columns: [[
                    {field: 'nama_auditor', title: 'Auditor', width: 150, sortable: true},
                    {field: 'create_by', title: 'Create By', width: 100, sortable: true},
                    {
						field: 'create_on', title: 'Create On', width: 100, sortable: true,
						formatter: function (val, row, index) {
							var date = new Date(val),
								dformat = ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' +  ((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + date.getFullYear();
							return dformat;
						}
					},
					{field: 'auditor_id', hidden: true},
					{field: 'id_audit_person', hidden: true},
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
                    {field: 'create_by', type: 'label'},
                    {field: 'create_on', type: 'label'},
                ]);
        });
		
		
        function addModalAuditor() {
            $("#id_audit").val("");
            $("#modalFormAuditorAdd").modal('show');
        }
		
		function confirmDeleteAuditor() {
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
					var data = $('#ttDataAuditor').datagrid('getData');
					var opts = $('#ttDataAuditor').datagrid('options');
					for (var i = 0; i < data.rows.length; i++) {
						var tr = opts.finder.getTr($('#ttDataAuditor')[0],i);
						var atLeastOneIsChecked = tr.find('input[type=checkbox]:checked').length > 0;
						if(atLeastOneIsChecked == true){
							idData.push(data.rows[i].id_reg);
						}
					}
                    $.ajax({
                        url: `{{url("$url/destroyAuditor")}}`,
						data: { 'ids[]': idData },
						type: 'DELETE',
                        success: function (response) {
                            toastCenter({
                                type: 'success',
                                title: response.message
                            })

                            let dg = $('#ttDataAuditor');
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