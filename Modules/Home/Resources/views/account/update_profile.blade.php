@extends('layouts.layout_app')

@section('title', "Perbarui Profile")

@section('content')
    <!-- Site Content -->
    <div class="dt-content">
        <!-- Card -->
        <div class="dt-card">
            <!-- Card Body -->
            <div class="dt-card__body">
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
            <!-- Form -->
                <form method="post" action="{{route('update_profile')}}" enctype="multipart/form-data" onsubmit="$('#btn-submit').attr('disabled', 'true')">
                @csrf
                <!-- Form Group -->

                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 col-xl-3 py-2" style="border-radius: 5px;">
                            @if($user_data->user_picture)
                                <div class="row justify-content-center">
                                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-xl-10">
                                        <img src="{{$user_data->user_picture}}" class="w-100" alt="foto" style="border-radius: 50%;">
                                    </div>
                                </div>
                            @endif
                            <div class="w-100 my-2">
                                <input class="form-control" type="file" name="avatar" id="avatar" accept="image/*">
                                <small class="text-danger text-sm">* Format: (jpg/jpeg/png)</small>
                                @error('avatar')
                                <span class="text-danger d-block">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-9 col-xl-9">
                            <div class="form-group form-row">
                                <label class="col-md-3 col-form-label text-sm-right" for="email">
                                    Email<span class="text-danger ml-1">*</span>
                                </label>
                                <div class="col-md-7">
                                    <input type="text" name="email" class="form-control" id="email" value="{{$user_data->user_email}}" disabled required>
                                </div>
                            </div>

                            <div class="form-group form-row">
                                <label class="col-md-3 col-form-label text-sm-right" for="fullname">
                                    Nama Lengkap<span class="text-danger ml-1">*</span>
                                </label>

                                <div class="col-md-7">
                                    <input type="text" name="fullname" class="form-control" id="fullname" placeholder="Masukkan nama lengkap..." value="{{$user_data->user_fullname}}" required>
                                    @error('fullname')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <hr/>

                            <div class="gx-w-100">
                                <h3>Data Perusahaan</h3>
                            </div>

                            <div class="form-group form-row">
                                <label class="col-md-3 col-form-label text-sm-right" for="company_name">
                                    Nama Perusahaan<span class="text-danger ml-1">*</span>
                                </label>
                                <div class="col-md-7">
                                    <input type="text" name="company_name" class="form-control" id="company_name" placeholder="Masukkan nama perusahaan..." value="{{$user_data->sis_pelanggan?->cust_nama}}" required>
                                    @error('company_name')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group form-row">
                                <label class="col-md-3 col-form-label text-sm-right" for="company_owner_name">
                                    Nama Pemilik<span class="text-danger ml-1">*</span>
                                </label>
                                <div class="col-md-7">
                                    <input type="text" name="company_owner_name" class="form-control" id="company_owner_name" placeholder="Masukkan nama pemilik perusahaan..." value="{{$user_data->sis_pelanggan?->cust_nama_pemilik}}" required>
                                    @error('company_owner_name')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group form-row">
                                <label class="col-md-3 col-form-label text-sm-right" for="company_pimpinan_name">
                                    Nama Pimpinan<span class="text-danger ml-1">*</span>
                                </label>
                                <div class="col-md-7">
                                    <input type="text" name="company_pimpinan_name" class="form-control" id="company_pimpinan_name" placeholder="Masukkan nama pemilik perusahaan..." value="{{$user_data->sis_pelanggan?->cust_nama_pimpinan}}" required>
                                    @error('company_pimpinan_name')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group form-row">
                                <label class="col-md-3 col-form-label text-sm-right" for="company_wakil_name">
                                    Nama Wakil Manajemen<span class="text-danger ml-1">*</span>
                                </label>
                                <div class="col-md-7">
                                    <input type="text" name="company_wakil_name" class="form-control" id="company_wakil_name" placeholder="Masukkan nama pemilik perusahaan..." value="{{$user_data->sis_pelanggan?->cust_nama_wakil_manajemen}}" required>
                                    @error('company_wakil_name')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group form-row">
                                <label class="col-md-3 col-form-label text-sm-right" for="company_address">
                                    Alamat<span class="text-danger ml-1">*</span>
                                </label>
                                <div class="col-md-7">
                                    <textarea rows="4" name="company_address" class="form-control" id="company_address" placeholder="Masukkan alamat perusahaan..." required>{{$user_data->sis_pelanggan?->cust_alamat}}</textarea>
                                    @error('company_address')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group form-row">
                                <label class="col-md-3 col-form-label text-sm-right" for="company_country">
                                    Negara<span class="text-danger ml-1">*</span>
                                </label>
                                <div class="col-md-6">
                                    <select name="company_country" id="company_country" class="form-control" required>
                                        <option disabled selected>--Pilih Negara--</option>
                                        @foreach($master_negara as $negara)
                                            <option value="{{$negara->negara_id}}" {{$negara->negara_id == $user_data->sis_pelanggan?->negara_id ? 'selected' : ''}}>
                                                {{$negara->negara_nama}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_country')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group form-row" id="company_province_container">
                                <label class="col-md-3 col-form-label text-sm-right" for="company_province">
                                    Provinsi<span class="text-danger ml-1">*</span>
                                </label>
                                <div class="col-md-6">
                                    <select name="company_province" id="company_province" class="form-control">
                                        <option disabled selected>--Pilih Provinsi--</option>
                                    </select>
                                    @error('company_province')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group form-row" id="company_kabupaten_container">
                                <label class="col-md-3 col-form-label text-sm-right" for="company_kabupaten">
                                    Kabupaten/Kota<span class="text-danger ml-1">*</span>
                                </label>
                                <div class="col-md-6">
                                    <select name="company_kabupaten" id="company_kabupaten" class="form-control">
                                        <option disabled selected>--Pilih Kabupaten--</option>
                                    </select>
                                    @error('company_kabupaten')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group form-row" id="company_kecamatan_container">
                                <label class="col-md-3 col-form-label text-sm-right" for="company_kecamatan">
                                    Kecamatan<span class="text-danger ml-1">*</span>
                                </label>
                                <div class="col-md-6">
                                    <select name="company_kecamatan" id="company_kecamatan" class="form-control">
                                        <option disabled selected>--Pilih Kecamatan--</option>
                                    </select>
                                    @error('company_kecamatan')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group form-row">
                                <label class="col-md-3 col-form-label text-sm-right" for="company_no_akta">
                                    No Akta Pendirian<span class="text-danger ml-1">*</span>
                                </label>
                                <div class="col-md-6">
                                    <input type="text" name="company_no_akta" class="form-control" id="company_no_akta" placeholder="Masukkan nomor akta pendirian perusahaan..." value="{{$user_data->sis_pelanggan?->cust_nomor_akta_pendirian}}" required>
                                    @error('company_no_akta')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group form-row">
                                <label class="col-md-3 col-form-label text-sm-right" for="company_badan_hukum">
                                    Badan Hukum<span class="text-danger ml-1">*</span>
                                </label>
                                <div class="col-md-6">
                                    <select name="company_badan_hukum" id="company_badan_hukum" class="form-control" required>
                                        <option disabled selected>--Pilih Badan Hukum--</option>
                                        @foreach($master_badan_hukum as $row)
                                            <option value="{{$row->badan_hukum_id}}" {{$row->badan_hukum_id == $user_data->sis_pelanggan?->badan_hukum_id ? 'selected' : ''}}>
                                                {{$row->badan_hukum_nama}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_badan_hukum')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group form-row">
                                <label class="col-md-3 col-form-label text-sm-right" for="company_jenis">
                                    Jenis Perusahaan<span class="text-danger ml-1">*</span>
                                </label>
                                <div class="col-md-6">
                                    <select name="company_jenis" id="company_jenis" class="form-control" required>
                                        <option disabled selected>--Pilih Jenis Perusahaan--</option>
                                        @foreach($master_jenis_perusahaan as $row)
                                            <option value="{{$row->jenis_perusahaan_id}}" {{$row->jenis_perusahaan_id == $user_data->sis_pelanggan?->jenis_perusahaan_id ? 'selected' : ''}}>
                                                {{$row->jenis_perusahaan_nama}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_jenis')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group form-row">
                                <label class="col-md-3 col-form-label text-sm-right" for="company_telp">
                                    Telp (Perusahaan)<span class="text-danger ml-1">*</span>
                                </label>
                                <div class="col-md-6">
                                    <input type="text" name="company_telp" class="form-control" id="company_telp" placeholder="Masukkan telp perusahaan..." value="{{$user_data->sis_pelanggan?->cust_nomor_telp}}" required>
                                    @error('company_telp')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group form-row">
                                <label class="col-md-3 col-form-label text-sm-right" for="company_fax">
                                    Fax<span class="text-danger ml-1">*</span>
                                </label>
                                <div class="col-md-6">
                                    <input type="text" name="company_fax" class="form-control" id="company_fax" placeholder="Masukkan fax perusahaan..." value="{{$user_data->sis_pelanggan?->cust_nomor_fax}}" required>
                                    @error('company_fax')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group form-row">
                                <label class="col-md-3 col-form-label text-sm-right" for="company_cp">
                                    Nomor HP (CP)<span class="text-danger ml-1">*</span>
                                </label>
                                <div class="col-md-6">
                                    <input type="text" name="company_cp" class="form-control" id="company_cp" placeholder="Masukkan nomor hp perusahaan..." value="{{$user_data->sis_pelanggan?->cust_nomor_hp}}" required>
                                    @error('company_cp')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr/>

                    <!-- Form Group -->
                    <div class="form-group form-row">
                        <div class="col-xl-9 offset-xl-3">
                            <button type="submit" class="btn btn-primary text-uppercase" id="btn-submit">
                                <i class="fas fa-save"></i> Perbarui
                            </button>
                        </div>
                    </div>
                    <!-- /form group -->
                </form>
                <!-- /form -->

            </div>
            <!-- /card body -->

        </div>
        <!-- /card -->
    </div>
@endsection
@push("javascript")
    <script>
        $(function(){
            $('#company_country').on('change', function(e){
                if (e.target.value != 3) { // bukan indonesia
                    $('#company_province_container').hide();
                    $('#company_kabupaten_container').hide();
                    $('#company_kecamatan_container').hide();
                } else {
                    $('#company_province_container').show();
                    $('#company_kabupaten_container').show();
                    $('#company_kecamatan_container').show();
                }
            });

            @if($user_data->sis_pelanggan?->negara_id != 3)
                $('#company_province_container').hide();
                $('#company_kabupaten_container').hide();
                $('#company_kecamatan_container').hide();
            @endif
        });

        let cb_prov = $('#company_province').combobox({
            mode: 'remote',
            method: 'GET',
            editable: false,
            required: true,
            valueField: 'prov_id',
            textField: 'prov_nama',
            url:`{{url("$url/ajax?action=combo-provinsi")}}`,
            onSelect: function(row)
            {
                if (row.prov_id)
                {
                    $('#company_kabupaten').combobox('loadData', []);
                    $('#company_kecamatan').combobox('loadData', []);
                    $('#company_kabupaten').combobox('setValue', '');
                    $('#company_kecamatan').combobox('setValue', '');
                    $('#company_kabupaten').combobox('reload', `{{url("$url/ajax?action=combo-kabupaten")}}&id=${row.prov_id}`);
                }
            },
            onLoadSuccess: function(data)
            {
                if (data && data.length)
                {
                    data.map((row) =>
                    {
                        if (row.prov_id == '{{ $user_data->sis_pelanggan?->prov_id }}')
                        {
                            cb_prov.combobox('setValue', '{{ $user_data->sis_pelanggan?->prov_id }}');
                        }
                    });
                }
            }
        });

        let cb_kab = $('#company_kabupaten').combobox({
            mode: 'remote',
            method: 'GET',
            editable: false,
            required: true,
            valueField: 'kab_id',
            textField: 'kab_nama',
            url:`{{url("$url/ajax?action=combo-kabupaten")}}`,
            onSelect: function(row)
            {
                if (row.kab_id)
                {
                    $('#company_kecamatan').combobox('loadData', []);
                    $('#company_kecamatan').combobox('setValue', '');
                    $('#company_kecamatan').combobox('reload', `{{url("$url/ajax?action=combo-kecamatan")}}&id=${row.kab_id}`);
                }
            },
            onLoadSuccess: function(data)
            {
                if (data && data.length)
                {
                    data.map((row) =>
                    {
                        if (row.kab_id == '{{ $user_data->sis_pelanggan?->kab_id }}')
                        {
                            cb_kab.combobox('setValue', '{{ $user_data->sis_pelanggan?->kab_id }}');
                        }
                    });
                }
            }
        });

        let cb_kec = $('#company_kecamatan').combobox({
            mode: 'remote',
            method: 'GET',
            editable: false,
            required: true,
            valueField: 'kec_id',
            textField: 'kec_nama',
            url:`{{url("$url/ajax?action=combo-kecamatan")}}`,
            onLoadSuccess: function(data)
            {
                if (data && data.length)
                {
                    data.map((row) =>
                    {
                        if (row.kec_id == '{{ $user_data->sis_pelanggan?->kec_id }}')
                        {
                            cb_kec.combobox('setValue', '{{ $user_data->sis_pelanggan?->kec_id }}');
                        }
                    });
                }
            }
        });

    </script>
@endpush
