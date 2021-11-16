<!-- Verifikasi Modal -->
<div class="modal fade" id="verifikasi-modal" tabindex="-1" role="dialog" aria-labelledby="model-4"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">

        <!-- Modal Content -->
        <div class="modal-content">
            <form action="{{action("$module@verifTemuan", [$jadwalID, $lksID])}}" method="post">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h3 class="modal-title" id="verifikasi-title">Verifikasi LKS</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- /modal header -->

                <!-- Modal Body -->
                <div class="modal-body" id="verifikasi-content">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input value="memadai" type="radio" id="lks_status1" name="lks_status"
                               class="custom-control-input">
                        <label class="custom-control-label" for="lks_status1">Memadai</label>
                    </div>

                    <div class="custom-control custom-radio custom-control-inline">
                        <input value="tidak-memadai" type="radio" id="lks_status2" name="lks_status"
                               class="custom-control-input">
                        <label class="custom-control-label" for="lks_status2">Tidak Memadai</label>
                    </div>
                </div>
                <!-- /modal body -->

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-xs">Save changes</button>
                </div>

                @csrf
            </form>
        </div>
        <!-- /modal content -->

    </div>
</div>
<!-- /modal -->
