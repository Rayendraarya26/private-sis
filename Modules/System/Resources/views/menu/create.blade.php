@extends('layouts.layout_app')

@section('title', 'Tambah Menu')

@push('css')
    <style>
        .fas{
            background-image: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .fas::before{
            font-size: 14px;
        }
        .fas.tree-folder:not(.tree-file)::before{
            content: "\f105"
        }
        .fas.tree-folder-open:not(.tree-file)::before{
            content: "\f107"
        }
    </style>
@endpush

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default" href="{{ url("$module") }}" style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali
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
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="menu_parent_id">
                                            Induk Menu
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control easyui-combotreegrid"
                                                   placeholder="Pilih induk menu..."
                                                   type="text" name="menu_parent_id" id="menu_parent_id"
                                                   value="{{old('menu_parent_id')}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="menu_name">
                                            Nama Menu
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control"
                                                   placeholder="Masukkan nama..."
                                                   type="text" name="menu_name" id="menu_name"
                                                   value="{{old('menu_name')}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="menu_desc">
                                            Deskripsi Menu
                                        </label>
                                        <div class="col-sm-8">
                                <textarea class="form-control" placeholder="Masukkan deskripsi..."
                                          name="menu_desc" id="menu_desc">{{old('menu_desc')}}</textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="menu_order">
                                            Urutan
                                        </label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" value="{{old('menu_order')}}"
                                                   placeholder="Masukkan urutan..."
                                                   name="menu_order" id="menu_order">
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="menu_icon">
                                            Icon
                                        </label>
                                        <div class="col-sm-8">
                                            <input id="menu_icon" name="menu_icon" style="width: 100%">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="menu_is_active">Aktif ?</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="menu_is_active" id="menu_is_active">
                                                <option value="yes">Ya</option>
                                                <option value="no">Tidak</option>
                                            </select>
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
            $('#menu_icon').combobox({
                method: 'get',
                mode:'remote',
                remoteFilter: true,
                clientPaging: false,
                url: '{{url("$module/ajax/data-icon")}}',
                valueField: 'icon_code',
                textField: 'icon_name',
                icons: [{
                    iconCls: 'fas fa-close',
                    handler: function (e) {
                        $(e.data.target).combobox('clear').combobox('textbox').focus();
                    }
                }],
                onChange: function (value) {
                    if (value) {
                        $(this).combobox('getIcon', 0).css('visibility', 'visible')
                    } else {
                        $(this).combobox('getIcon', 0).css('visibility', 'hidden')
                    }
                },
                formatter: function (row) {
                    let iconData = `<i class="${row.icon_code}"></i>`;
                    return iconData + " " + row.icon_code;
                }
            });


            $('#menu_parent_id').combotreegrid({
                method: 'get',
                width: "100%",
                url: `{{ url("$module/ajax/treegrid") }}`,
                idField: 'id',
                nowrap: false,
                singleSelect: true,
                toolbar: '#toolbar',
                fitColumns: true,
                treeField: 'menu_name',
                columns: [[
                    {field: 'menu_name', title: 'Nama Menu', width: 100},
                    {field: 'menu_is_active', title: 'Aktif ?', width: 20},
                    // {field: 'menu_order', title: 'Urutan', width: 10},
                ]]
            });
        });
    </script>
@endpush
