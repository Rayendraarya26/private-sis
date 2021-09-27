@extends('layouts.layout_app')

@section('title', 'Tambah Kecamatan')

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
                            <h3 class="dt-card__title">Tambah Kecamatan</h3>
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
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="prov_id">Provinsi *</label>
                                        <div class="col-sm-8">
                                            <input style="width:100%;" type="text" id="prov_id" name="prov_id" class="form-control" value="{{old('prov_id')}}"/>
                                        </div>
                                    </div>
									
									<div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="prov_id">Kabupaten *</label>
                                        <div class="col-sm-8">
                                            <input style="width:100%;" type="text" id="kab_id" name="kab_id" class="form-control" value="{{old('kab_id')}}"/>
                                        </div>
                                    </div>
									
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="kec_nama">Nama Kecamatan*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan nama kecamatan ..."
                                                   type="text" name="kec_nama" id="kec_nama"
                                                   value="{{old('kec_nama')}}">
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
@push("javascript")
    <script>
		let cb_prov = $('#prov_id').combobox({
			width: 300,    
			mode: 'remote',
			method: 'GET',
			valueField: 'prov_id',
			textField: 'prov_nama',
			url:`{{url("$url/ajax?action=combobox-provinsi")}}`,
			onSelect: function(rec){
				cb_kab.combobox({
					url:`{{url("$url/ajax?action=combobox-kabupaten")}}`,
					queryParams: {
						prov_id: `${rec.prov_id}`,
					},
				});
				
				cb_kab.combobox('setValue', '{{old('kab_id')}}');
			}
		});
		
		cb_prov.combobox('setValue', '{{old('prov_id')}}');
		
		let cb_kab = $('#kab_id').combobox({
			width: 300,    
			mode: 'remote',
			method: 'GET',
			valueField: 'kab_id',
			textField: 'kab_nama'
		});
    </script>
@endpush
