<template>
	<div>
		<div style="text-align: right;">
			<a class="btn btn-sm btn-alert" title="See orders" v-on:click.prevent="$emit('closeOrders')"><i class="fas fa-times is-20"></i></a>
		</div>
        
		<h3 @change="getOrders">Selected Meal ID: {{selectedMeal.id}}</h3>
		<h4>Orders associated</h4>
        <div class="container-fluid">
        	<div class="row">
					<div class="control">
						<select class="custom-select col-sm" v-model="tableData.length" @change="getOrders()">
			                <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
			            </select>
			        </div>
					<input class="form-control" type="text" v-model="tableData.search" placeholder="Search Table" @input="getOrders()">
		    </div>
			<datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" :datatableStyle="'table-dark'"
			@sort="sortBy">
				<tbody>
					<tr v-for="order in orders" :key="order.id">
						<td>{{order.state}}</td>
						<td>{{order.name}}</td>
						<td>{{order.type}}</td>
						<td>{{order.price}}</td>
					</tr>
				</tbody>
			</datatable>
			<pagination :pagination="pagination" @prev="getOrders(pagination.prevPageUrl)"
				@next="getOrders(pagination.nextPageUrl)">
			</pagination>
		</div>
	</div>
</template>
<script>
	import Datatable from './../datatable.vue';
	import Pagination from './../Pagination.vue';
	export default{
		props: ['selectedMeal'],
		watch: { 
      		selectedMeal: function(newVal, oldVal) { // watch it
      			this.tableData.meal_id = this.selectedMeal.id;
      			this.getOrders();
			}
		},
		components:{
			datatable: Datatable, 
			pagination: Pagination
		},
		mounted() {
			this.tableData.meal_id = this.selectedMeal.id;
	        this.getOrders();
	    },
		data(){
			let sortOrders = {};
	        let columns = [
	            {width: '25%', label: 'State', name: 'state', order:false},
	            {width: '25%', label: 'Name', name: 'name', order:true },
	            {width: '25%', label: 'Type', name: 'type', order:true },
	            {width: '25%', label: 'Price', name: 'price', order:true },
	        ];
	        columns.forEach((column) => {
	           sortOrders[column.name] = -1;
	        });
			return{
				alertSucces:{
					show:false,
					Message:'',
				},
				orders: [],
	            columns: columns,
	            sortKey: 'name',
	            sortOrders: sortOrders,
	            perPage: ['5','10', '20', '30'],
	            tableData: {
	                draw: 0,
	                length: 5,
	                search: '',
	                column: 0,
	                dir: 'desc',
	                meal_id: '',
	            },

	            pagination: {
	                lastPage: '',
	                currentPage: '',
	                total: '',
	                lastPageUrl: '',
	                nextPageUrl: '',
	                prevPageUrl: '',
	                from: '',
	                to: ''
	            },
			}
		},

		methods:{
			getOrders(url = 'api/manager/orderDataTable') {
	            this.tableData.draw++;
	            axios.get(url, {params: this.tableData})
	                .then(response => {
	                    let data = response.data;
	                    if (this.tableData.draw == data.draw) {
	                        this.orders = data.data.data;
	                        this.configPagination(data.data);
	                    }
	                })
	                .catch(errors => {
	                    console.log(errors);
	                });
	        },

	        configPagination(data) {
	            this.pagination.lastPage = data.last_page;
	            this.pagination.currentPage = data.current_page;
	            this.pagination.total = data.total;
	            this.pagination.lastPageUrl = data.last_page_url;
	            this.pagination.nextPageUrl = data.next_page_url;
	            this.pagination.prevPageUrl = data.prev_page_url;
	            this.pagination.from = data.from;
	            this.pagination.to = data.to;
	        },

	        sortBy(key) {
	            this.sortKey = key;
	            this.sortOrders[key] = this.sortOrders[key] * -1;
	            this.tableData.column = this.getIndex(this.columns, 'name', key);
	            this.tableData.dir = this.sortOrders[key] === 1 ? 'asc' : 'desc';
	            this.getOrders();
	        },

	        getIndex(array, key, value) {
	            return array.findIndex(i => i[key] == value)
	        },
	        buildSuccessMessage(message){
        		this.alertSucces.show = true;
				this.alertSucces.message = message;
				setTimeout(() => {
                        this.alertSucces.show = false;
                }, 2000);
        	},
		},
		sockets: {
            updateManagerOrders(mealId){
            	//só atualiza se estiver a ver as orders desta meal
            	if (mealId == this.selectedMeal.id) {
                	this.getOrders();
            	}
                let toast = this.$toasted.show(dataFromServer[0], {
                    theme: "outline",
                    position: "top-center",
                    duration : null
                });
            },
        },
	};
</script>


