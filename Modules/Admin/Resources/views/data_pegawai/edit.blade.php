@extends('layouts.layout_app')

@section('title', 'Ubah Pegawai')

@section('content')
    <div class="dt-content" id="pegawai">
        <div class="row">
            <div class="col-xl-12">
                {{--<a class="btn btn-sm btn-default" href="{{ url("$url") }}" style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali
                </a>--}}
                <div class="dt-card">
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
                                <form method="post" action="{{action("$module@store")}}" enctype="multipart/form-data"
                                      @submit.prevent="submitForm" id="submitForm">
                                    @csrf
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="nip">Nomor Induk Pegawai (NIP)</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan NIP ..."
                                                   type="text"
                                                   name="nip" id="nip" value="{{$data->master_pegawai?->peg_nip}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="fullname">Fullname*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan nama lengkap ..."
                                                   type="text"
                                                   name="fullname" id="fullname"
                                                   value="{{$data->master_pegawai?->peg_nama}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="email">Email*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan email..." type="email"
                                                   name="email" id="email" value="{{$data->user_email}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="no_telp">No Telp</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan nomor telp ..."
                                                   type="text"
                                                   name="no_telp" id="no_telp"
                                                   value="{{$data->master_pegawai?->peg_telp}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="alamat">Alamat</label>
                                        <div class="col-sm-8">
                                            <textarea name="alamat" id="alamat" class="form-control"
                                                      placeholder="Masukkan alamat ...">{{$data->master_pegawai?->peg_alamat}}</textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="password">Kata sandi*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan kata sandi..."
                                                   type="password" name="password" id="password"
                                                   value="{{old('password')}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="password_confirmation">Konfirmasi Password*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control"
                                                   placeholder="Masukkan ulang kata sandi..."
                                                   type="password" name="password_confirmation"
                                                   id="password_confirmation"
                                                   value="{{old('password_confirmation')}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="foto">
                                            Foto
                                            <small>(jpg/jpeg/png)</small>
                                        </label>
                                        <div class="col-sm-4">
                                            <input class="form-control-file" type="file" name="foto" id="foto"
                                                   accept="image/*">
                                        </div>
                                        <div class="col-sm-4">
                                            @if(!empty($data->user_picture))
                                                <div style="text-align: center; justify-content: center">
                                                    <img src="{{$data->user_picture}}" style="width: 200px" alt="foto">
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="no_telp">Group</label>
                                        <div class="col-sm-8">
                                            <div class="row">
                                                @foreach($groups as $group)
                                                    <div class="col-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox"
                                                                   name="group[]"
                                                                   value="{{$group->group_id}}" {{array_search($group->group_id, array_column($selected_group, "ug_group_id")) !== false ? 'checked' : ''}}>
                                                            {{$group->group_name}}
                                                        </label>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio"
                                                                   name="group_default" value="{{$group->group_id}}"
                                                                {{$group->group_id == $default_group ? "checked" :""}}>
                                                            default
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="signature_file">
                                            Tanda Tangan
                                            <br>
                                            <small>(anda dapat pilih salah satu atau mengisikan semuanya)</small>
                                        </label>
                                        <div class="col-sm-4">
                                            <input class="form-control-file" type="file" id="signature_file"
                                                   accept="image/*" name="signature_file">
                                            @if(!empty($data->master_pegawai->peg_ttd_file))
                                                <div style="text-align: center; justify-content: center">
                                                    <img src="{{asset($data->master_pegawai->peg_ttd_file)}}"
                                                         style="max-width: 300px" alt="foto">
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-sm-4">
                                            <div style="padding-bottom: 10px">
                                                <button class="btn btn-primary" type="button"
                                                        @click="isUpdateSignature = !isUpdateSignature">
                                                    Update TTD
                                                </button>
                                            </div>
                                            <div v-if="isUpdateSignature">
                                                <canvas id="signature_base64"
                                                        style="border: 1px solid black; width: 300px; height: 300px"></canvas>
                                                <br>
                                                <button type="button" class="btn btn-xs btn-warning"
                                                        @click="signatureClear">Reset
                                                </button>
                                            </div>
                                            <img src="{{$data->master_pegawai?->peg_ttd_base64}}" alt="signature"
                                                 style="max-width: 300px">
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
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script>
        new Vue({
            el: '#pegawai',
            data: {
                canvas: null,
                signaturePad: null,
                isUpdateSignature: false,
            },
            watch: {
                isUpdateSignature: function () {
                    if (this.isUpdateSignature) {
                        setTimeout(() => {
                            this.canvas = document.querySelector("#signature_base64");
                            console.log(this.canvas);
                            this.canvas.width  = 300;
                            this.canvas.height = 300;
                            this.signaturePad  = new SignaturePad(this.canvas);
                        }, 500);
                    }
                }
            },
            mounted() {
                // window.addEventListener("resize", this.resizeCanvas);
            },
            methods: {
                signatureClear() {
                    this.signaturePad.clear();
                },
                resizeCanvas() {
                    // let ratio          = Math.max(window.devicePixelRatio || 1, 1);
                    // console.log(ratio);
                    let ratio          = 1;
                    this.canvas.width  = this.canvas.offsetWidth * ratio;
                    this.canvas.height = this.canvas.offsetHeight * ratio;
                    this.canvas.getContext("2d").scale(ratio, ratio);
                    this.signaturePad.clear();
                },
                submitForm() {
                    const form   = document.getElementById('submitForm');
                    let formData = new FormData(form);

                    if (this.isUpdateSignature) {
                        formData.append("signature_base64", this.signaturePad.toDataURL())
                    }

                    if ($("#password").val() == "") {
                        formData.delete('password')
                        formData.delete('password_confirmation')
                    }

                    $.ajax({
                        url: `{{action("$module@update", $data->user_id)}}`,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: async function (res) {
                            toastCenter({
                                type: 'success',
                                title: res.message
                            })

                            setTimeout(() => location.href = "{{url("$url")}}", 1000)
                        },
                        error: function (xhr) {
                            if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                            else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                        }
                    });

                    return false;
                }
            }
        });
    </script>
@endpush
