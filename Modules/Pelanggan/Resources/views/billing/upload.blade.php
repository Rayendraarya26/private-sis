@extends("layouts.layout_app")

@section('title', 'Upload Kuitansi')

@push('css')
    {{--    <link rel="stylesheet" href="{{asset('assets/plugins/datepicker/bootstrap-datepicker3.min.css')}}">--}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css">
@endpush

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
                <div class="dt-card">
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

                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Unggah bukti pembayaran #{{$data->bill_nomor_billing}}</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form method="post"
                                      action="{{action("$module@processUpload", $data->bill_nomor_billing)}}"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="bill_payment_tipe">Tipe Pembayaran*</label>
                                        <div class="col-sm-8">
                                            <select name="bill_payment_tipe" id="bill_payment_tipe"
                                                    class="form-control">
                                                <option disabled selected>--Pilih Tipe Pembayaran--</option>
                                                <option value="tunai">Cash</option>
                                                <option value="non-tunai">Transfer</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="bill_payment_file">
                                            Bukti Pembayaran*</label>
                                        <div class="col-sm-8">
                                            <div class="custom-file">
                                                <input type="file" name="bill_payment_file" class="custom-file-input"
                                                       id="bill_payment_file">
                                                <label class="custom-file-label" for="bill_payment_file">Unggah bukti
                                                    pembayaran...</label>
                                            </div>
                                            @if(!empty($data->bill_payment_file))
                                                <small>
                                                    <a href="{{asset($data->bill_payment_file)}}" target="_blank">
                                                        <i class="fad fa-download"></i> Download bukti pembayaran
                                                    </a>
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="bill_payment_date">
                                            Tanggal Pembayaran</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Masukkan tanggal pembayaran..."
                                                   data-toggle="datetimepicker" data-target="#bill_payment_date"
                                                   type="text" name="bill_payment_date" id="bill_payment_date"
                                                   value="{{$data->bill_payment_date ?? old('bill_payment_date')}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="bill_payment_note">
                                            Keterangan
                                        </label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control"
                                                      placeholder="Masukkaan keterangan pembayaran..."
                                                      name="bill_payment_note"
                                                      id="bill_payment_note">{{$data->bill_payment_note ?? old('bill_payment_note')}}</textarea>
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
    <script src="{{asset('assets/plugins/datetimepicker/tempusdominus-bootstrap-4.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            // $('#bill_payment_date').datepicker({
            //     autoclose: true,
            //     format: 'yyyy-mm-dd',
            //     todayHighlight: true,
            // });


            $('#bill_payment_date').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss',
                showClose: true,
            });


            @if($data->bill_payment_tipe != null )
            $("#bill_payment_tipe").val('{{$data->bill_payment_tipe}}')
            @endif

            @if(old('bill_payment_tipe'))
            $("#bill_payment_tipe").val('{{old('bill_payment_tipe')}}')
            @endif
        });
    </script>
@endpush
