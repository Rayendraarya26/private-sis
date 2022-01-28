@extends('layouts.layout_app')

@section('title', 'Edit Kompetensi Auditor')

@section('content')
    <script>
        function submitKompetensi(id, value) {
            const payload = {
                peg_id: {{$pegawai->peg_id}},
                sert_id: id,
                value: value ? 'allow' : 'revoke',
            }

            $.ajax({
                url: `{{action("$module@saveByPegawai")}}`,
                type: 'post',
                data: payload,
                success: async function (res) {
                    toastCenter({
                        type: 'success',
                        title: res.message
                    })
                },
                error: function (xhr) {
                    self.loading_submit = false;
                    if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                    else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                }
            });
        }
    </script>

    <div class="dt-content" id="vueKompetensi">
        <div class="row">
            <div class="col-md-12">
                @if(session('message'))
                    <div class="alert alert-success" role="alert">
                        {{ session('message') }}
                    </div>
                @endif

                @error('message')
                <div class="alert alert-danger">
                    {{$message}}
                </div>
                @enderror

                <div class="dt-card">
                    <div class="dt-card__body">
                        <div class="row">
                            <div class="col-md-12">
                                <div style="text-align: center">
                                    <h4>Set Kompetensi Auditor untuk <b>{{$pegawai->peg_nama}}</b></h4>
                                </div>
                            </div>

                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Izinkan</th>
                                    <th>Sertifikasi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($dataSertifikat as $sert)
                                    <tr>
                                        <td>
                                            <input class="easyui-switchbutton" style="width: 100px"
                                                   data-options="onText:'Ya',offText:'Tidak',onChange: function(checked){submitKompetensi({{$sert->sert_id}},checked);}"
                                                   id="{{'sert_'.$sert->sert_id}}"
                                                {{in_array($sert->sert_id, $selectedSertifikatId) ? 'checked' : ''}}>
                                        </td>
                                        <td>{{$sert->sert_nama}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <a class="btn btn-sm btn-default" href="{{ url("$url") }}" style="margin-bottom: 20px">
                        <i class="fad fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection


