@extends('layouts.layout_app')

@section('title', 'Tambah Group')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default" href="{{ url("$module") }}" style="margin-bottom: 20px">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Tambah Menu</h3>
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
                                <form method="post" action="{{url("$module")}}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" id="permission" name="permission">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="nama_guru">
                                            Nama Grup
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control"
                                                   placeholder="Masukkan Nama..."
                                                   type="text" name="group_name" id="group_name"
                                                   value="{{old('group_name')}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="no_telp">
                                            Deskripsi Grup
                                        </label>
                                        <div class="col-sm-8">
                                        <textarea class="form-control" placeholder="Masukkaan deskripsi..."
                                                  name="group_desc" id="group_desc">{{old('group_desc')}}</textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="no_telp">Aktif ?</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="group_is_active" id="group_is_active">
                                                <option value="yes" selected>Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="no_telp">Hak Akses</label>
                                        <div class="col-sm-8">
                                            <table id="treegrid" style="width:100%;min-height: 500px"></table>
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
    <script>
        $(function () {
            $('#treegrid').treegrid({
                method: 'get',
                url: `{{ url("$module/ajax/treegrid") }}`,
                idField: 'menu_id',
                checkbox: true,
                nowrap: false,
                singleSelect: false,
                toolbar: '#toolbar',
                // fitColumns: true,
                treeField: 'menu_name',
                columns: [[
                    {field: 'menu_name', title: 'Menu', width: 300},
                    // {field: 'menu_desc', title: 'Deskripsi', width: 200},
                    {field: 'menu_order', title: 'Urutan', width: 100},
                    {field: 'menu_is_active', title: 'Aktif?', width: 100},
                ]],
                onCheckNode: function () {
                    // Gaet checked nodes only
                    // const nodes = $('#treegrid').treegrid('getCheckedNodes');
                    // console.log(nodes);

                    // Get checked and setengah check
                    let menuIds = []
                    let n1 = $('#treegrid').treegrid('getCheckedNodes');	// get checked nodes
                    let n2 = $('#treegrid').treegrid('getCheckedNodes', 'indeterminate');	// get indeterminate nodes
                    let nodes = n1.concat(n2);

                    if (nodes.length > 0) nodes.map(e => menuIds.push(e.menu_id));
                    console.log(menuIds);
                    $("#permission").val(menuIds);
                }
            });
        });
    </script>
@endpush
