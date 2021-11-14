@extends('layouts.layout_app')

@section('title', 'Manage Homepage')

@push('css')
@endpush

@section('content')
<div class="dt-content" id="edit-homepage">
    <div class="row">
        <div class="col-md-12">
            <div class="dt-card">
                <div class="dt-card__header">
                    <div class="dt-card__heading">
                    	<h3 class="dt-card__title">Manage Homepage</h3>
                    	<hr>
	                    @if ($errors->any())
	                        <div class="alert alert-danger" role="alert">
	                        	<ul>
	                            	{!! implode('', $errors->all('<li>:message</li>')) !!}
	                        	</ul>
	                        </div>
	                    @endif
	                    @if(session('message'))
	                        <div class="alert alert-success" role="alert">
	                            {{ session('message') }}
	                        </div>
	                    @endif
                    </div>
                </div>
                <div class="dt-card__body">
                	<form method="post" action="{{url("$url/update")}}" enctype="multipart/form-data">
						@csrf
                        <h3 class="text-primary">Data Perusahaan</h3>
                        <div class="form-group row mb-0">
                            <div class="col-sm-6 mb-4">
	                            <label for="company_name">
	                                Nama Perusahaan
	                            </label>
                                <input required class="form-control" placeholder="Nama perusahaan" type="text" name="company_name" id="company_name" v-model="company_name">
                            </div>
                            <div class="col-sm-3 mb-4">
	                            <label for="company_shortname">
	                                Nama Pendek/Singkatan
	                            </label>
                                <input required class="form-control" placeholder="Nama pendek/singkatan" type="text" name="company_shortname" id="company_shortname" v-model="company_shortname">
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-sm-3 mb-4">
	                            <label for="company_email">
	                                Email Perusahaan
	                            </label>
                                <input required class="form-control" placeholder="Email perusahaan" type="email" name="company_email" id="company_email" v-model="company_email">
                            </div>
                            <div class="col-sm-3 mb-4">
	                            <label for="company_telp">
	                                Telp Perusahaan
	                            </label>
                                <input class="form-control" placeholder="Telp perusahaan" type="text" name="company_telp" id="company_telp" v-model="company_telp">
                            </div>
                            <div class="col-sm-3 mb-4">
	                            <label for="company_fax">
	                                Fax Perusahaan
	                            </label>
                                <input class="form-control" placeholder="Fax perusahaan" type="text" name="company_fax" id="company_fax" v-model="company_fax">
                            </div>
                            <div class="col-sm-3 mb-4">
	                            <label for="company_whatsapp">
	                                Whatsapp Perusahaan
	                            </label>
                                <input class="form-control" placeholder="Whatsapp perusahaan" type="text" name="company_whatsapp" id="company_whatsapp" v-model="company_whatsapp">
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-sm-6 mb-4">
	                            <label for="company_address">
	                                Alamat Perusahaan
	                            </label>
                                <textarea class="form-control" placeholder="Alamat perusahaan" name="company_address" id="company_address" v-model="company_address" rows="3"></textarea>
                            </div>
                            <div class="col-sm-6 mb-4">
	                            <label for="company_desc">
	                                Deskripsi Perusahaan
	                            </label>
                                <textarea class="form-control" placeholder="Deskripsi perusahaan" name="company_desc" id="company_desc" v-model="company_desc" rows="3"></textarea>
                            </div>
                        </div>

                    	<hr class="border-dashed my-8">

                        <h3 class="text-primary">Data Aplikasi</h3>
                        <div class="form-group row mb-0">
                            <div class="col-sm-6 mb-4">
	                            <label for="app_name">
	                                Nama Aplikasi
	                            </label>
                                <input required class="form-control" placeholder="Nama aplikasi" type="text" name="app_name" id="app_name" v-model="app_name">
                            </div>
                            <div class="col-sm-3 mb-4">
	                            <label for="app_shortname">
	                                Nama Pendek/Singkatan
	                            </label>
                                <input required class="form-control" placeholder="Nama pendek/singkatan" type="text" name="app_shortname" id="app_shortname" v-model="app_shortname">
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-sm-6 mb-4">
	                            <label for="app_desc">
	                                Deskripsi Aplikasi
	                            </label>
                                <textarea class="form-control" placeholder="Deskripsi aplikasi" name="app_desc" id="app_desc" v-model="app_desc" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-sm-3 mb-4">
	                            <label for="app_ketidakberpihakan">
	                                Pernyataan Ketidakberpihakan
	                            </label>
	                            <div class="w-100">
                                	<input type="file" class="form-control" name="app_ketidakberpihakan" id="app_ketidakberpihakan" accept="application/pdf">
                                	<div class="w-100 border rounded text-center mt-2 p-4" v-if="app_ketidakberpihakan">
                                		<a target="_blank" rel="noopener noreferrer" :href="app_ketidakberpihakan" class="btn btn-default border btn-lg">
                                			<i class="fa fa-download"></i>
                                		</a>
                                	</div>
	                            </div>
                            </div>
                            <div class="col-sm-3 mb-4">
	                            <label for="app_icon">
	                                Aplikasi Icon
	                            </label>
	                            <div class="w-100">
	                                <input type="file" class="form-control" name="app_icon" id="app_icon" accept="image/*">
			                        <img draggable="false" alt="" :src="app_icon" v-if="app_icon" @click="window.open(app_icon)" style="width: 20vh; cursor: pointer;" class="mt-2"/>
	                            </div>
                            </div>
                            <div class="col-sm-4 mb-4">
	                            <label for="app_bg">
	                                Aplikasi Background
	                            </label>
	                            <div class="w-100">
	                                <input type="file" class="form-control" name="app_bg" id="app_bg" accept="image/*">
			                        <img draggable="false" alt="" :src="app_bg" v-if="app_bg" @click="window.open(app_bg)" style="width: 30vh; cursor: pointer;" class="mt-2"/>
	                            </div>
                            </div>
                        </div>

                    	<hr class="border-dashed my-8">

                        <h3 class="text-primary">Lembaga</h3>
                        <div class="w-100 text-center py-4" v-if="!(lembaga && lembaga.length > 0)">
                        	<span>Tidak ada</span>
                        </div>
                        <div :class="`form-group row mb-0 border mx-1 py-3 mb-4 rounded bg-lighten-primary ${r.delete && 'd-none'}`" v-for="(r, i) in lembaga">
                        	<input type="hidden" :name="`lembaga[${i}][id]`" v-model="r.id">
                        	<input type="hidden" :name="`lembaga[${i}][delete]`" v-model="r.delete">
                            <div class="col-sm-12 mb-4">
                            	<div class="col-sm-12 mb-4 px-0">
		                            <label>
		                                Nama Lembaga
		                            </label>
	                                <input :required="!r.delete" class="form-control" :name="`lembaga[${i}][name]`" placeholder="Nama lembaga" type="text" v-model="r.name">
                            	</div>
                            	<div class="col-sm-12 mb-4 px-0">
		                            <label>
		                                Deskripsi
		                            </label>
	                                <textarea class="form-control" :name="`lembaga[${i}][desc]`" placeholder="Deskripsi Lembaga" v-model="r.desc" rows="3"></textarea>
                            	</div>
                            	<div class="col-sm-12 mb-4 px-0">
		                            <label>
		                                External Llink
		                            </label>
		                            <div class="d-flex justify-content-between">
	                                	<input class="form-control" :name="`lembaga[${i}][link]`" placeholder="Link eksternal" type="url" v-model="r.link">
		                            	<button v-if="r.link" type="button" class="btn btn-default border btn-sm ml-2" @click="window.open(r.link)">
		                            		<i class="fa fa-external-link"></i>
		                            	</button>
		                            </div>
                            	</div>
                            	<div class="col-sm-12 px-0">
                            		<div class="form-group custom-control custom-checkbox">
	                                	<input type="checkbox" :checked="r.active == 1 ? true : false" value="1" class="custom-control-input" :name="`lembaga[${i}][status]`" :id="`lembaga[${i}][status]`" @click="r.active = r.active ? 0 : 1">
					                    <label class="custom-control-label" :for="`lembaga[${i}][status]`">Aktif ?</label>
					                </div>
                            	</div>
                            </div>
                            <div class="col-sm-12 mb-4">
	                            <label>
	                                Konten
	                            </label>
                                <textarea :required="!r.delete" class="form-control texteditor" :name="`lembaga[${i}][content]`" :id="`lembaga[${i}][content]`" placeholder="Konten Lembaga" v-model="r.content" rows="3"></textarea>
                            </div>
                            <div class="col-sm-1 mb-4 pt-6">
                            	<button type="button" class="btn btn-danger btn-sm" @click="removeLembaga(i)">
                            		<i class="fa fa-trash"></i>
                            	</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-warning text-white" @click="addLembaga()">
                        	<i class="fa fa-plus"></i>
                        	Tambah Lembaga
                        </button>

                    	<hr class="border-dashed my-8">

                        <h3 class="text-primary">SOP</h3>
                        <div class="w-100 text-center py-4" v-if="!(sop && sop.length > 0)">
                        	<span>Tidak ada</span>
                        </div>
                        <div :class="`form-group row mb-0 border mx-1 py-3 mb-4 rounded bg-lighten-primary ${r.delete && 'd-none'}`" v-for="(r, i) in sop">
                        	<input type="hidden" :name="`sop[${i}][id]`" v-model="r.id">
                        	<input type="hidden" :name="`sop[${i}][delete]`" v-model="r.delete">
                        	<input type="hidden" :name="`sop[${i}][delete_img]`" v-model="r.delete_img">
                            <div class="col-sm-3 mb-4">
	                            <label>
	                                Judul SOP
	                            </label>
                                <input :required="!r.delete" class="form-control" :name="`sop[${i}][title]`" placeholder="Judul SOP" type="text" v-model="r.name">
                            	<div class="col-sm-12 mt-4 px-0">
                            		<div class="form-group custom-control custom-checkbox">
	                                	<input type="checkbox" :checked="r.active == 1 ? true : false" value="1" class="custom-control-input" :name="`sop[${i}][status]`" :id="`sop[${i}][status]`" @click="r.active = r.active ? 0 : 1">
					                    <label class="custom-control-label" :for="`sop[${i}][status]`">Aktif ?</label>
					                </div>
                            	</div>
                            </div>
                            <div class="col-sm-5 mb-4">
	                            <label>
	                                Deskripsi
	                            </label>
                                <textarea class="form-control" :name="`sop[${i}][desc]`" placeholder="Deskripsi SOP" v-model="r.desc" rows="3"></textarea>
                            </div>
                            <div class="col-sm-3 mb-4">
	                            <label>
	                                Gambar
	                            </label>
	                            <div class="w-100">
	                                <input class="form-control" :name="`sop[${i}][file]`" type="file" accept="image/*">
	                            	<button v-if="!r.delete_img" type="button" class="btn btn-danger btn-xs btn-block mt-1" @click="r.delete_img = r.delete_img ? 0 : 1">
	                            		<i class="fa fa-trash"></i>&nbsp;Hapus gambar
	                            	</button>
		                            <img draggable="false" alt="" :src="r.link" v-if="r.link && !r.delete_img" @click="window.open(r.link)" style="width: 20vh; cursor: pointer;" class="mt-2"/>
	                            </div>
                            </div>
                            <div class="col-sm-1 mb-4 pt-6">
                            	<button type="button" class="btn btn-danger btn-sm" @click="removeSop(i)">
                            		<i class="fa fa-trash"></i>
                            	</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-warning text-white" @click="addSop()">
                        	<i class="fa fa-plus"></i>
                        	Tambah SOP
                        </button>

                    	<hr class="border-dashed my-8">

                        <h3 class="text-primary">Social Media</h3>
                        <div class="w-100 text-center py-4" v-if="!(social_media && social_media.length > 0)">
                        	<span>Tidak ada</span>
                        </div>
                        <div :class="`form-group row mb-0 border mx-1 py-3 mb-4 rounded bg-lighten-primary ${r.delete && 'd-none'}`" v-for="(r, i) in social_media">
                        	<input type="hidden" :name="`socmed[${i}][id]`" v-model="r.id">
                        	<input type="hidden" :name="`socmed[${i}][delete]`" v-model="r.delete">
                            <div class="col-sm-3 mb-4">
	                            <label>
	                                Nama
	                            </label>
                                <input :required="!r.delete" class="form-control" :name="`socmed[${i}][name]`" placeholder="Nama social media" type="text" v-model="r.name">
                            	<div class="col-sm-12 mt-4 px-0">
                            		<div class="form-group custom-control custom-checkbox">
	                                	<input type="checkbox" :checked="r.active == 1 ? true : false" value="1" class="custom-control-input" :name="`socmed[${i}][status]`" :id="`socmed[${i}][status]`" @click="r.active = r.active ? 0 : 1">
					                    <label class="custom-control-label" :for="`socmed[${i}][status]`">Aktif ?</label>
					                </div>
                            	</div>
                            </div>
                            <div class="col-sm-3 mb-4">
	                            <label>
	                                Icon
	                            </label>
                                <input class="form-control" style="width: 100%;" :name="`socmed[${i}][icon]`" :id="`socmed[${i}][icon]`" placeholder="Icon" v-model="r.icon">
                            </div>
                            <div class="col-sm-4 mb-4">
	                            <label>
	                                Link
	                            </label>
	                            <div class="d-flex justify-content-between">
                                	<input :required="!r.delete" class="form-control" :name="`socmed[${i}][link]`" placeholder="Link" type="url" v-model="r.link">
	                            	<button v-if="r.link" type="button" class="btn btn-default border btn-sm ml-2" @click="window.open(r.link)">
	                            		<i class="fa fa-external-link"></i>
	                            	</button>
	                            </div>
                            </div>
                            <div class="col-sm-2 mb-4 pt-6">
                            	<button type="button" class="btn btn-danger btn-sm" @click="removeSocialMedia(i)">
                            		<i class="fa fa-trash"></i>
                            	</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-warning text-white" @click="addSocialMedia()">
                        	<i class="fa fa-plus"></i>
                        	Tambah Social Media
                        </button>
                        <hr/>
                        <div class="w-100 text-right">
                        	<button type="submit" class="btn btn-primary">
                        		<i class="fa fa-check mr-2"></i>Simpan perubahan
                        	</button>
                        </div>
					</form>
				</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("javascript")
