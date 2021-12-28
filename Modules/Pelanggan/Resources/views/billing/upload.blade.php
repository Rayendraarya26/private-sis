@extends("layouts.layout_app")

@section('title', 'Upload Kuitansi')

@push('css')
    {{--    <link rel="stylesheet" href="{{asset('assets/plugins/datepicker/bootstrap-datepicker3.min.css')}}">--}}
    <link rel="stylesheet" href="{{asset('assets/plugins/datetimepicker/tempusdominus-bootstrap-4.min.css')}}">
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
                            <h3 class="dt-card__title">Unggah bukti pembayaran #{{$data->bill_id}}</h3>
                            <i>Total: Rp {{moneyFormat($total_billing)}}</i>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form method="post"
                                      onsubmit="$('#btnSubmit').attr('disabled', true)"
                                      action="{{action("$module@processUpload", $data->bill_id)}}"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3"
                                               for="bill_payment_tipe">Tipe Pembayaran*</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="text" value="Transfer" disabled
                                                   id="bill_payment_tipe">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3" for="bill_payment_file">
                                            Bukti Pembayaran*
                                            <br>
                                            <small>(pdf/png/jpg)</small>
                                        </label>
                                        <div class="col-sm-8">
                                            <div class="custom-file">
                                                <input type="file" name="bill_payment_file" class="custom-file-input"
                                                       id="bill_payment_file"
                                                       accept="image/png,image/jpg,application/pdf">
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
                                        <button class="btn btn-success" type="submit" id="btnSubmit">
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
        });
    </script>
@endpush
