<template>
	<div class="container-fluid">
		<div v-if="chartData.labels">
			<h5>The total number of orders handled per months</h5>
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
        		axios.get('api/cook/orders/statisticTotalOrdersPerMonth')
                .then(response=>{
                	this.statistics = response.data.data;
                	var arrayL = [];
                	var arrayD = [];
                	this.statistics.forEach( function(element, index) {
                		arrayL.push(element.year + "-" + element.month);
                		arrayD.push(element.data);
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