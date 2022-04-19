		$(function () {
            let dg = $('#ttDataAuditor').datagrid({
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid-auditor-audit") }}&id_reg={{$data_permohonan['id_reg']}}`,
                toolbar: '#toolbarAuditor',
                rownumbers: true,
                nowrap: false,
                singleSelect: false,
                remoteSort: false,
                remoteFilter: false,
                multiSort: true,
                pagination: false,
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