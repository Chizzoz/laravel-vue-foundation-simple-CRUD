@extends('layouts.app')

@section('content')
<div class="large-12 medium-12 small-12 columns">
	<div class="primary callout">
			<h3>Register</h3>
			<div class="panel-body">
				<form class="form-horizontal" role="form" method="POST" action="{{ route('register') }}">
					{{ csrf_field() }}
	
					<!-- Username -->
					<div>
						<div class="row collapse prefix-radius">
							<div class="medium-3 columns">
								<span class="prefix"><strong>Username</strong></span>
							</div>
							<div class="medium-9 column">
								<input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required autofocus />
							</div>
						</div>
						@if (count($errors) > 0)
							@foreach ($errors->get('username') as $error)
								<span class="error">{{ $error }}</span>
							@endforeach
						@endif
					</div>
	
					<!-- eMail Address -->
					<div>
						<div class="row collapse prefix-radius">
							<div class="medium-3 columns">
								<span class="prefix"><strong>e-Mail Address</strong></span>
							</div>
							<div class="medium-9 column">
								<input type="email" name="email" placeholder="e-Mail Address" value="{{ old('email') }}" required autofocus />
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

					<!-- Password Confirm -->
					<div>
						<div class="row collapse prefix-radius">
							<div class="medium-3 columns">
								<span class="prefix"><strong>Password Confirm</strong></span>
							</div>
							<div class="medium-9 column">
								<input type="password" name="password_confirmation" placeholder="Password" required autofocus />
							</div>
						</div>
						@if (count($errors) > 0)
							@foreach ($errors->get('password-confirm') as $error)
								<span class="error">{{ $error }}</span>
							@endforeach
						@endif
					</div>

					<div>
						<button type="submit" class="large expanded button">Register</button>
					</div>
				</form>
			</div>
	</div>
</div>
@endsection
