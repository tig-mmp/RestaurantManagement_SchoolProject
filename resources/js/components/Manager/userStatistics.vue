<template>
	<div class="container-fluid">
		<div>
			<h1>User Statistics</h1>


	        <div class="row" v-if="chartData.labels && selectedUser.type=='cook'">
	        	<div class="col-sm">
	        		<h3>Orders per day</h3>
	        		<testChart :chartData="chartData" ></testChart>
	        	</div>
	        </div>


			<div class="row">
				<div class="col-sm-8">
					<div class="form-group">
			            <label for="inputID">ID</label>
			            <input
			                    type="text" class="form-control" disabled v-model="selectedUser.id"
			                    name="id" id="inputId"
			                    placeholder="id"/>
			        </div>
			        <div class="form-group">
			            <label for="inputName">Name</label>
			            <input
			                    type="text" class="form-control" disabled v-model="selectedUser.name"
			                    name="name" id="inputName"
			                    placeholder="name"/>
			        </div>
			        <div class="form-group">
			            <label for="inputUsername">Username</label>
			            <input
			                    type="text" class="form-control" disabled v-model="selectedUser.username"
			                    name="name" id="inputUsername"
			                    placeholder="username"/>
			        </div>
			        <div class="form-group">
			            <label for="inputEmail">Email</label>
			            <input
			                    type="text" class="form-control" disabled v-model="selectedUser.email"
			                    name="name" id="inputEmail"
			                    placeholder="email"/>
			        </div>
			        <div class="form-group">
			            <label for="inputType">Type</label>
			            <select
			                    type="text" class="form-control" disabled v-model="selectedUser.type"
			                    name="name" id="inputType"
			                    placeholder="type">
			                    <option>manager</option>
			                    <option>waiter</option>
			                    <option>cook</option>
			                    <option>cashier</option>
			            </select>
			        </div>
		        </div>
		        <div class="col-sm">
			        <div class="form-group">
			        	<img class="rounded" :src="'/imgProfiles/' + selectedUser.photo_url" width="300" height="300" >
			        </div>
		        </div>
	        </div>
	    </div>
	</div>
</template>
<script>
	import TestChart from './testChart.vue';
    export default {
    	components:{
			testChart: TestChart,
		},
        data: function(){
            return { 
                selectedUser: {},
                statistics: [],
                chartData:{
					labels: null,
					datasets: [
						{
							label: 'Orders',
							backgroundColor: '#f87979',
							data: [],
						}
					]
				}
            }
        },
        mounted(){
        	this.selectedUser = this.$parent.selectedUser;
        	this.getCookStatistics();
        },
        methods:{
        	getCookStatistics(){
        		axios.get('api/cook/' + this.selectedUser.id +'/orders/statisticAvgOrdersByDayCook')
                .then(response=>{
                	this.statistics = response.data.data;
                	var arrayL = [];
                	var arrayD = [];
                	this.statistics.forEach( function(element, index) {
                		arrayL.push(element.day);
                		arrayD.push(element.orders);
                	});
                	this.chartData.labels = arrayL;
                	this.chartData.datasets[0].data = arrayD;
                })
                .catch(error => {
                    console.log(error);
                });
        	},
        }
    }
</script>