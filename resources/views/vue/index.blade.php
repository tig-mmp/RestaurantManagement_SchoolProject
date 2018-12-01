@extends('master')

@section('title', 'Restaurant Management')
@section('menu')
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<div class="container">
		<a class="navbar-brand" href="#">Restaurant</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
	    	<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse flex-row-reverse" id="navbarNavAltMarkup">
		    <div class="navbar-nav">
			    <router-link to="/menu" class="nav-item nav-link active">
			    	Menu
			    </router-link>
			    <router-link to="/login"  v-show="!this.$store.state.user" class="nav-item nav-link">
			    	<i class="fas fa-sign-in-alt"></i> 
			    	Login
			    </router-link>
			    <router-link to="/logout" v-show="this.$store.state.user" class="nav-item nav-link">
			    	<i class="fas fa-sign-out-alt"></i>
			    	Logout
			    </router-link>
		    </div>
		</div>
	</div>
</nav>
@endsection
@section('content')

	<em>User: @{{this.$store.state.user != null ? this.$store.state.user.name : " - None - " }}</em>

	<router-view></router-view>

@endsection
@section('pagescript')
<script src="js/vue.js"></script>
@stop  
