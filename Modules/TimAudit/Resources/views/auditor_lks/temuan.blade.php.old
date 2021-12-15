@extends('layouts.layout_app')

@section('title', 'Temuan LKS')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
                @if(session('message'))
                    <div class="alert alert-primary alert-dismissible fade show" role="alert">
                        {!! session('message') !!}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                @endif
                @error('message')
                <div class="alert alert-danger">
                    {{$message}}
                </div>
                @enderror
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title" style="text-align: center">
                                LAPORAN KETIDAKSESUAIAN dan LAPORAN VERIFIKASI
                            </h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="col-lg-12">
                            <table class="table">
                                <tr>
                                    <td style="width: 50px">1</td>
                                    <td>Jenis Kegiatan</td>
                                    <td>:
                                        @foreach($data->sis_jadwal_audits as $audit)
                                            {{$audit->jadw_audit_kegiatan . (!$loop->last ? ' - ' : '.')}}
                                        @endforeach
                                    </td>
                                </tr>

                                <tr>
                                    <td rowspan="3">2</td>
                                    <td>Nama Perusahaan</td>
                                    <td>: {{$data->sis_pelanggan->cust_nama}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>No. Referensi</td>
                                    <td>:
                                        @foreach($data->sis_jadwal_audits as $audit)
                                            @if($audit->jadw_audit_nomor_referensi != "")
                                                {{$audit->jadw_audit_nomor_referensi . (!$loop->last ? ' ; ' : '.')}}
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>: {{$data->sis_pelanggan->cust_alamat}}
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td>Tanggal Asesmen</td>
                                    <td>
                                        : {{ $data->jadw_tanggal_mulai->isoFormat("LL") }}
                                        s/d {{ $data->jadw_tanggal_selesai->isoFormat("LL") }}</td>
                                </tr>

                                <tr>
                                    <td>4</td>
                                    <td>Tim Asesmen</td>
                                    <td>:
                                        <ol>
                                            @foreach($data->sis_jadwal_tims as $tim)
                                                <li>
                                                    {{$tim->master_pegawai->peg_nama}} | {{$tim->jadw_tim_kode}}
                                                    <b>({{ucwords($tim->jadw_tim_posisi)}})</b>
                                                </li>
                                            @endforeach
                                        </ol>
                                    </td>
                                </tr>

                                <tr>
                                    <td>5</td>
                                    <td>Standar Acuan</td>
                                    <td>:
                                        @foreach($data->sis_jadwal_audits as $audit)
                                            @if($audit->jadw_audit_standart_acuan != "")
                                                {{$audit->jadw_audit_standart_acuan . (!$loop->last ? ' ; ' : '.')}}
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div id="ttData" style="width:100%; min-width: 310px"></div>
                        <div id="toolbar" style="padding: 10px 0 10px 20px">
                            <div class="row">
                                {{--@if(authorized("{$module}@addTemuan"))--}}
                                <div>
                                    <a href="{{ url("$url/temuan/$data->jadw_id/tambah") }}"
                                       class="btn btn-outline-success btn-xs">
                                        <i class="fas fa-plus"></i> Tambah
                                    </a>
                                </div>
                                &nbsp;&nbsp;&nbsp;
                                {{--@endif--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("javascript")
    <script src="{{asset('assets/plugins/easyui/datagrid-detailview.js')}}"></script>
    <script>
        function confirmDelete(lksID) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-success mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Hapus LKS ?`,
                text: `Menghapus data LKS bersifat permanen dan tidak dapat di kembalikan`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `{{url("$url/temuan/$data->jadw_id/delete")}}/${lksID}`,
                        type: 'DELETE',
                        dataType: 'json',
                        success: function (response) {
                            toastCenter({
                                type: 'success',
                                title: response.message
                            })

                            $('#ttData').datagrid('reload');
                        },
                        error: function (xhr) {
                            if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                            else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                        }
                    });
                }
            });
        }

        $(function () {
            let dg = $('#ttData').datagrid({
                title: "Data LKS",
                toolbar: '#toolbar',
                view: detailview,
                method: 'get',
                height: 500,
                url: `{!! url("$url/ajax?action=datagrid-lks&jadwal_id=$data->jadw_id") !!}`,
                rownumbers: true,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
                pagination: true,
                pageSize: 50,
                clientPaging: false,
                detailFormatter: function (index, row) {
                    return `
                    <div style="padding: 20px 0 20px 0">
                        <h4>Perbaikan:</h4>
                        <ul>
                            <li>Analisa: <br>${row.lks_perbaikan_analisa}</li>
                            <li>Koreksi: <br>${row.lks_perbaikan_koreksi}</li>
                            <li>Tindakan: <br>${row.lks_perbaikan_tindakan}</li>
                        </ul>
                        <br>
                        <a href="{{url("$url/temuan/$data->jadw_id/detail")}}/${row.lks_id}"><i class="fad fa-eye"></i> Lihat lebih detail...</a>
                    </div>`;
                },
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "Aksi",
                        width: 80,
                        align: 'center',
                        formatter: function (val, row) {
                            // let btnEdit   = `<a href="" class="btn btn-xs btn-outline-warning" title="Edit"><i class="fas fa-pencil"></i></a>`
                            // let btnDelete = `<button onclick="confirmDelete(${row.lks_id})" class="btn btn-xs btn-outline-danger" title="Delete"><i class="fas fa-trash-alt"></i></button>`
                            {{--let btnDetail = `<a href="{{url("$url/temuan/$data->jadw_id/detail")}}/${row.lks_id}" class="btn btn-xs btn-outline-primary" title="Detail"><i class="fas fa-eye"></i></a>`--}}

                            let btnEdit   = `<div data-options="iconCls:'fad fa-edit'" onclick="location.href = '{{url("$url/temuan/$data->jadw_id/edit")}}/${row.lks_id}'">Edit</div>`;
                            let btnDetail = `<div data-options="iconCls:'fad fa-check'" onclick="location.href = '{{url("$url/temuan/$data->jadw_id/detail")}}/${row.lks_id}'">Detail & Verif</div>`;
                            if (row.lks_status == "memadai" || row.lks_status == "tidak-memadai") {
                                btnDetail = `<div data-options="iconCls:'fad fa-eye'" onclick="location.href = '{{url("$url/temuan/$data->jadw_id/detail")}}/${row.lks_id}'">Detail</div>`;
                            }
                            let btnDelete = `<div data-options="iconCls:'fad fa-trash'" onclick="confirmDelete('${row.lks_id}')">Delete</div>`;

                            if (row.lks_sudah_ditutup == "tidak") {
                                if (!row.allow_modify) {
                                    btnEdit = btnDelete = '';
                                }
                            } else {
                                btnEdit = btnDelete = '';
                            }

                            let dom = `dropdownMenu_${row.lks_id}`;

                            let renderDelete = `<div class="menu-sep"></div>${btnDelete}`
                            if (btnDelete == '') renderDelete = ''
                            return `
                                <div>
                                    <button class="btn-action btn-info" data-index="${row.lks_id}" title="Aksi"
                                    id="btn_action_${row.lks_id}">
                                        <i class="fa fa-setting"></i> Aksi
                                    </button>
                                    <div id="${dom}" style="width:150px; display: none;">
                                        ${btnDetail}
                                        ${btnEdit}

                                        ${renderDelete}
                                    </div>
                                </div>`;
                        },
                    },
                ]],
                columns: [[
                    {
                        field: 'lks_sudah_ditutup', title: 'Ditutup ?', width: 100, sortable: true,
                        formatter: function (val) {
                            return val.ucwords();
                        }
                    },
                    {
                        field: 'lks_status', title: 'Status', width: 100, sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'memadai':
                                    return 'Memadai';
                                case 'tidak-memadai':
                                    return 'Tidak Memadai'
                                case 'revisi':
                                    return 'Revisi'
                                case 'proses':
                                    return 'Proses'
                                case 'fixed':
                                    return 'Telah Diperbaiki'
                            }
                        }
                    },
                    {field: 'jadw_tim_kode', title: 'Tim Kode', width: 90, sortable: true},
                    {field: 'lks_uraian_ketidaksesuaian', title: 'Uraian', width: 200, sortable: true},
                    {field: 'lks_kategori_ketidaksesuaian', title: 'Kategori', width: 200, sortable: true},
                    {field: 'lks_klausul_ketidaksesuaian', title: 'Klausul', width: 150, sortable: true},
                    {field: 'lks_expired_date_perbaikan', title: 'Max Tgl <br>Perbaikan', width: 100, sortable: true},
                ]],
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
                            menu: '#dropdownMenu_' + data.rows[idx].lks_id
                        });
                    });
                },
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {
                        field: 'lks_status',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'memadai', text: 'Memadai'},
                                {value: 'tidak-memadai', text: 'Tidak Memadai'},
                                {value: 'revisi', text: 'Revisi'},
                                {value: 'proses', text: 'Proses'},
                                {value: 'fixed', text: 'Telah Diperbaiki'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'lks_sudah_ditutup',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                    {
                        field: 'lks_sudah_ditutup',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'ya', text: 'Ya'},
                                {value: 'tidak', text: 'Tidak'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'lks_status',
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
