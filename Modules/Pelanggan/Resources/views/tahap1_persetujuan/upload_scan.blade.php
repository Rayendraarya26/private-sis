@extends("layouts.layout_app")

@section('title', 'Upload Scan')

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
                            <h3 class="dt-card__title">Upload Scan</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <form method="post" enctype="multipart/form-data" onsubmit="$('#btnSubmit').attr('disabled', true)">
                            @csrf
                            <div class="row">
                                <div class="col-sm-10">
                                    <div class="form-group">
                                        <label for="berkas_ket">
                                            Unggah <b>Scan Surat Tugas</b> yang sudah diberi TTD dan cap
                                            @if(!empty($data->aud_thp1_file_surat_tugas))
                                                &nbsp; | &nbsp;
                                                <a href="{{asset($data->aud_thp1_file_surat_tugas)}}">
                                                    <i class="fad fa-download"></i> Download Surat Tugas
                                                </a>
                                            @endif
                                        </label>
                                        <input type="file" class="form-control" name="file_surat_tugas"
                                               accept="application/pdf">
                                    </div>
                                    <div class="form-group">
                                        <label for="berkas_ket">
                                            Unggah <b>Scan Notulen</b> yang sudah diberi TTD dan cap
                                            @if(!empty($data->aud_thp1_file_notulen))
                                                &nbsp; | &nbsp;
                                                <a href="{{asset($data->aud_thp1_file_notulen)}}">
                                                    <i class="fad fa-download"></i> Download Notulen
                                                </a>
                                            @endif
                                        </label>
                                        <input type="file" class="form-control" name="file_notulen"
                                               accept="application/pdf">
                                    </div>
                                    <div class="form-group">
                                        <label for="berkas_ket">
                                            Unggah <b>Scan Subkontrak</b> yang sudah diberi TTD dan cap
                                            <small>(optional)</small>
                                            @if(!empty($data->aud_thp1_file_subkon))
                                                &nbsp; | &nbsp;
                                                <a href="{{asset($data->aud_thp1_file_subkon)}}">
                                                    <i class="fad fa-download"></i> Download Subkontrak
                                                </a>
                                            @endif
                                        </label>
                                        <input type="file" class="form-control" name="file_subkontrak"
                                               accept="application/pdf">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <button class="btn btn-success" id="btnSubmit"><i class="fad fa-paper-plane"></i> Kirim</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
