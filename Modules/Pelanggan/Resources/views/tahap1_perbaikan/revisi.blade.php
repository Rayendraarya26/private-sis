@extends("layouts.layout_app")

@section('title', 'Persetujuan Temuan Tahap 1')

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


                    <div class="dt-card__body table-responsive">
                        <div class="pb-3">
                            <span class="bg-orange">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Revisi
                            <br>
                            <span class="bg-light-green">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Perbaikan
                            Dikirim ke Auditor
                        </div>
                        <form method="post" enctype="multipart/form-data" id="formRevisi">
                            @csrf
                            <table id="tinjauan" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th rowspan="2">Klausul</th>
                                    <th rowspan="2">Persyaratan</th>
                                    <th colspan="2" class="text-center">
                                        Dokumen PT. {{strtoupper($data->sis_permohonan->mohon_cust_nama)}}
                                    </th>

                                    <th rowspan="2" class="text-center">Hasil Tinjauan <br>(OK / NO)</th>
                                    <th colspan="3" class="text-center">Perbaikan</th>
                                    {{--                                <th colspan="2">Perbaikan</th>--}}
                                </tr>
                                <tr>
                                    <th>Kode Dokumen</th>
                                    <th>Judul Dokumen</th>
                                    <th>Ket Revisi</th>
                                    <th>Info Perbaikan</th>
                                    <th>File Upload</th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php $isRevisiExist = false; ?>
                                @foreach($data->sis_audit_tahap1_details as $detail)
                                    @php
                                        $isFixed = false;
                                        $dataRevisi = $detail->sis_audit_tahap1_revisis->where("thp1_revisi_status", '=', "open")->first();
                                        if(empty($dataRevisi)){
                                            $isFixed = true;
                                            $dataRevisi = $detail->sis_audit_tahap1_revisis->sortByDesc("created_at")->first();
                                        }else{
                                            $isRevisiExist = true;
                                        }
                                    @endphp

                                    <tr class="{!! (($detail->sis_audit_tahap1_revisis->count() == 0 || $dataRevisi?->thp1_revisi_status == "closed") ? '' : (!$isFixed ? 'bg-orange' : 'bg-light-green')) !!}">
                                        <td style="padding-left: 10px">{{$detail->aud_thp1_det_thp1_nomor}}</td>
                                        <td>{{$detail->aud_thp1_det_peryataan}}</td>
                                        <td>{{$detail->aud_thp1_det_kode_dok}}</td>
                                        <td>{{$detail->aud_thp1_det_judul_dok}}</td>
                                        <td class="text-center">{{ucwords($detail->aud_thp1_det_hasil_tinjauan)}}</td>
                                        {{--<td>{{$detail->aud_thp1_det_keterangan}}</td>--}}

                                        {{--Field Revisi--}}
                                        @if($detail->sis_audit_tahap1_revisis->count() > 0)
                                            @if($isFixed)
                                                <td>{{$dataRevisi->thp1_revisi_catatan}}</td>
                                                <td>{{$dataRevisi->thp1_revisi_perbaikan}}</td>
                                                <td>
                                                    @if($dataRevisi->sis_audit_tahap1_revisi_files->count() > 0)
                                                        <ul>
                                                            @foreach($dataRevisi->sis_audit_tahap1_revisi_files as $revisiFile)
                                                                <li>
                                                                    <a href="{!! asset($revisiFile->thp1_revisi_file_path) !!}"
                                                                       target="_blank">
                                                                        Berkas {{$loop->iteration}}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </td>
                                            @else
                                                <td>{{$dataRevisi->thp1_revisi_catatan}}</td>
                                                <td>
                                                <textarea aria-label="perbaikan" class="form-control"
                                                          name="revisi_perbaikan[{{$dataRevisi->thp1_revisi_id}}]"
                                                          placeholder="Tulis info perbaikan..."></textarea>
                                                </td>
                                                <td>
                                                    <input multiple type="file" class="form-control"
                                                           aria-label="upload perbaikan"
                                                           name="revisi_files[{{$dataRevisi->thp1_revisi_id}}][]">
                                                    <small>Allow multiple upload</small>
                                                </td>
                                            @endif
                                        @else
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            @if($isRevisiExist)
                                <div style="float: right">
                                    <button class="btn btn-primary" type="button" onclick="confirmSubmit()">
                                        <i class="fad fa-save"></i> Simpan
                                    </button>
                                </div>
                            @endif
                        </form>
                    </div>
                        <a href="{{url($url)}}" class="btn btn-default">
                            <i class="fad fa-arrow-left"></i>Back
                        </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('javascript')
    <script>
        function confirmSubmit() {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-primary mb-2',
                cancelButtonClass: 'btn btn-warning mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Kirim ke Auditor ?`,
                text: "Pastikan revisi anda sudah selesai dan benar. klik Kirim untuk melanjutkan",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Kirim',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#formRevisi').submit();
                }
            });
        }
    </script>
@endpush
