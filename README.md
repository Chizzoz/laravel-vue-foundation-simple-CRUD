# laravel-vue-foundation-simple-CRUD
A Simple Item Create/Read/Update/Delete (CRUD) Application created using Laravel 5.4, Vue.js 2.3.3, Foundation 6.3, Axios and Toastr 2.1 with composer and npm package or dependency management.

So, I came across this tutorial [Laravel 5 and Vue JS CRUD with Pagination example and demo from scratch](http://itsolutionstuff.com/post/laravel-5-and-vue-js-crud-with-pagination-example-and-demo-from-scratchexample.html). After I got the Item CRUD app working, I thought of redoing it with some packages managed by composer, which Laravel mostly utilises, and use Zurb Foundation, instead of Bootstrap, which Laravel utilises straight out of the box. Also, in the link I shared, the author makes use of Vue.js version 1 and Vue-resource for making web requests and handle responses. In this approach, I use Vue.js version 2 and Axios for making web requests and handling responses. Vue and Axios are managed by npm, while the rest of the dependencies are managed by composer.

This is the journey to make the above happen.

I developed this using Uniserver Zero XIII 13.3.2; Apache Server (PHP 7.1.1), MySQL and the Server Console. Firstly, you install composer and then get your Apache and MySQL running, then launch the server console to install Laravel and the other dependencies. Below are the steps taken:

1. Install Laravel in folder named 'laravel-vue-foundation-simple-CRUD'
```cmd
composer create-project --prefer-dist laravel/laravel laravel-vue-foundation-simple-CRUD
```
2. Create authenication scaffolding for the app
```cmd
php artisan make:auth
```
3. Create database migration for the items table
```cmd
php artisan make:migration create_items_table --create=items
```
4. EDIT: database\migrations\*_create_users_table.php
   Change name to username
```php
...
            $table->string('username');
...
```
5. EDIT: config\app.php
   Give your app a name and make any other necessary configs
```php
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'name' => env('APP_NAME', 'Simple CRUD'),
```
database.php
```php
'strict' => false,
'engine' => 'InnoDB',
```
6. EDIT: laravel-vue-foundation\.env
   Modify with details relevant to your database
```php
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel-vue-foundtn
DB_USERNAME=****
DB_PASSWORD=********
```
7. At this point you can run your migration to create database table
```cmd
php artisan migrate
```
   But you will get the following error:
```cmd
  [Illuminate\Database\QueryException]
  SQLSTATE[42000]: Syntax error or access violation: 1071 Specified key was too long; max key length is 767 bytes (SQL: alter table `users` add unique `users_email_unique`(`email`))
```
8. So, to solve the above error. EDIT: app\Providers\AppServiceProvider.php
Add:
```php
use Illuminate\Support\Facades\Schema;
```
Then modify boot as follows:
```php
public function boot()
    {
        Schema::defaultStringLength(191);
    }
```
9. If the migration failed, some tables were created in your database, so first go ahead and delete all tables, then run migration again
```cmd
php artisan migrate
```
10. To avoid compiling errors later on due to version mismatch, we'll uninstall and reinstall some packages that might be outdated using npm.
```cmd
npm uninstall axios --save-dev
npm uninstall bootstrap-sass --save-dev
npm uninstall cross-env --save-dev
npm uninstall jquery --save-dev
npm uninstall vue --save-dev

npm install axios --save-dev
npm install cross-env --save-dev
npm install vue --save-dev
npm update
```
Also, we'll install some packages using composer.
```cmd
composer require components/jquery
composer require grimmlink/toastr
composer require zurb/foundation
composer update
```
11. Create the Item model using the following command:
```cmd
php artisan make:model Item
```
12. EDIT: app\Item.php model as follows:
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public $fillable = ['title', 'description'];
}
```
13. Create an item resource controller with the following command:
```cmd
php artisan make:controller VueItemController --resource
```
14. EDIT: app\Http\Controllers\VueItemController.php
```php
<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;

class VueItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Manage vue items.
     *
     * @return \Illuminate\Http\Response
     */
    public function manageVue()
    {
        return view('manage-vue');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = Item::latest()->paginate(5);
		
		$response = [
			'pagination' => [
				'total' => $items->total(),
				'per_page' => $items->perPage(),
				'current_page' => $items->currentPage(),
				'last_page' => $items->lastPage(),
				'from' => $items->firstItem(),
				'to' => $items->lastItem(),
			],
			'data' => $items
		];
		
		return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
			'title' => 'required',
			'description' => 'required',
		]);
		
		$create = Item::create($request->all());
		
		return response()->json($create);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $item)
    {
        $this->validate($request, [
			'title' => 'required',
			'description' => 'required',
		]);
		
		$edit = Item::find($item)->update($request->all());
		
		return response()->json($edit);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy($item)
    {
        Item::find($item)->delete();
		return response()->json(['done']);
    }
}
```
15. EDIT: app\Http\Controllers\Auth\LoginController.php
```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/manage-vue';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
```
RegisterController.php
```php
<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/manage-vue';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
```
16. EDIT: routes\web.php
```php
<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('manage-vue', array('as' => 'manage-vue', 'uses' => 'VueItemController@manageVue'));
Route::resource('vueitems', 'VueItemController');
```
17. DELETE: resources\assets\sass\_variables.scss and then EDIT: resources\assets\sass\app.scss as follows:
```php
// Fonts
@import url(http://fonts.googleapis.com/css?family=Open+Sans);

// Settings
@import "vendor/zurb/foundation/scss/settings/settings";

// Foundation
@import "vendor/zurb/foundation/scss/foundation";

// Everything
@include foundation-global-styles;
@include foundation-grid;
@include foundation-typography;
@include foundation-forms;
@include foundation-button;
@include foundation-accordion;
@include foundation-accordion-menu;
@include foundation-badge;
@include foundation-breadcrumbs;
@include foundation-button-group;
@include foundation-callout;
@include foundation-card;
@include foundation-close-button;
@include foundation-menu;
@include foundation-menu-icon;
@include foundation-drilldown-menu;
@include foundation-dropdown;
@include foundation-dropdown-menu;
@include foundation-responsive-embed;
@include foundation-label;
@include foundation-media-object;
@include foundation-off-canvas;
@include foundation-orbit;
@include foundation-pagination;
@include foundation-progress-bar;
@include foundation-slider;
@include foundation-sticky;
@include foundation-reveal;
@include foundation-switch;
@include foundation-table;
@include foundation-tabs;
@include foundation-thumbnail;
@include foundation-title-bar;
@include foundation-tooltip;
@include foundation-top-bar;
@include foundation-visibility-classes;
@include foundation-float-classes;
```
18. EDIT: vendor\zurb\foundation\scss\settings\_settings.scss
Line 44
```php
@import '../util/util';
```
19. EDIT: laravel-vue-foundation\resources\assets\js\app.js
```php
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Crud.vue'));

const app = new Vue({
    el: '#manage-vue'
});
```
bootstrap.js
```php
window._ = require('lodash');

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

window.Vue = require('vue');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
```
20. CREATE: resources\assets\js\components\Crud.vue
```php
<template>
<div class="large-12 medium-12 small-12 column">
	<div class="secondary callout"><h3>Laravel Foundation Vue.js Axios and Toastr</h3></div>
	<div class="secondary callout"><a data-open="create-item" class="success button">Create Item</a></div>
	<table class="hover">
		<thead>
			<tr>
				<th>Title</th>
				<th>Description</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="item in items">
				<td>{{ item.title }}</td>
				<td>{{ item.description }}</td>
				<td><a data-open="edit-item" class="button" @click.prevent="editItem(item)">Edit</a><div role="button" class="alert button" v-on:click.prevent="deleteItem(item)">Delete</div></td>
			</tr>
		</tbody>
	</table>
	<!-- Pagination -->
	<ul class="pagination text-center" role="navigation" aria-label="Pagination">
		<li><a href="#" @click.prevent="changePage(pagination.current_page - 1)" v-bind:class="[pagination.current_page > 1 ? '': 'disabled']">Previous</a></li>
		<li v-for="page in pagesNumber" v-bind:class="[page == isActived ? 'current' : '']">
			<a href="#" v-on:click.prevent="changePage(page)">{{ page }}</a>
		</li>
		<li><a href="#" aria-label="Next page" @click.prevent="changePage(pagination.current_page + 1)" v-bind:class="[pagination.current_page < pagination.last_page ? '': 'disabled']">Next</a></li>
	</ul>
	<!-- Create Item Modal -->
	<div class="reveal" id="create-item" data-reveal>
		<form method="POST" enctype="multipart/form-data" @submit.prevent="createItem">
			<!-- Item -->
			<div class="large-12 medium-12 small-12 columns">
				<div class="row collapse prefix-radius">
					<div class="medium-3 columns">
						<span class="prefix"><strong>Title*</strong></span>
					</div>
					<div class="medium-9 column">
						<input type="text" name="title" placeholder="Title" v-model="newItem.title" />
					</div>
				</div>
				<span v-for="error in formErrors['title']" v-if="formErrors['title']" class="error">{{ error }}</span>
			</div>
			<!-- Description -->
			<div class="large-12 medium-12 small-12 columns">
				<div class="row collapse prefix-radius">
					<div class="medium-3 columns">
						<span class="prefix"><strong>Description*</strong></span>
					</div>
					<div class="medium-9 column">
						<input type="text" name="description" placeholder="Description" v-model="newItem.description" />
					</div>
				</div>
				<span v-for="error in formErrors['description']" v-if="formErrors['description']" class="error">{{ error }}</span>
			</div>
			<input type="submit" value="Create" class="expanded button" />
		</form>
		<button class="close-button" data-close aria-label="Close modal" type="button">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	<!-- Edit Item Modal -->
	<div class="reveal" id="edit-item" data-reveal>
		<form method="POST" enctype="multipart/form-data" @submit.prevent="updateItem(fillItem.id)">
			<input name="_method" type="hidden" value="PUT">
			<!-- Item -->
			<div class="large-12 medium-12 small-12 columns">
				<div class="row collapse prefix-radius">
					<div class="medium-3 columns">
						<span class="prefix"><strong>Title*</strong></span>
					</div>
					<div class="medium-9 column">
						<input type="text" name="title" v-model="fillItem.title" />
					</div>
				</div>
				<span v-for="error in formErrorsUpdate['title']" v-if="formErrorsUpdate['title']" class="error">{{ error }}</span>
			</div>
			<!-- Description -->
			<div class="large-12 medium-12 small-12 columns">
				<div class="row collapse prefix-radius">
					<div class="medium-3 columns">
						<span class="prefix"><strong>Description*</strong></span>
					</div>
					<div class="medium-9 column">
						<input type="text" name="description" placeholder="Description" v-model="fillItem.description" />
					</div>
				</div>
				<span v-for="error in formErrorsUpdate['description']" v-if="formErrorsUpdate['description']" class="error">{{ error }}</span>
			</div>
			<input type="submit" value="Update" class="expanded button" />
		</form>
		<button class="close-button" data-close aria-label="Close modal" type="button">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
</div>
</template>

<script>
	export default {
		data: () => ({
			items: [],
			pagination: {
				total: 0, 
				per_page: 2,
				from: 1, 
				to: 0,
				current_page: 1
			},
			offset: 4,
			formErrors:{},
			formErrorsUpdate:{},
			newItem : {'title':'','description':''},
			fillItem : {'title':'','description':'','id':''}
		}),

		computed: {
			isActived: function () {
				return this.pagination.current_page;
			},
			pagesNumber: function () {
				if (!this.pagination.to) {
					return [];
				}
				var from = this.pagination.current_page - this.offset;
				if (from < 1) {
					from = 1;
				}
				var to = from + (this.offset * 2);
				if (to >= this.pagination.last_page) {
					to = this.pagination.last_page;
				}
				var pagesArray = [];
				while (from <= to) {
					pagesArray.push(from);
					from++;
				}
				return pagesArray;
			}
		},

		mounted : function(){
			this.getVueItems(this.pagination.current_page);
		},

		methods : {

			getVueItems: function(page){
				axios.get('/laravel-vue-foundation-simple-CRUD/public/vueitems?page='+page).then(response => {
					this.items = response.data.data.data,
					this.pagination = response.data.pagination
				});
			},

			createItem: function(){
				var input = this.newItem;
				axios.post('/laravel-vue-foundation-simple-CRUD/public/vueitems',input).then((response) => {
					this.changePage(this.pagination.current_page);
					this.newItem = {'title':'','description':''};
					$('#create-item').foundation('close');
					toastr.success('Item Created Successfully.', 'Success Alert', {timeOut: 5000});
				}).catch((error) => {
					this.formErrors = error.response.data;
				});
			},

			deleteItem: function(item){
				axios.delete('/laravel-vue-foundation-simple-CRUD/public/vueitems/'+item.id).then((response) => {
					this.changePage(this.pagination.current_page);
					toastr.error('Item Deleted Successfully.', 'Success Alert', {timeOut: 5000});
				});
			},

			editItem: function(item){
				this.fillItem.title = item.title;
				this.fillItem.id = item.id;
				this.fillItem.description = item.description;
				$('#edit-item').foundation('open');
			},

			updateItem: function(id){
				var input = this.fillItem;
				axios.put('/laravel-vue-foundation-simple-CRUD/public/vueitems/'+id,input).then((response) => {
					this.changePage(this.pagination.current_page);
					this.fillItem = {'title':'','description':'','id':''};
					$("#edit-item").foundation('close');
					toastr.info('Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
				}, (error) => {
					this.formErrorsUpdate = error.response.data;
				});
			},

			changePage: function (page) {
				this.pagination.current_page = page;
				this.getVueItems(page);
			}
		}
	}
</script>
```
21. EDIT: webpack.mix.js
```php
const { mix } = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.sass('resources/assets/sass/app.scss', 'public/css')
   .js('resources/assets/js/app.js', 'public/js');
mix.copy('vendor/components/jquery/jquery.min.js', 'public/js');
mix.copy('vendor/zurb/foundation/dist/js/foundation.min.js', 'public/js');
mix.copy('vendor/grimmlink/toastr/build/toastr.min.css', 'public/css');
mix.copy('vendor/grimmlink/toastr/build/toastr.min.js', 'public/js');
```
22. Then run <code>npm run dev</code> to compile once OR <code>npm run watch</code> if you'll be making frequent changes to javascript code, this way, changes will be monitored and system will automatically recompile your components each time they are modified.

23. CREATE: resources\views\manage-vue.blade.php
```php
@extends('layouts.app')

@section('content')
	<div class="large-12 medium-12 small-12 column">
		<div id="manage-vue" class="large-12 medium-12 small-12 column primary callout">
			<example></example>
		</div>
		<script src="{{ asset('js/app.js') }}"></script>
	</div>
@endsection

```
24. EDIT: resources\views\layouts\app.blade.php
```php
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
```
25. EDIT: resources\views\auth\login.blade.php
```php
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
```
auth\register.blade.php
```php
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
```
= THE END =
