<template>
	<div class="container-fluid">
		<div v-if="chartData.labels">
			<h5>The average number of orders handled by day for each waiter</h5>
    		<testChart :chartData="chartData" ></testChart>
	    </div>
        <div v-else>
            <img src="loader.gif">
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
        	this.getCookStatistics();
        },
        methods:{
        	getCookStatistics(){
                console.log('WAITER PROCESSING...');
        		axios.get('api/cook/orders/statisticAvgOrdersByDayWaiter')
                .then(response=>{
                	this.statistics = response.data.data;
                	var arrayL = [];
                	var arrayD = [];
                	this.statistics.forEach( function(element, index) {
                		arrayL.push(element.name);
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