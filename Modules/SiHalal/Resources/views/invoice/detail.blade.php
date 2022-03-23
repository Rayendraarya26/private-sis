<div class="modal fade" id="modalFormLunas" tabindex="-1" role="dialog" aria-labelledby="modalFormLunas" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <!-- Modal Content -->
        <div class="modal-content">
        <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modalFormLunasTitle">
                    Tambah Biaya
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- /modal header -->
                <input type="hidden" name="id_inv" id="id_inv" value="">
                <!-- Modal Body -->
                <div class="modal-body">
					<div class="form-group">
						<label for="id_inv_text" class="col-form-label">ID. INV :</label>
						<input class="form-control" id="id_inv_text" type="text" disabled value="">
					</div>
					
					<div class="form-group">
						<label for="no_inv" class="col-form-label">No. INV :</label>
						<input class="form-control" id="no_inv" type="text" disabled value="">
					</div>
					
					<div class="form-group">
						<label for="no_ref" class="col-form-label">NO. REF :</label>
						<input class="form-control" id="no_ref" type="text" disabled value="">
					</div>
					
					<div class="form-group">
						<label for="id_ref" class="col-form-label">ID. REF :</label>
						<input class="form-control" id="id_ref" type="text" disabled value="">
					</div>
					
					<div class="form-group">
						<label for="tgl_inv" class="col-form-label">Tanggal Invoice :</label>
						<input class="form-control" id="tgl_inv" type="text" disabled value="">
					</div>
					
					<div class="form-group">
						<label for="tipe_trans" class="col-form-label">Tipe Transaksi :</label>
						<input class="form-control" id="tipe_trans" type="text" disabled value="">
					</div>
					
					<div class="form-group">
						<label for="nama_pu" class="col-form-label">Nama Pelaku Usaha :</label>
						<input class="form-control" id="nama_pu" type="text" disabled value="">
					</div>
					
					<div class="form-group">
						<label for="alamat1" class="col-form-label">Alamat :</label>
						<textarea class="form-control" id="alamat1" disabled></textarea>
					</div>
					
					<div class="form-group">
						<label for="No_telp" class="col-form-label">No. Telp :</label>
						<input class="form-control" id="No_telp" type="text" disabled value="">
					</div>
					
					<div class="form-group">
						<label for="duedate" class="col-form-label">Due Date :</label>
						<input class="form-control" id="duedate" type="text" disabled value="">
					</div>
					
					<div class="form-group">
						<label for="total_inv" class="col-form-label">Total Invoice :</label>
						<input class="form-control" id="total_inv" type="text" disabled value="">
					</div>
					
					<div class="media mb-5">
					  <i class="icon icon-link icon-xl mr-5"></i>
					  <div class="media-body">
						<span class="d-block text-light-gray f-12 mb-1">File Invoice</span>
						<a target="blank" href="javascript:void(0)" id="file_inv">[ Download ]</a>
					  </div>
					</div>
                </div>
                <!-- /modal body -->

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button id="simpanBtnLunas" type="submit" class="btn btn-warning btn-sm" onclick="confirmLunas()">
                        <i class="fad fa-paper-plane"></i> Lunas
                    </button>
                </div>
                <!-- /modal footer -->
        </div>
        <!-- /modal content -->
    </div>
</div>
