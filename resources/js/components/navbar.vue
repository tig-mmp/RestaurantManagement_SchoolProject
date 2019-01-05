<template>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container">
			<a class="navbar-brand" href="#">Restaurant</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse flex-row-reverse" id="navbarNavAltMarkup">
				<div class="navbar-nav">
					<div v-if="this.$store.state.user" class="mr-auto mt-2 mt-lg-0 text-white navbar-collapse">
						<div v-if="this.$store.state.user.shift_active === 0" class="navbar-collapse flex-row-reverse">
							<a class="nav-item nav-link btn btn-sm btn-outline-secondary" v-on:click.prevent="setShift()"><small>Start shift</small></a>
							<small class="nav-item nav-link active">Last shift ended: {{this.$store.state.user.last_shift_start}}</small>
							<small class="nav-item nav-link active">Time past: {{this.differenceEnd}}</small>
						</div>
						<div v-else class="navbar-collapse flex-row-reverse">
							<a class="nav-item nav-link btn btn-sm btn-outline-secondary" v-on:click.prevent="setShift()"><small>End shift</small></a>
							<small class="nav-item nav-link active">Last shift started: {{this.$store.state.user.last_shift_end}}</small>
							<small class="nav-item nav-link active">Time past: {{this.differenceStart}}</small>
						</div>
					</div>

					<div v-if="this.$store.state.user && this.$store.state.user.type === 'manager'"
						 class="dropdown">
						<a class="btn btn-secondary" href="#"  data-toggle="dropdown">
							<i class="fas fa-comments"></i>
						</a>
						<div v-show="this.$store.state.user" class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
							<div class="p-4" style="width: 100vh">
								<managersNotifications></managersNotifications>
							</div>
						</div>
					</div>

					<router-link to="/menu" class="nav-item nav-link active">
						Menu
					</router-link>
					<router-link to="/manager/dashboard" v-show="this.$store.state.user && this.$store.state.user.type === 'manager' && this.$store.state.user.shift_active === 1" class="nav-item nav-link">
						Manager
					</router-link>
					<router-link to="/login" v-show="!this.$store.state.user" class="nav-item nav-link">
						<i class="fas fa-sign-in-alt"></i>
						Login
					</router-link>
					<router-link to="/shift" v-if="this.$store.state.user && this.$store.state.user.shift_active === 1" class="nav-item nav-link">shift</router-link>

					<div v-if="this.$store.state.user && this.$store.state.user.photo_url !== null" class="dropdown show">
						<a class="btn btn-secondary" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<img :src="'/imgProfiles/' + this.$store.state.user.photo_url" width="40" height="40">
						</a>
						<div v-show="this.$store.state.user" class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
							<div class="col-lg-13 p-4">
								<p class="text-left">
									<strong v-if="this.$store.state.user">
										{{this.$store.state.user.name}}
									</strong>
								</p>
								<p class="text-left small" v-if="this.$store.state.user">
									{{this.$store.state.user.email}}
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
</template>


<script type="text/javascript">
	export default {
		data: function(){
			return {
				differenceStart: "",
				differenceEnd: ""
			}
		},
		methods: {
			setShift() {
				if (this.$store.state.user.shift_active === 1){
					this.$store.state.user.shift_active = 0;
					this.$socket.emit('user_exit', this.$store.state.user);
				} else {
					this.$store.state.user.shift_active = 1;
					this.$socket.emit('user_enter', this.$store.state.user);
				}
				axios.put('/api/users/'+this.$store.state.user.id, this.$store.state.user)
						.then(response=>{
							this.$store.commit('setUser',response.data.data);
						});
			},
			differenceDate(date1, date2){
				let timeDiff = Math.abs(date1.getTime() - date2.getTime());
				let date = new Date(timeDiff);
				let d = Math.floor(date/(1000*60*60*24)); //o getDay() devolve um valor errado
				let h = date.getHours() - 1;    //o getMonth() devolve de 0 a 11
				let m = date.getMinutes();
				let s = date.getSeconds();
				return d + " dias e " + h + ":" + m + ":" + s;
			},
			updateDate(){
				let atual = new Date();
				if (this.$store.state.user.shift_active === 0){
					let end = new Date(this.$store.state.user.last_shift_end);
					this.differenceEnd = this.differenceDate(atual, end);
				} else{
					let start = new Date(this.$store.state.user.last_shift_start);
					this.differenceStart = this.differenceDate(atual, start);
				}
			}
		},
		mounted() {
			this.$nextTick(function () {
				window.setInterval(() => {
					if (this.$store.state.user) {
						this.updateDate()
					}
				},1000);
			});
		},
		sockets: {

		}
	}
</script>

