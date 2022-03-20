<div class="modal fade" id="modalFormUpdate" tabindex="-1" role="dialog" aria-labelledby="modalFormUpdate" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <!-- Modal Content -->
        <div class="modal-content">
        <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modalFormUpdateTitle">
                    Tambah Biaya
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- /modal header -->
            <form action="{{action("$module@updateBiaya")}}" method="post" onsubmit="$('#simpanBtnUpdate').attr('disabled', true)">
                @csrf
                <input type="hidden" name="id_reg" id="edit_id_reg" value="{{$data_permohonan['id_reg']}}">
                <input type="hidden" name="id_biaya" id="edit_id_biaya" value="">
                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-10">
                            <div class="form-group">
                                <label for="keterangan">Tuliskan Keterangan</label>
                                <textarea name="keterangan" id="edit_keterangan" class="form-control"></textarea>
                            </div>
							<div class="form-group">
                                <label for="qty">Qty</label>
                                <input type="number" name="qty" id="edit_qty" class="form-control">
                            </div>
							<div class="form-group">
                                <label for="harga">Harga (Rp)</label>
                                <input type="number" name="harga" id="edit_harga" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                </div>
                <!-- /modal body -->

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button id="simpanBtnUpdate" type="submit" class="btn btn-warning btn-sm">
                        <i class="fad fa-paper-plane"></i> Ubah
                    </button>
                </div>
                <!-- /modal footer -->

            </form>
        </div>
        <!-- /modal content -->
    </div>
</div>
