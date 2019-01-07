<template>
	<div class="container-fluid">
		<h1>Dashboard</h1>
		<div class="alert alert-success" v-if="alertSucces.show">
	    	<button type="button" class="close-btn" v-on:click="alertSucces.show=false">&times;</button>
	    	<strong  alert.message>{{ alertSucces.message }}</strong>
	    </div>

	    <!-- Statistics ---------------------------->
	    <h2 class="mt-5">Statistics</h2>
	    <div class="row">
			<div class="col-sm">
				 <totalOrdersPerMonth></totalOrdersPerMonth>
			</div>
			<div class="col-sm">
				<totalMealsPerMonth></totalMealsPerMonth>
			</div>
		</div>
	    <div class="row">
			<div class="col-sm">
				 <cookStatistics></cookStatistics>
			</div>
			<div class="col-sm">
				 <waiterStatisticsOredrs></waiterStatisticsOredrs>
			</div>
			<div class="col-sm">
				 <waiterStatisticsMeals></waiterStatisticsMeals>
			</div>
		</div>
		<!--------------------------------------------------------->

	    <!-- Invoices + Invoice Items ---------------------------->
	    <div class="row mt-5">
			<div class="col-sm">
				 <pendingInvoices @invoiceItems="fillInvoiceItems" @declaredNotPaid="declaredNotPaid" ></pendingInvoices>
			</div>
			<div class="col-sm-4"  v-if="selectedInvoice != null">
				<items :selectedInvoice="selectedInvoice" @closeItems="closeItems"></items>
			</div>
		</div>
		<!--------------------------------------------------------->

		<!-- Meals + Meals Orders --------------------------------->
		<div class="row mt-5">
			<div class="col-sm">
				<meals @melOrders="fillOrders" @declaredNotPaid="declaredNotPaid"></meals>
			</div>
			<div class="col-sm-4"  v-if="selectedMeal != null">
				<orders :selectedMeal="selectedMeal" @closeOrders="closeOrders"></orders>
			</div>
		</div>
		<!--------------------------------------------------------->
	</div>
</template>

<script>
	import PendingInvoices from './pendingInvoices.vue';
	import Meals from './activeTerminatedMeals.vue';
	import MealOrders from './mealOrders.vue';
	import InvoiceItems from './invoicesItems.vue';
	import AvgOrdersPerDayCook from './avgOrdersPerDayCook.vue';
	import AvgOrdersPerDayWaiter from './avgOrdersPerDayWaiter.vue';
	import AvgMealsPerDayWaiter from './avgMealsPerDayWaiter.vue';
	import TotalOrdersPerMonth from './totalOrdersPerMonth.vue';
	import TotalMealsPerMonth from './totalMealsPerMonth.vue';
	export default{
		components:{
			pendingInvoices: PendingInvoices,
			meals: Meals,
			orders: MealOrders,
			items: InvoiceItems,
			cookStatistics: AvgOrdersPerDayCook,
			waiterStatisticsOredrs: AvgOrdersPerDayWaiter,
			waiterStatisticsMeals: AvgMealsPerDayWaiter,
			totalOrdersPerMonth: TotalOrdersPerMonth,
			totalMealsPerMonth: TotalMealsPerMonth,
		},
		data(){
			return{
				selectedMeal: null,
				selectedInvoice: null,
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
			fillInvoiceItems(invoice){
				this.selectedInvoice = invoice;
			},
			closeOrders(){
				this.selectedMeal = null;
			},
			closeItems(){
				this.selectedInvoice = null;
			},
			declaredNotPaid: function(meal_id){
	        	axios.put('/api/manager/pendingInvoicesAsNotPaid/' + meal_id)
                .then(response=>{
                	this.$socket.emit('setAsNotPaid', response.data.responsible_waiter_id);
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