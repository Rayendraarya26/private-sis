@extends('layouts.layout_app')

@section('title', 'LKS')

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
                            <h3 class="dt-card__title">Data Jadwal Audit dan LKS</h3>
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
    <script>
        function confirmAjukan(jadwalID) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-success mb-2',
                cancelButtonClass: 'btn btn-warning mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Ajukan LKS?`,
                text: `Pastikan semua data sudah benar sebelum anda mengajukan LKS ke client`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `{{url("$url/temuan")}}/${jadwalID}/ajukan`,
                        method: 'POST',
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
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid-jadwal-audit") }}`,
                rownumbers: true,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
                pagination: true,
                pageSize: 50,
                clientPaging: false,
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "Aksi",
                        width: 130,
                        align: 'center',
                        formatter: function (val, row) {
                            let btnTemuan = "";
                            let btnSubmit = "";
                            let btnVerif  = "";
                            if (row.jadw_setujui_temuan == "none" || row.jadw_setujui_temuan == "revisi") {
                                if (row.total_temuan > 0) {
                                    btnTemuan = `<a href="{{url("$url/temuan")}}/${row.jadw_id}" class="btn btn-xs btn-warning btn-block">(${row.total_temuan}) Temuan LKS</a>`
                                } else {
                                    btnTemuan = `<a href="{{url("$url/temuan")}}/${row.jadw_id}" class="btn btn-xs btn-success btn-block"><i class="fas fa-check"></i> Temuan LKS</a>`
                                }

                                btnSubmit = `<button onclick="confirmAjukan(${row.jadw_id})" class="btn btn-xs btn-success btn-block"><i class="fas fa-paper-plane"></i> Ajukan LKS</a>`
                            } else if (row.jadw_setujui_temuan == "setuju") {
                                btnVerif = `<a href="{{url("$url/temuan")}}/${row.jadw_id}/verifikasi" class="btn btn-xs btn-primary btn-block">(${row.total_temuan}) Verifikasi LKS</a>`
                            }

                            return btnTemuan + btnSubmit + btnVerif
                        },
                    },
                ]],
                columns: [[
                    {field: 'jadw_setujui_temuan', title: 'Pengajuan ?', width: 200, sortable: true},
                    {field: 'cust_nama', title: 'Nama pelanggan', width: 200, sortable: true},
                    {field: 'jadw_jenis', title: 'Jenis Jadwal', width: 150, sortable: true},
                    {
                        field: 'total_jadwal', title: 'Jadwal', width: 80, sortable: true,
                        formatter: function (val) {
                            return val + " Jadwal";
                        },
                    },
                    {field: 'sert_nama', title: 'Sertifikasi', width: 250, sortable: true},
                    {field: 'jadw_tanggal_mulai', title: 'Tanggal<br/>Mulai', width: 100, sortable: true},
                    {field: 'jadw_tanggal_selesai', title: 'Tanggal<br/>Selesai', width: 100, sortable: true},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'total_jadwal', type: 'label'},
                    {field: 'jadw_audit_jenis', type: 'label'},
                ]);
        });
    </script>
@endpush
