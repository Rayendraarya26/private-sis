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
                                                    {{$tim->master_pegawai->peg_nama}}
                                                    ({{ucwords($tim->jadw_tim_posisi)}})
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
                    </div>`;
                },
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "Aksi",
                        width: 120,
                        align: 'center',
                        formatter: function (val, row) {
                            let btnDetail = `<button class="btn btn-xs btn-outline-primary" title="Detail"><i class="fas fa-eye"></i></button>`
                            let btnEdit   = `<button class="btn btn-xs btn-outline-warning" title="Edit"><i class="fas fa-pencil"></i></button>`
                            let btnDelete = `<button class="btn btn-xs btn-outline-danger"  title="Delete"><i class="fas fa-trash-alt"></i></button>`

                            if (row.lks_sudah_ditutup == "tidak") {
                                if (!row.allow_modify) {
                                    return btnDetail;
                                } else {
                                    return btnDetail + '&nbsp;' + btnEdit + '&nbsp;' + btnDelete
                                }
                            }
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

                    // {field: 'lks_input_date_perbaikan', title: 'Tgl Input Perbaikan', width: 100, sortable: true},
                    // {field: 'lks_perbaikan_analisa', title: 'Perbaikan', width: 250, sortable: true},
                    // {field: 'lks_perbaikan_koreksi', title: 'Tanggal<br/>Mulai', width: 100, sortable: true},
                    // {field: 'lks_perbaikan_tindakan', title: 'Tanggal<br/>Selesai', width: 100, sortable: true},
                    // {field: 'lks_bagian_pendamping', title: 'Tanggal<br/>Selesai', width: 100, sortable: true},
                    // {field: 'lks_bukti_tindakan_perbaikan', title: 'Tanggal<br/>Selesai', width: 100, sortable: true},
                ]],
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
