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

	</div>
</div>