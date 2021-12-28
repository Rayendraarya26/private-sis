@extends("layouts.layout_app")

@section('title', 'Detail Jadwal')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Jadwal Tahap 1
                                ({{$data->sis_permohonan_detail?->master_sertifikasi->sert_nama}})</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Pelaksanaan</h4>
                                <table class="table">
                                    <tr>
                                        <td>Tanggal</td>
                                        <td>: {{$data->aud_thp1_tanggal_mulai->isoFormat("LL")}}
                                            s/d {{$data->aud_thp1_tanggal_selesai->isoFormat("LL")}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tujuan</td>
                                        <td>: {{$data->aud_thp1_tujuan}}</td>
                                    </tr>
                                    <tr>
                                        <td>Standar Acuan</td>
                                        <td>: {{$data->aud_thp1_standart_acuan}}</td>
                                    </tr>
                                    <tr>
                                        <td>Jenis</td>
                                        <td>: {{strtoupper($data->sert_tahap1_jenis)}}</td>
                                    </tr>
                                    @if(!empty($data->aud_thp1_file_jadwal))
                                        <tr>
                                            <td>File Jadwal</td>
                                            <td>:
                                                <a href="{{asset($data->aud_thp1_file_jadwal)}}">
                                                    <i class="fad fa-download"></i> Unduh
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-12">
                                <h4>Tim Audit</h4>
                                <ol>
                                    @foreach($data->sis_audit_tahap1_tims as $tim)
                                        <li>
                                            {{$tim->thp1_tim_posisi}}
                                            {{$tim->master_pegawai->peg_nama}} ({{$tim->thp1_tim_kode}})
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
