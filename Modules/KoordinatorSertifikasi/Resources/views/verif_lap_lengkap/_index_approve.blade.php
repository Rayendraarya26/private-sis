<div class="modal fade" id="modalApprove" tabindex="-1" role="dialog"
     aria-labelledby="modalApprove" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">

        <!-- Modal Content -->
        <div class="modal-content">

        @csrf
        <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modalApproveTitle">
                    Verifikasi Persetujuan
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- /modal header -->
            <form action="{{action("$module@verifikasi")}}" method="post" enctype="multipart/form-data"
                  onsubmit="$('#simpanApprove').attr('disabled', true)">
                @csrf
                <input type="hidden" name="jadw_id" id="approve_jadw_id">
                <input type="hidden" name="lap_lengkp_verifikasi_status" value="ya">
                <input type="hidden" name="lap_lengkp_revisi_note" value="">
                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
							<p>Apakah anda yakin ingin melanjutkan verifikasi ini?</p>
						</div>
                    </div>
                </div>
                <!-- /modal body -->

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button id="simpanApprove" type="submit" class="btn btn-primary btn-sm">
                        <i class="fad fa-paper-plane"></i> Simpan
                    </button>
                </div>
                <!-- /modal footer -->

            </form>
        </div>
        <!-- /modal content -->
    </div>
</div>
