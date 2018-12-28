<template>
	<div class="container-fluid">
		<h1>Dashboard</h1>
		<pendingInvoices></pendingInvoices>
		<div class="row mt-5">
			<div class="col-sm">
				<meals @melOrders="fillOrders"></meals>
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
			}
		},
		methods:{
			fillOrders(meal){
				this.selectedMeal = meal;
			},
			closeOrders(){
				this.selectedMeal = null;
			}			
		}
	};
</script>