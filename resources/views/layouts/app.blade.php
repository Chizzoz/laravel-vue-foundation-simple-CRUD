<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- CSRF Token -->
		<script>window.Laravel = { csrfToken: '{{ csrf_token() }}' }</script>
		<meta name="_token" content="{{ csrf_token() }}"/>

		<title>{{ config('app.name', 'Vue') }}</title>

		<!-- Styles -->
		<link href="{{ asset('css/app.css') }}" rel="stylesheet">
		<link href="{{ asset('css/custom.css') }}" rel="stylesheet">
		<link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
		<link href="{{ asset('css/f5-forms.css') }}" rel="stylesheet">

		<!-- Scripts -->
	</head>
	<body>
		<div class="top-bar">
			<div class="row">
				<div class="top-bar-title">
					<span data-responsive-toggle="responsive-menu" data-hide-for="small">
						<span class="menu-icon dark" data-toggle></span>
					</span>
					<!-- Branding Image -->
					<a href="{{ url('/') }}">{{ config('app.name') }}</a>
				</div>
				<div>
					<div class="top-bar-left">
						<ul class="menu">
							<li><a href="{{ route('manage-vue') }}" title="Manage Vue">Manage Vue</a></li>
						</ul>
					</div>
					<div class="top-bar-right">
						<!-- Right Side Of Navbar -->
						<ul class="dropdown menu" data-dropdown-menu>
							<!-- Authentication Links -->
							@if (Auth::guest())
								<li class="divider"></li>
								<li><a href="{{ route('login') }}">Login</a></li>
								<li class="divider"></li>
								<li><a href="{{ route('register') }}">Register</a></li>
							@else
								<li>
									<a href="home">{{ Auth::user()->username }}</a>
									<ul class="vertical menu">
										<li>
											<a href="{{ route('logout') }}"
												onclick="event.preventDefault();
												document.getElementById('logout-form').submit();">
												Logout
											</a>

											<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
												{{ csrf_field() }}
											</form>
										</li>
									</ul>
								</li>
							@endif
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			@yield('content')
		</div>

		<div class="footer">
			<div class="row">
				<div class="large-12 medium-12 small-12 columns">
					<ul class="menu">
						<li><a href="">Say Chizz Productions &copy; Copyright 2017</a></li>
					</ul>
				</div>
			</div>
		</div>
		<!-- Scripts -->
		<script src="{{ asset('js/jquery.min.js') }}"></script>
		<script src="{{ asset('js/foundation.min.js') }}"></script>
		<script>$(document).foundation();</script>
		<script src="{{ asset('js/toastr.min.js') }}"></script>
		<script>toastr.options.progressBar = true;</script>
	</body>
</html>
