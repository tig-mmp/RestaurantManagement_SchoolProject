@extends('master')

@section('title', 'Restaurant Management')
@section('menu')
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<div class="container">
		{{ $_SERVER['REQUEST_URI']}}
		<form method="PUT">
			<div class="form-group">
				<label for="password">New password</label>
				<input type="password" class="form-control"name="password" id="password"
						placeholder="New password"/>
			</div>
			<div class="form-group">
				<label for="passwordConfirm">Password Confirmation</label>
				<input type="password" class="form-control" name="password_confirmation" id="passwordConfirm"
						placeholder="Password Confirmation"/>
			</div>
		</form>
	</div>
</nav>
@endsection
@section('content')

@endsection
@section('pagescript')

@stop  
