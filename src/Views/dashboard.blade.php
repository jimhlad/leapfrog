@extends('leapfrog::layout')

@section('content')
	<div class="row">
		@include('leapfrog::sidebar')
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
			<h1 class="page-header">Changelog</h1>
			<h2>1.0.2</h2>
			<ul>
				<li>Initial release</li>
				<li>Added CRUD Generator module for creating files according to a Controller-Service-Model pattern.</li>
			</ul>
		</div>
	</div>
@endsection
