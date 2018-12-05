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
                <router-link to="/createUser" class="nav-item nav-link">createUser</router-link>
                <router-link to="/manager" v-show="this.$store.state.user && this.$store.state.user.type == 'manager'" class="nav-item nav-link">
			    	Manager
			    </router-link>
			    <router-link to="/login"  v-show="!this.$store.state.user" class="nav-item nav-link">
			    	<i class="fas fa-sign-in-alt"></i> 
			    	Login
			    </router-link>
			    <div v-show="this.$store.state.user" class="dropdown show">
		  			<a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		    			<img src="imgProfiles/7MF36ilEs1VraVma.jpg" width="40" height="40">
		  			</a>
  					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                        <div class="col-lg-13 p-4">
                        	<p class="text-left">
                        		<strong v-if="this.$store.state.user">
                        			@{{this.$store.state.user.name}}
                        		</strong>
                        	</p>
                        	<p class="text-left small" v-if="this.$store.state.user">
                        		@{{this.$store.state.user.email}}
                        	</p>
                        	<p class="text-left" v-show="this.$store.state.user">
                            	<router-link to="/profile" class="btn btn-primary btn-block btn-sm">
			    				<i class="fas fa-cog"></i>
							    	Show Profile
							    </router-link>
								<router-link to="/editProfile" class="btn btn-primary btn-block btn-sm">
									<i class="fas fa-cog"></i>
									Profile Setings
								</router-link>
								<router-link to="/editPassword" class="btn btn-primary btn-block btn-sm">
									<i class="fas fa-cog"></i>
									Edit password
								</router-link>
                            	<router-link to="/logout" class="btn btn-secondary btn-block btn-sm">
			    				<i class="fas fa-sign-out-alt"></i>
							    	Logout
							    </router-link>
                        	</p>
                    	</div>
                    </div>
  				</div>
			</div>		   
		</div>
	</div>
		
</nav>
@endsection
@section('content')

	<!-- <em>User: @{{this.$store.state.user != null ? this.$store.state.user.name : " - None - " }}</em> -->

	<router-view></router-view>

@endsection
@section('pagescript')
<script src="js/vue.js"></script>
@stop  
