@extends('leapfrog::layout')

@section('content')
	<div class="row">
		@include('leapfrog::sidebar')
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
			<h1 class="page-header">CRUD Generator</h1>
			<form method="post" action="">
				<p>Start off by choosing an entity name:</p>
				<div class="well">
					<div class="form-group">
					    <label for="entity_name">Entity Name</label>
					    <input name="entity_name" type="text" class="form-control" placeholder="e.g. Truck" />
					</div>
				</div>
				<p>What database fields are we thinking?</p>
				<div class="well">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
					    		<label for="field_name">Field Name</label>
					    		<input name="field_name" type="text" class="form-control" placeholder="e.g. description" />
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
					    		<label for="field_type">Field Type</label>
					    		<select class="form-control" name="field_type">
					    			<option value="string">String</option>
					    			<option value="text">Text</option>
					    			<option value="integer">Integer</option>
					    			<option value="date">Date</option>
					    			<option value="datetime">Date Time</option>
					    			<option value="boolean">Boolean</option>
					    			<option value="belongsto">BelongsTo</option>
					    			<option value="belongsto">HasOne</option>
					    			<option value="hasmany">HasMany</option>
					    			<option value="belongstomany">BelongsToMany</option>
					    		</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
					    		<label for="field_options">Options</label>
					    		
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
					    		<label for="field_default">Other</label>
					    		
							</div>
						</div>
					</div>
				</div>
				<p>What files do we want to create?</p>
				<div class="well">
					<div class="checkbox">
					    <label>
					     	<input name="controller" type="checkbox"> Controller
					    </label>
					</div>
					<div class="checkbox">
					    <label>
					     	<input name="service" type="checkbox"> Service
					    </label>
					</div>
				</div>
				<button type="submit" class="btn btn-primary pull-right">Okay, let's go!</button>
			</form>
		</div>
	</div>
@endsection