<div class="col-sm-3 col-md-2 sidebar">
	<ul class="nav nav-sidebar">
		<li class="@if (Request::is('leapfrog')) active @endif">
			<a href="{{ route('leapfrog.dashboard') }}">Dashboard</a>
		</li>
		<li class="@if (Request::is('leapfrog/crud') || Request::is('leapfrog/crud/*')) active @endif">
			<a href="{{ route('leapfrog.crud') }}">CRUD Generator</a>
		</li>
	</ul>
</div>