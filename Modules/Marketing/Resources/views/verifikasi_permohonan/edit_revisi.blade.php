@extends('layouts.layout_app')

@section('title', 'Revisi Pengajuan')

@section('content')
	<div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default" href="{{url("$url/detail/$dataPermohon->mohon_id?action=verifikasi")}}" style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali ke Detail Permohonan
                </a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Tambah Informasi Revisi Permohonan "{{$dataPermohon->mohon_id}}"</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="row">
                            <div class="col-lg-12">
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

                                <form method="post" action="{{action("$module@update")}}">
                                    <!-- Security CSRF TOKEN -->
                                    @csrf
									
									<input type="hidden" name="tipe" value="update-revisi">
									<input type="hidden" name="mohon_id" value="{{$dataPermohon->mohon_id}}">
									<input type="hidden" name="status_tipe" value="revisi">
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="status_judul">Judul *</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="status_judul" name="status_judul" class="form-control" value="{{old('status_judul')}}"/>
                                        </div>
                                    </div>
									
									
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="status_pesan">Pernyataan *</label>
                                        <div class="col-sm-8">
											<textarea class="form-control" placeholder="Pesan revisi..."
                                                      name="status_pesan"
                                                      id="status_pesan">{{old('status_pesan')}}</textarea>
                                        </div>
                                    </div>
									
                                    <div class="form-buttons-w">
                                        <button class="btn btn-success" type="submit">
                                            <i class="fas fa-save"></i> Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('javascript')
    <script src="https://cdn.tiny.cloud/1/hb65btdze8ubxfoabqu7fqjpuzpmx0c4k0je5f883m4l9ajf/tinymce/5/tinymce.min.js"
            referrerpolicy="origin"></script>

    <script>
        tinymce.init({
            selector: '#status_pesan',
            plugins: 'autosave link image code lists',
            relative_urls: false,
            height: 500,
            placeholder: '',
            images_reuse_filename: true,
            automatic_uploads: true,
            images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
            images_upload_credentials: true,
            toolbar: [{
                name: 'history',
                items: ['undo', 'redo']
            },
                {
                    name: 'styles',
                    items: ['styleselect']
                },
                {
                    name: 'formatting',
                    items: ['bold', 'italic']
                },
                {
                    name: 'alignment',
                    items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']
                },
                {
                    name: 'list',
                    items: ['bullist', 'numlist']
                },
                {
                    name: 'indentation',
                    items: ['outdent', 'indent']
                },
                {
                    name: 'link',
                    items: ['link', 'image']
                },
                {
                    name: 'restore',
                    items: ['restoredraft']
                },
            ],
        });

        @if(session('message'))
        toastCenter({
            type: 'success',
            title: '{{ session('message') }}'
        })
        @endif
    </script>
@endpush
