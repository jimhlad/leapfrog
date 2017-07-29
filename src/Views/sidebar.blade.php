<div class="col-sm-3 col-md-2 sidebar">
	<ul class="nav nav-sidebar">
		<li class="@if (Request::is('leapfrog')) active @endif">
			<a href="{{ route('leapfrog.dashboard') }}">Dashboard</a>
		</li>
		<li class="@if (Request::is('leapfrog/crud') || Request::is('leapfrog/crud/*')) active @endif">
			<a href="{{ route('leapfrog.crud') }}">CRUD Generator</a>
		</li>
	</ul>
	<div class="credits">
		<p>Follow me on Twitter:</p>
		<p><a href="https://www.twitter.com/jimhlad" target="_blank">@jimhlad</a></p>
	</div>
	<div class="credits">
		<p>GitHub page:</p>
		<p><a href="https://www.github.com/" target="_blank">github.com/jimhlad/leapfrog</a></p>
	</div>
	<div class="version">
		<p><em>Version 1.0.0</em></p>
	</div>
</div>