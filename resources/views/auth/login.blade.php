@extends('layouts.app')

@section('content')
<div class="large-12 medium-12 small-12 columns">
<div class="primary callout">
	<h3>Login</h3>
	<form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
		{{ csrf_field() }}
	
		<!-- Username -->
		<div>
			<div class="row collapse prefix-radius">
				<div class="medium-3 columns">
					<span class="prefix"><strong>Username</strong></span>
				</div>
				<div class="medium-9 column">
					<input type="email" name="email" placeholder="Username, Mobile or e-Mail" value="{{ old('email') }}" required autofocus />
				</div>
			</div>
			@if (count($errors) > 0)
				@foreach ($errors->get('email') as $error)
					<span class="error">{{ $error }}</span>
				@endforeach
			@endif
		</div>

		<!-- Password -->
		<div>
			<div class="row collapse prefix-radius">
				<div class="medium-3 columns">
					<span class="prefix"><strong>Password</strong></span>
				</div>
				<div class="medium-9 column">
					<input type="password" name="password" placeholder="Password" required autofocus />
				</div>
			</div>
			@if (count($errors) > 0)
				@foreach ($errors->get('password') as $error)
					<span class="error">{{ $error }}</span>
				@endforeach
			@endif
		</div>

		<div>
			<input id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}><label for="remember">Remember Me</label>
		</div>
		
		<div>
			<button type="submit" class="large expanded button">Login</button>
			<a class="small button" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
		</div>
	</form>
</div>
</div>
@endsection
