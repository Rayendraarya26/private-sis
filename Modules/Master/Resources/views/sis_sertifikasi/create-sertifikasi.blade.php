@extends('layouts.layout_app')

@section('title', 'Tambah Sertifikasi')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default"
                   href="{{url("$url")}}"
                   style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali
                </a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Tambah Sertifikasi</h3>
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

                                <form method="post" action="{{action("$module@store")}}">
                                    <!-- Security CSRF TOKEN -->
                                    @csrf
									<input type="hidden" name="tipe" value="store-sertifikasi">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="sert_nama">Nama Sertifikasi*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="text" name="sert_nama" id="sert_nama" value="{{old('sert_nama')}}">
                                        </div>
                                    </div>
									
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="sert_expired">Waktu Max Kadaluarsa(Tahun)*</label>
                                        <div class="col-sm-4">
                                            <input class="form-control" type="text" name="sert_expired" id="sert_expired" value="{{old('sert_expired')}}">
                                        </div>
                                    </div>
									
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="sert_format_referensi">Format No. Referensi *</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="text" name="sert_format_referensi" id="sert_format_referensi" value="{{old('sert_format_referensi')}}">
                                        </div>
                                    </div>
									
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="sert_is_product">Merupakan Sertifikasi Produk? *</label>
                                        <div class="col-sm-8">
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="sert_is_product" id="sert_is_product" value="ya" {{old('sert_is_product') == "ya" ? "checked" :""}} >
												<label class="form-check-label" for="sert_is_product">Ya</label>
											</div>
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="sert_is_product" id="sert_is_product" value="tidak" {{old('sert_is_product') == "tidak" ? "checked" :""}}>
												<label class="form-check-label" for="sert_is_product">Tidak</label>
											</div>
                                        </div>
                                    </div>
									
									
									
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="sert_deskripsi">
                                            Deskripsi
                                        </label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" placeholder="Masukkaan deskripsi..."
                                                      name="sert_deskripsi"
                                                      id="sert_deskripsi">{{old('sert_deskripsi')}}</textarea>
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
            selector: '#sert_deskripsi',
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
