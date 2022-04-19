		$(function () {
            let dg = $('#ttDataBiaya').datagrid({
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid-biaya-audit") }}&id_reg={{$data_permohonan['id_reg']}}`,
                toolbar: '#toolbarBiaya',
                rownumbers: true,
                nowrap: false,
                singleSelect: false,
                remoteSort: false,
                remoteFilter: false,
                multiSort: true,
                pagination: false,
                columns: [[
                    {field: 'keterangan', title: 'Keterangan', width: 320, sortable: true},
                    {field: 'qty', title: 'Qty', width: 100, sortable: true},
                    {
						field: 'harga', title: 'Harga(Rp.)', width: 100, sortable: true,
						formatter: function (val, row, index) {
							return val.toString().formatUang(".");
						}
					},
                    {
						field: 'total', title: 'Total(Rp.)', width: 100, sortable: true,
						formatter: function (val, row, index) {
							return val.toString().formatUang(".");
						}
					},
					{field: 'id_biaya', hidden: true},
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