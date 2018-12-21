<template>
	<div>
		<h1>Tables</h1>
        <div class="container-fluid">
        	<div class="row">
				<div class="col-sm-9">
					<input class="form-control col-sm-2" type="text" v-model="tableData.search" placeholder="Search Table" @input="getTables()">

					<div class="control">
						<select class="custom-select col-sm-1" v-model="tableData.length" @change="getTables()">
			                <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
			            </select>
			        </div>
			    </div>
			    <div class="col-sm-3" style="text-align: right;">
			    	<div class="container-fluid">
			    		<div class="row">
			    			<input class="form-control col-sm" type="text" v-model="newTable.table_number" placeholder="New Table id" >
			    			<a class="btn btn-sm btn-primary font-weight-bold" title="Edit" v-on:click.prevent="createTable()"><i class="fas fa-plus is-20"></i> Create Table</a>
			    		</div>
			    	</div>
			    	
			    </div>
		    </div>
			<datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" 
			@sort="sortBy">
				<tbody>
					<tr v-for="table in tables" :key="table.id">
						<td>{{table.table_number}}</td>
						<td>
							<a class="btn btn-sm btn-danger" title="Remove" v-on:click.prevent="deleteTable(table)"><i class="fas fa-trash-alt is-20"></i></a>
						</td>
					</tr>
				</tbody>
			</datatable>
			<pagination :pagination="pagination" @prev="getTables(pagination.prevPageUrl)"
				@next="getTables(pagination.nextPageUrl)">
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
	        this.getTables();
	    },
		data(){
			let sortOrders = {};
	        let columns = [
	            {width: '20%', label: 'Table Number', name: 'table_id', order:true },
	            {width: '20%', label: 'Actions', name: 'actions', order:false}
	        ];
	        columns.forEach((column) => {
	           sortOrders[column.name] = 1;
	        });
			return{
				newTable: {
					table_number:null
				},
				tables: [],
	            columns: columns,
	            sortKey: 'name',
	            sortOrders: sortOrders,
	            perPage: ['5','10', '20', '30'],
	            tableData: {
	                draw: 0,
	                length: 5,
	                search: '',
	                column: 0,
	                dir: 'asc',
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
			getTables(url = 'api/manager/tablesDataTable') {
	            this.tableData.draw++;
	            axios.get(url, {params: this.tableData})
	                .then(response => {
	                    let data = response.data;
	                    if (this.tableData.draw == data.draw) {
	                        this.tables = data.data.data;
	                        this.configPagination(data.data);
	                    }
	                })
	                .catch(errors => {
	                    console.log(errors);
	                });
	        },

	        deleteTable(table){
	        	axios.delete('api/manager/managerTableList/' + table.table_number)
                    .then(response => {
                        this.getTables();
                    });
	        },

	        createTable(){
	        	axios.post('/api/manager/createTable', this.newTable)
        		.then(response=>{
        			this.newTable.table_number = null;
        			this.getTables();
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
	            this.getTables();
	        },

	        getIndex(array, key, value) {
	            return array.findIndex(i => i[key] == value)
	        },
		}
	};
</script>