<script>
	new Vue({
		el: '#edit-homepage',
		data: {
			company_name: `{{ $company?->profil_fullname_perusahaan }}`,
			company_shortname: `{{ $company?->profil_shortname_perusahaan }}`,
			company_email: `{{ $company?->profil_email_perusahaan }}`,
			company_telp: `{{ $company?->profil_telp_perusahaan }}`,
			company_address: `{{ $company?->profil_alamat_perusahaan }}`,
			company_desc: `{{ $company?->profil_desc_perusahaan }}`,
			company_fax: `{{ $company?->profil_fax_perusahaan }}`,
			company_whatsapp: `{{ $company?->profil_whatsapp_perusahaan }}`,
			app_name: `{{ $company?->profil_fullname_app }}`,
			app_shortname: `{{ $company?->profil_shortname_app }}`,
			app_desc: `{{ $company?->profil_app_desc }}`,
			app_icon: `{{ $company?->profil_app_icon }}`,
			app_bg: `{{ $company?->profil_background_image }}`,
			app_ketidakberpihakan: `{{ $company?->profil_ketidakperpihakan_file }}`,
			lembaga: [],
			sop: [],
			social_media: [],
		},
		mounted()
		{
			@foreach($lembaga_rows as $row)
				this.addLembaga({
					id: `{{$row->lem_id}}`,
					name: `{{$row->lem_name}}`,
					desc: `{{$row->lem_desc}}`,
					content: `{!! $row->lem_content !!}`,
					link: `{{$row->lem_external_link}}`,
					active: <?= $row->lem_status ? 1 : 0 ?>,
					delete: 0
				});
			@endforeach

			@foreach($sop_rows as $row)
				this.addSop({
					id: `{{$row->sop_id}}`,
					name: `{{$row->sop_name}}`,
					desc: `{{$row->sop_desc}}`,
					link: `{{$row->sop_image}}`,
					active: <?= $row->sop_status ? 1 : 0 ?>,
					delete_img: <?= $row->sop_image ? 0 : 1 ?>,
					delete: 0
				});
			@endforeach

			@foreach($socmed_rows as $row)
				this.addSocialMedia({
					id: `{{$row->socmed_id}}`,
					name: `{{$row->socmed_name}}`,
					icon: `{{$row->socmed_icon_cls}}`,
					link: `{{$row->socmed_link}}`,
					active: <?= $row->socmed_status ? 1 : 0 ?>,
					delete: 0
				});
			@endforeach
		},
		methods:
		{
			addLembaga: function(values)
			{
				this.lembaga.push(values ? values : {
					id: '',
					name: '',
					desc: '',
					content: '',
					link: 'https://',
					active: 1,
					delete: 0,
				});

				let last_index = 0;
				this.lembaga.map((r,i) => {
					last_index = i;
				});

				this.initEditor(`lembaga[${last_index}][content]`, values ? values.content : '');
			},
			addSop: function(values)
			{
				this.sop.push(values ? values : {
					id: '',
					name: '',
					desc: '',
					link: '',
					active: 1,
					delete_img: 1,
					delete: 0,
				});
			},
			addSocialMedia: function(values)
			{
				this.social_media.push(values ? values : {
					id: '',
					name: '',
					icon: '',
					link: 'https://',
					active: 1,
					delete: 0,
				});

				let last_index = 0;
				this.social_media.map((r,i) => {
					last_index = i;
				});

				this.initComboIcon(`socmed[${last_index}][icon]`, values ? values.content : '');
			},
			removeLembaga: function(index)
			{
				this.lembaga = this.lembaga.map((r, i) => {
					if (i === index) {
						r.delete = 1;
						r.link = '';
					}
					return r;
				});
			},
			removeSop: function(index)
			{
				this.sop = this.sop.map((r, i) => {
					if (i === index) r.delete = 1;
					return r;
				});
			},
			removeSocialMedia: function(index)
			{
				this.social_media = this.social_media.map((r, i) => {
					if (i === index) {
						r.delete = 1;
						r.link = '';
					}
					return r;
				});
			},
			initEditor: function(selector, content)
			{
				setTimeout(() =>
				{
			    	const editor = CKEDITOR.replace(selector, {data: content});
			    	editor.setData(content);
				}, 300);
			},
			initComboIcon: function(selector, value)
			{
				setTimeout(() =>
				{
					$(`[name='${selector}']`).combobox({
						editable: false,
						required: true,
						value: value,
						data: [
							{value: 'fab fa-facebook-square'},
							{value: 'fab fa-twitter-square'},
							{value: 'fab fa-instagram'},
							{value: 'fab fa-pinterest'},
							{value: 'fab fa-whatsapp'},
							{value: 'fab fa-telegram'},
							{value: 'fab fa-google-plus'},
							{value: 'fab fa-youtube'},
							{value: 'fab fa-wikipedia-w'},
							{value: 'fab fa-quora'},
						],
		                valueField: 'value',
		                textField: 'value',
		                formatter: function (row) {
		                    let iconData = `<i class="${row.value}"></i>`;
		                    return iconData + " " + row.value;
		                }
		            });
				}, 1000);
			}
		}
	});
</script>
@endpush