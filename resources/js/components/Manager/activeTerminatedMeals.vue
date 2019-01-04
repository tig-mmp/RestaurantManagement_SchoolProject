<template>
	<div >
		<h3>Active or Terminated meals</h3>
        <div class="container-fluid">
        	<div class="row">
					<div class="control">
						<select class="custom-select col-sm" v-model="tableData.length" @change="getMeals()">
			                <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
			            </select>
			        </div>
					<input class="form-control" type="text" v-model="tableData.search" placeholder="Search Table" @input="getMeals()">
		    </div>
			<datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders"
			@sort="sortBy">
				<tbody>
					<tr v-for="meal in meals" :key="meal.id">
						<td>{{meal.id}}</td>
						<td>{{meal.table_number}}</td>
						<td>{{meal.name}}</td>
						<td>{{meal.state}}</td>
						<td>{{meal.total_price_preview}}</td>
						<td>
							<a class="btn btn-sm btn-primary" title="See orders" v-on:click.prevent="$emit('melOrders', meal)"><i class="fas fa-th-list"></i></a>
							<a class="btn btn-danger" title="Declare as not paid" v-if="meal.state=='terminated'"  @click="$emit('declaredNotPaid', meal.id)">
								<i class="fab fa-creative-commons-nc-eu"></i>
							</a>
						</td>
					</tr>
				</tbody>
			</datatable>
			<pagination :pagination="pagination" @prev="getMeals(pagination.prevPageUrl)"
				@next="getMeals(pagination.nextPageUrl)">
			</pagination>
		</div>
	</div>
</template>

<script>
	import Datatable from './../datatable.vue';
	import Pagination from './../Pagination.vue';
	export default{
		components:{
			datatable: Datatable, 
			pagination: Pagination
		},
		created() {
	        this.getMeals();
	    },
		data(){
			let sortOrders = {};
	        let columns = [
	            {width: '15%', label: 'Meal id', name: 'id', order:true},
	            {width: '15%', label: 'Table', name: 'table_number', order:true},
	            {width: '40%', label: 'Responsable waiter', name: 'users.name', order:true },
	            {width: '20%', label: 'State', name: 'state', order:true},
	            {width: '25%', label: 'Total Price', name: 'total_price_preview', order:true},
	            {width: '25%', label: 'Actions', name: 'actions', order:false},
	        ];
	        columns.forEach((column) => {
	           sortOrders[column.name] = -1;
	        });
			return{
				alertSucces:{
					show:false,
					Message:'',
				},
				meals: [],
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
			getMeals(url = 'api/manager/mealDataTable') {
	            this.tableData.draw++;
	            axios.get(url, {params: this.tableData})
	                .then(response => {
	                    let data = response.data;
	                    if (this.tableData.draw == data.draw) {
	                        this.meals = data.data.data;
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
	            this.getMeals();
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
            setAsNotPaid(){
                this.getMeals();
            },
            mealCreated(){
                this.getMeals();
            },
            mealRemoved(meal_id){
                this.getMeals();
                let toast = this.$toasted.show("Meal with id " + meal_id[0] + " is terminated", {
                    theme: "bubble", 
					position: "top-right", 
					duration : 5000
                });
            },
        },
	};
</script>