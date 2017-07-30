@extends('leapfrog::layout')

@section('content')
	<div class="row">
		@include('leapfrog::sidebar')
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
			<h1 class="page-header">CRUD Generator</h1>
				<crud-form
					generate_url="{{ route('leapfrog.crud.generate') }}"
					models_path="{{ config('leapfrog.paths.models') }}"
					controllers_path="{{ config('leapfrog.paths.controllers') }}"
					services_path="{{ config('leapfrog.paths.services') }}"
					requests_path="{{ config('leapfrog.paths.requests') }}"
					views_path="{{ config('leapfrog.paths.views') }}"
				>
					
				</crud-form>
		</div>
	</div>
@endsection