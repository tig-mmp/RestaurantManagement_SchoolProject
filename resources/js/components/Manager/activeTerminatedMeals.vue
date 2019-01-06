<template>
	<div >
		

		<h3>Meals</h3>
        <div class="container-fluid">
        	
        	<div class="row">
					<div class="control">
						<select class="custom-select col-sm" v-model="tableData.length" @change="getMeals()">
			                <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
			            </select>
			        </div>
			        <div class="row">
			        	<div class="col-sm">
			        		<input class="form-control" type="text" v-model="tableData.search" placeholder="Search Per Name" 
							@input="getMeals()">
			        	</div>
			        	
			        	<div class="col-sm">
			        		<datepicker format="YYYY-MM-DD" @dateSelected="onDateSelected"></datepicker>
			        	</div>
			        	<div id='example-3' @change="getMeals()">
							<input type="checkbox" id="chackActive" value="active" v-model="tableData.filterState" >
							<label for="chackActive">Active</label>
							<input type="checkbox" id="checkTerminated" value="terminated" v-model="tableData.filterState" >
							<label for="checkTerminated">Terminated</label>
							<input type="checkbox" id="checkPaid" value="paid" v-model="tableData.filterState">
							<label for="checkPaid">Paid</label>
							<input type="checkbox" id="chekNotPaid" value="not paid" v-model="tableData.filterState">
							<label for="chekNotPaid">Not paid</label>
						</div>

			        </div>
			        
					
			    </div>
					
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
						<td>{{meal.created_at}}</td>
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
	import Datepicker from './../datepicker.vue';
	export default{
		components:{
			datatable: Datatable, 
			pagination: Pagination,
			datepicker: Datepicker,
		},
		created() {
	        this.getMeals();
	    },
		data(){
			let sortOrders = {};
	        let columns = [
	            {width: '15%', label: 'Meal id', name: 'id', order:true},
	            {width: '15%', label: 'Table', name: 'table_number', order:true},
	            {width: '20%', label: 'Responsable waiter', name: 'users.name', order:true },
	            {width: '10%', label: 'State', name: 'state', order:true},
	            {width: '15%', label: 'Total Price', name: 'total_price_preview', order:true},
	            {width: '25%', label: 'Created at', name: 'created_at', order:true},
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
	            	date:'',
	            	filterState: ["active","terminated"],
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

	        onDateSelected: function(date){
	        	this.tableData.date = date;
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
			managerMealRemoved(meal_id){
                this.getMeals();
            },
        },
	};
</script>