@extends('layouts.layout_app')

@section('title', 'Ubah Template')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default" href="{{ url("$url") }}" style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali
                </a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Tambah Template</h3>
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
                                <form method="post" action="{{url("$url/update")}}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="template_id" value="{{$data->template_id}}">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="template_code">Kode template*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan kode template ..."
                                                   type="text"
                                                   name="template_code" id="template_code"
                                                   value="{{old('template_code') ?? $data->template_code}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="template_desc">Deskripsi
                                            template</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan deskripsi template..."
                                                   type="text"
                                                   name="template_desc" id="template_desc"
                                                   value="{{old('template_desc') ?? $data->template_desc}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="template_mail_subject">Judul
                                            email</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan judul email..."
                                                   type="text"
                                                   name="template_mail_subject" id="template_mail_subject"
                                                   value="{{old('template_mail_subject') ?? $data->template_mail_subject}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="template_mail_body">
                                            Konten email
                                        </label>
                                        <div class="col-sm-8">
                                            <div class="alert alert-info">
                                                Common Parser
                                                <ol>
                                                    @foreach($email_parser as $ep)
                                                        <li>{{"{ $ep }"}}</li>
                                                    @endforeach
                                                </ol>
                                            </div>
                                            <textarea class="form-control" placeholder="Masukkaan deskripsi..."
                                                      name="template_mail_body"
                                                      id="template_mail_body">{{old('template_mail_body') ?? $data->template_mail_body}}</textarea>
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
        tinyMCE.init({
            invalid_elements: "script",
            selector: '#template_mail_body',
            plugins: 'autosave link image code lists',
            relative_urls: false,
            height: 500,
            placeholder: 'Halo { FULLNAME } selamat datang...',
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

