@extends("layouts.layout_app")

@section('title', 'Pembatalan Sertifikasi')

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
                            @if($data->mohon_cancel_status !== 'no')
                                <div class="alert alert-info">
                                    @if($data->mohon_cancel_status === 'process')
                                        Pembatalan sedang dalam proses pengajuan
                                    @else
                                        Pembatalan berhasil
                                    @endif
                                </div>
                            @endif
                            <h3 class="dt-card__title">Pembatalan Sertifikasi #{{ $data->mohon_id }}</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="row">
                            <div class="col-lg-12">
                                @if($data->mohon_cancel_status == 'no')
                                    <form method="post"
                                          onsubmit="$('#btnSubmit').attr('disabled', true)"
                                          action="{{action("$module@processCancel", $data->mohon_id)}}"
                                          enctype="multipart/form-data">
                                        @csrf

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3" for="mohon_cancel_reason">
                                                Keterangan*
                                            </label>
                                            <div class="col-sm-8">
                                            <textarea class="form-control"
                                                      placeholder="Masukkaan keterangan pembatalan..."
                                                      name="mohon_cancel_reason"
                                                      id="mohon_cancel_reason">{{$data->mohon_cancel_reason ?? old('mohon_cancel_reason')}}</textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"
                                                   for="mohon_cancel_file">Surat Pembatalan*</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" name="mohon_cancel_file" type="file"
                                                       accept="application/pdf" value="Transfer"
                                                       id="mohon_cancel_file">

                                                @if(!empty($data->mohon_cancel_file))
                                                    <small>
                                                        <a href="{{asset($data->mohon_cancel_file)}}" target="_blank">
                                                            <i class="fad fa-download"></i> Download surat pembatalan
                                                        </a>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-buttons-w">
                                            <button class="btn btn-success" type="submit" id="btnSubmit">
                                                <i class="fas fa-save"></i> Simpan
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="mohon_cancel_reason">
                                            Alasan Pembatalan*
                                        </label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" readonly
                                                      placeholder="Masukkaan keterangan pembatalan..."
                                                      name="mohon_cancel_reason"
                                                      id="mohon_cancel_reason">{{$data->mohon_cancel_reason ?? old('mohon_cancel_reason')}}</textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="mohon_cancel_file">Surat Pembatalan*</label>
                                        <div class="col-sm-8">
                                            @if(!empty($data->mohon_cancel_file))
                                                <a href="{{asset($data->mohon_cancel_file)}}" target="_blank">
                                                    <i class="fad fa-download"></i> Download surat pembatalan
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-buttons-w">
                                        <a class="btn btn-sm btn-default" href="{{ url("$url") }}" style="margin-bottom: 20px">
                                            <i class="fad fa-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
