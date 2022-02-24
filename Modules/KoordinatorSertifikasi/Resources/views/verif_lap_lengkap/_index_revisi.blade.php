<div class="modal fade" id="modalRevisi" tabindex="-1" role="dialog"
     aria-labelledby="modalRevisi" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">

        <!-- Modal Content -->
        <div class="modal-content">

        @csrf
        <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modalRevisiTitle">
                    Revisi Verifikasi Laporan
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- /modal header -->
            <form action="{{action("$module@verifikasi")}}" method="post"
                  onsubmit="$('#simpanRevisi').attr('disabled', true)">
                @csrf
                <input type="hidden" name="jadw_id" id="revision_jadw_id">
                <input type="hidden" name="lap_lengkp_verifikasi_status" value="revisi">
                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-10">


                            <div class="form-group">
                                <label for="catatan">
                                    Tuliskan Keterangan
                                </label>
                                <textarea name="lap_lengkp_revisi_note" id="lap_lengkp_revisi_note" class="form-control"></textarea>
                            </div>


                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                </div>
                <!-- /modal body -->

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button id="simpanRevisi" type="submit" class="btn btn-warning btn-sm">
                        <i class="fad fa-paper-plane"></i> Revisi
                    </button>
                </div>
                <!-- /modal footer -->

            </form>
        </div>
        <!-- /modal content -->
    </div>
</div>
