@extends('backend.layouts.html')
@section('content')
<section>
	@include('backend.layouts.success')
	@include('backend.layouts.error')
	<div class="row">
 		<div class="col-sm-6">
			@if(isset($transactions))
				<div class="alert alert-secondary" role="alert">
					<strong>Info:</strong><br/>
					<?php echo implode("<br/>",$transactions)?>
				</div>
			@else
				<h2>No transactions found</h2>
			@endif
		</div>
 		<div class="col-sm-6">
			<form id="upload" method="post" action="{{route('admin.finance.import') }}" enctype="multipart/form-data">
				{{ csrf_field() }}
				<input type="file" name="original_filename" />
				<p>Drag one file here or click in this area.</p>
			</form>
		</div>
	</div>
</section>
@endsection
