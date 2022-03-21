@if(session('message'))
	<div class="alert alert-primary alert-dismissible fade show" role="alert">
		{!! session('message') !!}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">×</span>
		</button>
	</div>
@endif
<div id="ttDataAuditor" style="width:100%; min-width: 310px"></div>
<div id="toolbarAuditor" style="padding: 10px 0 10px 20px">
	<div class="row">
		@if(authorized("{$module}@addAuditor"))
			<div>
				<a href="javascript:void(0)" class="btn btn-outline-success btn-xs" onclick="addModalAuditor()"> 
					<i class="fas fa-plus"></i> Tambah Auditor
				</a>
			</div>
			&nbsp;&nbsp;&nbsp;
		@endif							
		@if(authorized("{$module}@destroyAuditor"))
			<div class="datagrid-btn-separator"></div>
			&nbsp;&nbsp;&nbsp;
			<div>
				<button class="btn btn-outline-danger btn-xs" onclick="confirmDeleteAuditor()"><i class="fas fa-trash"></i> Hapus Auditor</button>
			</div>
		@endif
	</div>
</div>

<div class="modal fade" id="modalFormAuditorAdd" tabindex="-1" role="dialog" aria-labelledby="modalFormAuditorAdd" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalFormAuditorAddTitle">
                    Tambah Auditor
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{action("$module@addAuditor")}}" method="post" onsubmit="$('#simpanBtnAuditorAdd').attr('disabled', true)">
                @csrf
                <input type="hidden" name="id_reg" id="id_reg" value="{{$data_permohonan['id_reg']}}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-10">
							<div class="form-group">
                                <label for="auditor_id">Auditor : </label>
								    <select id="auditor_id" class="easyui-combobox form-control" name="auditor_id" style="width:250px;"
										data-options="
											method: 'get',
											valueField: 'id',
											textField: 'text',
											remoteSort: false,
											remoteFilter: false,
											url:'{{ url("$url/ajax?action=combobox-auditor") }}'
										"></select>
                            </div>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="simpanBtnAuditorAdd" type="submit" class="btn btn-warning btn-sm">
                        <i class="fad fa-paper-plane"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>