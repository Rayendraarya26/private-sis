@extends("layouts.layout_app")

@section('title', 'Data Sertifikasi')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
                <div class="dt-card">
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

                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Data Sertifikat Pelanggan</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div id="ttData" style="width:100%; min-width: 310px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("javascript")
    <script src="{{asset('assets/plugins/easyui/datagrid-detailview.js')}}"></script>
    <script>
		function statusStyle(value,row,index){
            if (value == 'ya'){
                return 'background-color:blue;color:white;';
            }
			else{
				return 'background-color:#ffee00;color:red;';
			}
        }
		
        $(function () {
            let dg = $('#ttData').datagrid({
                view: detailview,
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid") }}`,
                rownumbers: false,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
                pagination: true,
                pageSize: 50,
                clientPaging: false,
				onBeforeLoad: function () {
                    $(this).datagrid('getPanel').find('.btn-action').each(function (idx, row) {
                        try {
                            $(this).menubutton('destroy');
                        } catch (e) {
                            console.log('failed destroy');
                        }
                    });
                },
                onLoadSuccess: function (data) {
                    $(this).datagrid('getPanel').find('.btn-action').each(function (idx, row) {
                        $(this).menubutton({
                            menu: '#dropdownMenu_' + data.rows[idx].cust_sert_id
                        });
                    });
                },
                detailFormatter: function (index, row) {
                    let komoditasNama = "-";
                    let tipe          = "-";
                    let merk          = "-";

                    if (row.komodt_nama != null) komoditasNama = row.komodt_nama;
                    if (row.cust_sert_tipe != null) tipe = row.cust_sert_tipe;
                    if (row.cust_sert_merk != null) merk = row.cust_sert_merk;

                    return `
                    <div style="padding: 20px 0 20px 0">
                        <h4>Komoditas</h4>
                        <ul>
                            <li>Nama Komoditas : ${komoditasNama}</li>
                            <li>Tipe : ${tipe}</li>
                            <li>Merk : ${merk}</li>
                        </ul>
                    </div>`;
                },
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "",
                        width: 100,
                        align: 'center',
                        formatter: function (val, row) {
                            let dom = `dropdownMenu_${row.cust_sert_id}`;
                            let btnEdit = ``;							
							btnEdit += `<div data-options="iconCls:'fad fa-print'" onclick="window.open('{{ url("$url/cetak") }}/${row.cust_sert_id}', '_blank').focus()">Cetak Sertifikat</div>`;
							btnEdit += `<div data-options="iconCls:'fad fa-upload'" onclick="location.href = '{{ url("$url/upload") }}/${row.cust_sert_id}'">Upload Sertifikat</div>`;
							if(row.cust_sert_file == 'ya'){
								btnEdit += `<div data-options="iconCls:'fad fa-download'" onclick="window.open('${row.cust_sert_filepath}', '_blank').focus()">Download Sertifikat</div>`;
							}
                            return `
								<div>
									<button class="btn-action btn-info btn-block" data-index="${row.cust_sert_id}" title="Aksi">
										<i class="fa fa-setting"></i> Aksi
									</button>
									<div id="${dom}" style="width:200px; display: none;">
										@if(authorized("{$module}@cetak")) ${btnEdit} @endif
								</div>
							</div>`
                        }
                    }
                ]],
                columns: [[
					{field: 'cust_nama', title: 'Nama pelanggan', width: 250, sortable: true},
                    {
                        field: 'cust_sert_status', title: 'Status<br/>Sertifikat', width: 100, sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'on_going':
                                    return "Aktif"
                                case 'expired':
                                    return "Kadaluarsa"
                                case 'dibekukan':
                                    return "Dibekukan"
                            }
                        }
                    },
					{
                        field: 'cust_sert_file', title: 'Status<br/>Upload', width: 100, sortable: true, styler:statusStyle,
                        formatter: function (val) {
                            switch (val) {
                                case 'ya':
                                    return "Sudah"
                                case 'tidak':
                                    return "Belum"
                            }
                        }
                    },
                    {field: 'cust_sert_nomor_referensi', title: 'No Ref', width: 220, sortable: true},
                    {field: 'cust_sert_nomor_sertifikat', title: 'No Sertifikat', width: 220, sortable: true},
                    {field: 'cust_sert_nomor_sni', title: 'No SNI', width: 220, sortable: true},
                    {field: 'cust_sert_expired_date', title: 'Tgl <br> Kadaluarsa', width: 150, sortable: true},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'cust_sert_filepath', type: 'label'},
					{
                        field: 'cust_sert_file',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'ya', text: 'Sudah'},
                                {value: 'tidak', text: 'Belum'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'cust_sert_file',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                    {
                        field: 'cust_sert_status',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'on_going', text: 'Aktif'},
                                {value: 'expired', text: 'Kadaluarsa'},
                                {value: 'dibekukan', text: 'Dibekukan'}
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'cust_sert_status',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                ]);
        });
    </script>
@endpush
