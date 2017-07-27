@extends('leapfrog::layout')

@section('content')
	<div class="row">
		@include('leapfrog::sidebar')
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
			<h1 class="page-header">Dashboard</h1>
			<p>Welcome to LeapFrog! Eventually this will be an awesome dashboard.</p>
			<hr>
			<h2>Just starting your project?</h2>
			<form method="post" action="">
				<p>Generate some common base classes for the Controller-Service-Model design pattern:</p>
				<div class="well">
					<div class="checkbox">
					    <label>
					     	<input name="base_service" type="checkbox"> BaseService
					    </label>
					 </div>
					 <div class="checkbox">
					    <label>
					     	<input name="base_controller" type="checkbox"> BaseController
					    </label>
					 </div>
				</div>
				<p>How about some other commonly needed Service classes:</p>
				<div class="well">
					<div class="checkbox">
					    <label>
					     	<input name="file_service" type="checkbox"> FileService
					    </label>
					 </div>
				</div>
				<button type="submit" class="btn btn-primary pull-right">Generate</button>
			</form>
		</div>
	</div>
@endsection