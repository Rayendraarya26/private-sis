@if(session('message'))
	<div class="alert alert-primary alert-dismissible fade show" role="alert">
		{!! session('message') !!}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">×</span>
		</button>
	</div>
@endif
<div id="ttDataJadwal" style="width:100%; min-width: 310px"></div>
<div id="toolbarJadwal" style="padding: 10px 0 10px 20px">
	<div class="row">
		@if(authorized("{$module}@addJadwal"))
			<div>
				<a href="javascript:void(0)" class="btn btn-outline-success btn-xs" onclick="addModalJadwal()"> 
					<i class="fas fa-plus"></i> Tambah Jadwal
				</a>
			</div>
			&nbsp;&nbsp;&nbsp;
		@endif							
		@if(authorized("{$module}@destroyJadwal"))
			<div class="datagrid-btn-separator"></div>
			&nbsp;&nbsp;&nbsp;
			<div>
				<button class="btn btn-outline-danger btn-xs" onclick="confirmDeleteJadwal()"><i class="fas fa-trash"></i> Hapus Jadwal</button>
			</div>
		@endif
	</div>
</div>
<div class="modal fade" id="modalFormJadwalAdd" tabindex="-1" role="dialog" aria-labelledby="modalFormJadwalAdd" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalFormJadwalAddTitle">
                    Tambah Jadwal
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{action("$module@addJadwal")}}" method="post" onsubmit="$('#simpanBtnJadwalAdd').attr('disabled', true)">
                @csrf
                <input type="hidden" name="id_reg" id="id_reg" value="{{$data_permohonan['id_reg']}}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-10">
							<div class="form-group">
                                <label for="jadwal_awal">Jadwal Awal</label>
                                <input type="text" name="jadwal_awal" id="jadwal_awal" class="easyui-datebox form-control" required="required" data-options="formatter:myformatter,parser:myparser">
                            </div>
							<div class="form-group">
                                <label for="jadwal_akhir">Jadwal Akhir</label>
                                <input type="text" name="jadwal_akhir" id="jadwal_akhir" class="easyui-datebox form-control" required="required" data-options="formatter:myformatter,parser:myparser">
                            </div>
							<div class="form-group">
                                <label for="jml_hari">Jumlah Hari</label>
                                <input type="number" name="jml_hari" id="jml_hari" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="simpanBtnJadwalAdd" type="submit" class="btn btn-warning btn-sm">
                        <i class="fad fa-paper-plane"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFormJadwalEdit" tabindex="-1" role="dialog" aria-labelledby="modalFormJadwalEdit" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalFormJadwalEditTitle">
                    Edit Jadwal
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{action("$module@updateJadwal")}}" method="post" onsubmit="$('#simpanBtnJadwalEdit').attr('disabled', true)">
                @csrf
                <input type="hidden" name="id_reg" id="edit_id_reg" value="{{$data_permohonan['id_reg']}}">
                <input type="hidden" name="id_audit" id="edit_id_audit" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-10">
							<div class="form-group">
                                <label for="jadwal_awal">Jadwal Awal</label>
                                <input type="text" name="jadwal_awal" id="edit_jadwal_awal" class="easyui-datebox form-control" required="required" data-options="formatter:myformatter,parser:myparser">
                            </div>
							<div class="form-group">
                                <label for="jadwal_akhir">Jadwal Akhir</label>
                                <input type="text" name="jadwal_akhir" id="edit_jadwal_akhir" class="easyui-datebox form-control" required="required" data-options="formatter:myformatter,parser:myparser">
                            </div>
							<div class="form-group">
                                <label for="jml_hari">Jumlah Hari</label>
                                <input type="number" name="jml_hari" id="edit_jml_hari" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="simpanBtnJadwalEdit" type="submit" class="btn btn-warning btn-sm">
                        <i class="fad fa-paper-plane"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>