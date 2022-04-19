		$(function () {
            let dg = $('#ttDataJadwal').datagrid({
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid-jadwal-audit") }}&id_reg={{$data_permohonan['id_reg']}}`,
                toolbar: '#toolbarJadwal',
                rownumbers: true,
                nowrap: false,
                singleSelect: false,
                remoteSort: false,
                remoteFilter: false,
                multiSort: true,
                pagination: false,
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