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
					<div class="media mb-5">
					  <i class="icon icon-link icon-xl mr-5"></i>
					  <div class="media-body">
						<span class="d-block text-light-gray f-12 mb-1">ID. INV</span>
						<a href="javascript:void(0)" id="id_inv_text"></a>
					  </div>
					</div>
					
					<div class="media mb-5">
					  <i class="icon icon-link icon-xl mr-5"></i>
					  <div class="media-body">
						<span class="d-block text-light-gray f-12 mb-1">No. INV</span>
						<a href="javascript:void(0)" id="no_inv"></a>
					  </div>
					</div>
					
					<div class="media mb-5">
					  <i class="icon icon-link icon-xl mr-5"></i>
					  <div class="media-body">
						<span class="d-block text-light-gray f-12 mb-1">NO. REF</span>
						<a href="javascript:void(0)" id="no_ref"></a>
					  </div>
					</div>
					
					<div class="media mb-5">
					  <i class="icon icon-link icon-xl mr-5"></i>
					  <div class="media-body">
						<span class="d-block text-light-gray f-12 mb-1">ID. REF</span>
						<a href="javascript:void(0)" id="id_ref"></a>
					  </div>
					</div>
					
					<div class="media mb-5">
					  <i class="icon icon-link icon-xl mr-5"></i>
					  <div class="media-body">
						<span class="d-block text-light-gray f-12 mb-1">Tanggal Invoice</span>
						<a href="javascript:void(0)" id="tgl_inv"></a>
					  </div>
					</div>
					
					<div class="media mb-5">
					  <i class="icon icon-link icon-xl mr-5"></i>
					  <div class="media-body">
						<span class="d-block text-light-gray f-12 mb-1">Tipe Transaksi</span>
						<a href="javascript:void(0)" id="tipe_trans"></a>
					  </div>
					</div>
					
					<div class="media mb-5">
					  <i class="icon icon-link icon-xl mr-5"></i>
					  <div class="media-body">
						<span class="d-block text-light-gray f-12 mb-1">Nama Pelaku Usaha</span>
						<a href="javascript:void(0)" id="nama_pu"></a>
					  </div>
					</div>
					
					<div class="media mb-5">
					  <i class="icon icon-link icon-xl mr-5"></i>
					  <div class="media-body">
						<span class="d-block text-light-gray f-12 mb-1">Alamat</span>
						<a href="javascript:void(0)" id="alamat1"></a>
					  </div>
					</div>
					
					<div class="media mb-5">
					  <i class="icon icon-link icon-xl mr-5"></i>
					  <div class="media-body">
						<span class="d-block text-light-gray f-12 mb-1">No. Telp</span>
						<a href="javascript:void(0)" id="No_telp"></a>
					  </div>
					</div>
					
					<div class="media mb-5">
					  <i class="icon icon-link icon-xl mr-5"></i>
					  <div class="media-body">
						<span class="d-block text-light-gray f-12 mb-1">Due Date</span>
						<a href="javascript:void(0)" id="duedate"></a>
					  </div>
					</div>
					
					<div class="media mb-5">
					  <i class="icon icon-link icon-xl mr-5"></i>
					  <div class="media-body">
						<span class="d-block text-light-gray f-12 mb-1">Total Invoice</span>
						<a href="javascript:void(0)" id="total_inv"></a>
					  </div>
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
