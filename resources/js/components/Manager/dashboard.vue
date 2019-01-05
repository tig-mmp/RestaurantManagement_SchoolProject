<template>
	<div class="container-fluid">
		<h1>Dashboard</h1>
		<div class="alert alert-success" v-if="alertSucces.show">
	    	<button type="button" class="close-btn" v-on:click="alertSucces.show=false">&times;</button>
	    	<strong  alert.message>{{ alertSucces.message }}</strong>
	    </div>

	    <pendingInvoices @declaredNotPaid="declaredNotPaid" :updateNotPaid="updateNotPaid" ></pendingInvoices>

		<div class="row mt-5">
			<div class="col-sm">
				<meals @melOrders="fillOrders" @declaredNotPaid="declaredNotPaid" :updateNotPaid="updateNotPaid"></meals>
			</div>
			<div class="col-sm-4"  v-if="selectedMeal != null">
				<orders :selectedMeal="selectedMeal" @closeOrders="closeOrders"></orders>
			</div>
		</div>
	</div>
</template>

<script>
	import PendingInvoices from './pendingInvoices.vue';
	import Meals from './activeTerminatedMeals.vue';
	import MealOrders from './mealOrders.vue';
	export default{
		components:{
			pendingInvoices: PendingInvoices,
			meals: Meals,
			orders: MealOrders,
		},
		data(){
			return{
				selectedMeal: null,
				updateNotPaid: '',
				alertSucces:{
					show:false,
					Message:'',
				},
			}
		},
		methods:{
			fillOrders(meal){
				this.selectedMeal = meal;
			},
			closeOrders(){
				this.selectedMeal = null;
			},
			declaredNotPaid: function(meal_id){
	        	axios.put('/api/manager/pendingInvoicesAsNotPaid/' + meal_id)
                .then(response=>{
                	this.$socket.emit('setAsNotPaid');
                	this.buildSuccessMessage('Invoices and Meals updated successfully');
                });
	        },
	        buildSuccessMessage(message){
        		this.alertSucces.show = true;
				this.alertSucces.message = message;
				setTimeout(() => {
                        this.alertSucces.show = false;
                }, 2000);
        	},

		},
	};
</script>