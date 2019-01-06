<template>
	<div>
		<div style="text-align: right;">
			<a class="btn btn-sm btn-alert" title="See items" v-on:click.prevent="$emit('closeItems')"><i class="fas fa-times is-20"></i></a>
		</div>
        
		<h3 @change="getItems">Selected Invoice ID: {{selectedInvoice.id}}</h3>
		<h4>Items associated</h4>
        <div class="container-fluid">
        	<div class="row">
					<div class="control">
						<select class="custom-select col-sm" v-model="tableData.length" @change="getItems()">
			                <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
			            </select>
			        </div>
					<input class="form-control" type="text" v-model="tableData.search" placeholder="Search Table" @input="getItems()">
		    </div>
			<datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" :datatableStyle="'table-dark'"
			@sort="sortBy">
				<tbody>
					<tr v-for="item in items" :key="item.id">
						<td>{{item.type}}</td>
						<td>{{item.name}}</td>
						<td>{{item.quantity}}</td>
						<td>{{item.unit_price}}</td>
						<td>{{item.sub_total_price}}</td>
					</tr>
				</tbody>
			</datatable>
			<pagination :pagination="pagination" @prev="getItems(pagination.prevPageUrl)"
				@next="getItems(pagination.nextPageUrl)">
			</pagination>
		</div>
	</div>
</template>
<script>
	import Datatable from './../datatable.vue';
	import Pagination from './../Pagination.vue';
	export default{
		props: ['selectedInvoice'],
		watch: { 
      		selectedInvoice: function(newVal, oldVal) { // watch it
      			this.tableData.invoice_id = this.selectedInvoice.id;
      			this.getItems();
			}
		},
		components:{
			datatable: Datatable, 
			pagination: Pagination
		},
		mounted() {
			this.tableData.invoice_id = this.selectedInvoice.id;
	        this.getItems();
	    },
		data(){
			let sortOrders = {};
	        let columns = [
	            {width: '20%', label: 'Type', name: 'type', order:true},
	            {width: '20%', label: 'Name', name: 'name', order:true},
	            {width: '20%', label: 'Quantity', name: 'quantity', order:true},
	            {width: '20%', label: 'Unit price', name: 'unit_price', order:true},
	            {width: '20%', label: 'Sub total', name: 'sub_total_price', order:true},
	        ];
	        columns.forEach((column) => {
	           sortOrders[column.name] = -1;
	        });
			return{
				alertSucces:{
					show:false,
					Message:'',
				},
				items: [],
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
	                invoice_id: '',
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
			getItems(url = 'api/manager/invoiceItemsDataTable') {
	            this.tableData.draw++;
	            axios.get(url, {params: this.tableData})
	                .then(response => {
	                    let data = response.data;
	                    if (this.tableData.draw == data.draw) {
	                        this.items = data.data.data;
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
	            this.getItems();
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
            updateManagerInvoiceItems(invoiceId){
            	//só atualiza se estiver a ver as orders desta meal
            	if (invoiceId == this.selectedInvoice.id) {
					let toast = this.$toasted.show("updating items of selected invoice", {
						theme: "outline",
						position: "top-right",
						duration: 2500
					});
                	this.getItems();
            	}
            },
        },
	};
</script>


