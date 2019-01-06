<template>
	<div >
		<invoiceToPdf :invoiceID="selectedInvoice"></invoiceToPdf>
		<h3>Pending Invoices</h3>
        <div class="container-fluid">
        	<div class="row">
					<div class="control">
						<select class="custom-select col-sm" v-model="tableData.length" @change="getInvoices()">
			                <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
			            </select>
			        </div>
			        <div class="row">
			        	<div class="col-sm">
			        		<input class="form-control" type="text" v-model="tableData.search" placeholder="Search Per Name" 
							@input="getInvoices()">
			        	</div>
			        	
			        	<div class="col-sm">
			        		<datepicker format="YYYY-MM-DD" @dateSelected="onDateSelected"></datepicker>
			        	</div>
			        	<div id='example-3' @change="getInvoices()">
							<input type="checkbox" id="checkPending" value="pending" v-model="tableData.filterState" >
							<label for="checkPending">Pending</label>
							<input type="checkbox" id="checkPaid" value="paid" v-model="tableData.filterState">
							<label for="checkPaid">Paid</label>
							<input type="checkbox" id="chekNotPaid" value="not paid" v-model="tableData.filterState">
							<label for="chekNotPaid">Not paid</label>
						</div>
			        </div>
			    </div>
		    
			<datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders"
			@sort="sortBy">
				<tbody>
					<tr v-for="invoice in invoices" :key="invoice.id">
						<td>{{invoice.id}}</td>
						<td>{{invoice.table_number}}</td>
						<td>{{invoice.meal_id}}</td>
						<td>{{invoice.name}}</td>
						<td>{{invoice.state}}</td>
						<td>{{invoice.total_price}}</td>
						<td>{{invoice.created_at}}</td>
						<td>

							<a class="btn btn-warning" title="download invoice" v-if="invoice.state=='paid'" @click="exportToPdf(invoice.id)">
								<i class="fas fa-file-pdf"></i>
							</a>
							<a class="btn btn-sm btn-primary" title="See associated items" v-on:click.prevent="$emit('invoiceItems', invoice)"><i class="fas fa-th-list"></i></a>
							<a class="btn btn-danger is-20" title="declare as not paid" v-if="invoice.state=='pending'" @click="$emit('declaredNotPaid', invoice.meal_id)">
								<i class="fab fa-creative-commons-nc-eu"></i>
							</a>
						</td>
					</tr>
				</tbody>
			</datatable>
			<pagination :pagination="pagination" @prev="getInvoices(pagination.prevPageUrl)"
				@next="getInvoices(pagination.nextPageUrl)">
			</pagination>
		</div>
	</div>
</template>

<script>
	import Datatable from './../datatable.vue';
	import Pagination from './../Pagination.vue';
	import Datepicker from './../datepicker.vue';
	import PaidInvoicesPDF from './../downloadPaidInvoicesPDF.vue';
	export default{
		components:{
			datatable: Datatable, 
			pagination: Pagination,
			datepicker: Datepicker,
			invoiceToPdf: PaidInvoicesPDF
		},
		created() {
	        this.getInvoices();
	    },
		data(){
			let sortOrders = {};
	        let columns = [
	            {width: '7%', label: 'ID', name: 'id', order:true},
	            {width: '7%', label: 'Table', name: 'table_number', order:true},
	            {width: '7%', label: 'Meal ID', name: 'meal_id', order:true},
	            {width: '20%', label: 'Responsable waiter', name: 'name', order:true },
	            {width: '20%', label: 'State', name: 'state', order:false},
	            {width: '12%', label: 'Total Price', name: 'total_price', order:true},
	            {width: '20%', label: 'Created at', name: 'created_at', order:true},
	            {width: '25%', label: 'Actions', name: 'actions', order:false},
	        ];
	        columns.forEach((column) => {
	           sortOrders[column.name] = -1;
	        });
			return{
				selectedInvoice:null,
				alertSucces:{
					show:false,
					Message:'',
				},
				invoices: [],
	            columns: columns,
	            sortKey: 'name',
	            sortOrders: sortOrders,
	            perPage: ['5','10', '20', '30'],
	            tableData: {
	            	date:'',
	            	filterState: ["pending","not paid"],
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
			exportToPdf(invoiceID){
                this.selectedInvoice = null;
				
				setTimeout(() => {
					this.selectedInvoice = invoiceID;
                }, 1);
			},
			getInvoices(url = 'api/manager/invoiceDataTable') {
	            this.tableData.draw++;
	            axios.get(url, {params: this.tableData})
	                .then(response => {
	                    let data = response.data;
	                    if (this.tableData.draw == data.draw) {
	                        this.invoices = data.data.data;
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
	            this.getInvoices();
	        },
	        
	        onDateSelected: function(date){
	        	this.tableData.date = date;
	        	this.getInvoices();
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
                this.getInvoices();
            },
            mealRemoved(){
                this.getInvoices();
            },
        },
	};
</script>