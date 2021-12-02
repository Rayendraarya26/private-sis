<div class="row">
    <div class="col-md-12" style="padding-top: 20px">
        <h4>Kelengkapan Dokumen</h4>

        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Dokumen</th>
                    <th>Dokumen Anda</th>
                </tr>
                </thead>
                <tbody>
                @foreach($dataPemohon->sis_permohonan_dokumens as $dok)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$dok->master_jenis_dok_perusahaan->jenis_dok_perusahaan_text}}</td>
                        <td>
                            <a target="_blank" href="{{asset($dok->mohon_dok_filepath)}}">
                                <i class="fad fa-download"></i>
                                Unduh
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>
