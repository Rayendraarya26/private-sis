<div class="modal fade" id="modalFormAdd1" tabindex="-1" role="dialog" aria-labelledby="modalFormAdd1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <!-- Modal Content -->
        <div class="modal-content">
        <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modalFormAdd1Title">
                    Tambah Laporan
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- /modal header -->
            <form action="{{action("$module@prosesAudit1")}}" method="post" onsubmit="$('#simpanBtnAdd1').attr('disabled', true)">
                @csrf
                <input type="hidden" name="id_reg" id="id_reg" value="{{$data_permohonan['id_reg']}}">
                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-10">
							<div class="form-group">
                                <label for="tgl_selesai">Tanggal Selesai</label>
                                <input type="text" name="tgl_selesai" id="tgl_selesai" class="easyui-datebox form-control" required="required" data-options="formatter:myformatter,parser:myparser">
                            </div>
							<div class="form-group">
                                <label for="keterangan">Tuliskan Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="hasil_audit">Hasil Audit</label>
                                <input type="text" name="hasil_audit" id="hasil_audit" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                </div>
                <!-- /modal body -->

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button id="simpanBtnAdd1" type="submit" class="btn btn-warning btn-sm">
                        <i class="fad fa-paper-plane"></i> Simpan
                    </button>
                </div>
                <!-- /modal footer -->

            </form>
        </div>
        <!-- /modal content -->
    </div>
</div>
