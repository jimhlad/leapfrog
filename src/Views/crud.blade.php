@extends('leapfrog::layout')

@section('content')
	<div class="row">
		@include('leapfrog::sidebar')
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
			<h1 class="page-header">CRUD Generator</h1>
			<form method="post" action="">
				<crud-form></crud-form>
			</form>
		</div>
	</div>
@endsection