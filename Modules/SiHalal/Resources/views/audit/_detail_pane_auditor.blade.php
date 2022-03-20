@if(session('message'))
	<div class="alert alert-primary alert-dismissible fade show" role="alert">
		{!! session('message') !!}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">×</span>
		</button>
	</div>
@endif
<div id="ttDataAuditor" style="width:100%; min-width: 310px"></div>
<div id="toolbar" style="padding: 10px 0 10px 20px">
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