<div class="col-sm-3 col-md-2 sidebar">
	<ul class="nav nav-sidebar">
		<li class="@if (Request::is('leapfrog')) active @endif">
			<a href="{{ route('leapfrog.dashboard') }}">Dashboard</a>
		</li>
		<li class="@if (Request::is('leapfrog/crud') || Request::is('leapfrog/crud/*')) active @endif">
			<a href="{{ route('leapfrog.crud') }}">CRUD Generator</a>
		</li>
	</ul>
	<p>If you have any feedback/questions, please feel free to reach out to me on twitter:</p>
	<p><a href="https://www.twitter.com/jimhlad" target="_blank">@jimhlad</a></p>
</div>