@extends('leapfrog::layout')

@section('content')
	<div class="row">
		@include('leapfrog::sidebar')
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
			<h1 class="page-header">Changelog</h1>
			<h2>1.1.1</h2>
			<ul>
				<li>Made installation process easier by changing the way dependencies are registered</li>
			</ul>
			<h2>1.1.0</h2>
			<ul>
				<li>Added support for the creation of foreign key columns in migration files</li>
				<li>Ability to generate boilerplate for model relationship methods</li>
				<li>Ability to choose default value for certain database field types</li>
				<li>Support for the creation of a select menu form field</li>
				<li>Added config option allowing migrations to be automatically run (default: false)</li>
			</ul>
			<h2>1.0.2</h2>
			<ul>
				<li>Initial release</li>
				<li>Added CRUD Generator module for creating files according to a Controller-Service-Model pattern.</li>
			</ul>
		</div>
	</div>
@endsection
