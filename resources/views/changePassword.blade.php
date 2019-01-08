@extends('master')

@section('title', 'Restaurant Management')
@section('menu')
	<navbar></navbar>
@endsection
@section('content')
		<h1>New Password</h1>
		<div class="form-group">
			<label for="password">New password</label>
			<input
					type="password" class="form-control"
					name="password" id="password"
					placeholder="New password"/>
		</div>
		<div class="form-group">
			<label for="passwordConfirm">Password Confirmation</label>
			<input
					type="password" class="form-control"
					name="password_confirmation" id="passwordConfirm"
					placeholder="Password Confirmation"/>
		</div>
		<div class="form-group">
			<a class="btn btn-primary" onclick="savePassword()">Save</a>
		</div>
	</div>

@endsection
@section('pagescript')
	<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
	<script>
		function savePassword() {
			let urlSplited = window.location.href.split('/');
			let id = urlSplited[urlSplited.length - 1];
			axios.put('/api/users/'+ id +'/password/firstTime', {password : document.getElementById('password').value,
				password_confirmation : document.getElementById('passwordConfirm').value})
			.then(response=>{
				this.typeofmsg = "alert-success";
				this.message = "Password changed with success";
				this.showMessage = true;
			})
			.catch(error => {
				this.typeofmsg = "alert-danger";
				this.message = error.response.data.errors.password;
				this.showMessage = true;
			})
		}
	</script>
@stop
