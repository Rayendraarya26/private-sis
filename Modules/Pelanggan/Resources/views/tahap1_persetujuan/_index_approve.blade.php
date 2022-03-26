<div class="modal fade" id="modalApprove" tabindex="-1" role="dialog"
     aria-labelledby="modalApprove" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">

        <!-- Modal Content -->
        <div class="modal-content">

        @csrf
        <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modalApproveTitle">
                    Unggah Berkas Persetujuan
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="alert alert-info">
                <small>Anda dapat mengunggah dokumen sekarang atau setelah melakukan persetujuan</small>
            </div>
            <!-- /modal header -->
            <form action="{{action("$module@approveTemuan")}}" method="post" enctype="multipart/form-data"
                  onsubmit="$('#simpanApprove').attr('disabled', true)">
                @csrf
                <input type="hidden" name="aud_thp1_id" id="approve_aud_thp1_id">
                <input type="hidden" name="status" value="setuju">
                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-10">
                            <div class="form-group">
                                <label for="berkas_ket">
                                    Unggah <b>Scan Surat Tugas</b> yang sudah diberi TTD dan cap
                                </label>
                                <input type="file" class="form-control" id="file_surat_tugas" name="file_surat_tugas"
                                       accept="application/pdf">
                            </div>
                            <div class="form-group">
                                <label for="berkas_ket">
                                    Unggah <b>Scan Notulen</b> yang sudah diberi TTD dan cap
                                </label>
                                <input type="file" class="form-control" id="file_notulen" name="file_notulen"
                                       accept="application/pdf">
                            </div>
                            <div class="form-group">
                                <label for="berkas_ket">
                                    Unggah <b>Scan Subkontrak</b> yang sudah diberi TTD dan cap
                                    <small>(optional)</small>
                                </label>
                                <input type="file" class="form-control" id="file_subkontrak" name="file_subkontrak"
                                       accept="application/pdf">
                            </div>

                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                </div>
                <!-- /modal body -->

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button id="simpanApprove" type="submit" class="btn btn-primary btn-sm">
                        <i class="fad fa-paper-plane"></i> Setuju
                    </button>
                </div>
                <!-- /modal footer -->

            </form>
        </div>
        <!-- /modal content -->
    </div>
</div>
