<template>
	<div>
		
	</div>
</template>
<script>
    export default{
    	props:['invoiceID'],
        data(){
        	return{
        		invoiceItems:[],
        		invoice:{
        			id:null,
        			table_number:null,
        			meal_id:null,
        			name:null,
        			state:null,
        			total_price:null,
        			created_at:null
        		}
        	}
        },
        methods:{
        	getInvoiceItems(){
        		axios.get('api/invoice/paid/pdf', {params: this.invoice})
	                .then(response => {
	                    this.invoice = response.data.data.data[0];
	                    axios.get('api/invoice/paid/items/pdf', {params: this.invoice})
		                .then(response => {
		                    this.invoiceItems = response.data.data.data;
		                    this.exportPdf();
		                })
		                .catch(errors => {
		                    console.log(errors);
		                });
	                })
	                .catch(errors => {
	                    console.log(errors);
	                });
        		

        	},
        	exportPdf(){
        		var invoiceColumns = [
        			{title: 'ID', dataKey: 'id'},
		            {title: 'Table', dataKey: 'table_number'},
		            {title: 'Meal ID', dataKey: 'meal_id'},
		            {title: 'Responsable waiter', dataKey: 'name' },
		            {title: 'State', dataKey: 'state'},
		            {title: 'Total Price', dataKey: 'total_price'},
		            {title: 'Created at', dataKey: 'created_at'},
        		];

        		var invoiceItemsColumns = [
        			{title: 'Type', dataKey: 'type'},
		            {title: 'Name', dataKey: 'name'},
		            {title: 'Quantity', dataKey: 'quantity'},
		            {title: 'Unit Price', dataKey: 'unit_price' },
		            {title: 'Total Price', dataKey: 'sub_total_price'}
        		];

        		var invoiceHeader = [];
        		invoiceHeader.push(this.invoice);

        		var doc = new jsPDF('p', 'pt');
                doc.text(40, 30, 'Invoice');
        		doc.autoTable(invoiceColumns, invoiceHeader, {
				    margin: {top: 40},
				});
				doc.text(40, 127, 'Invoice Items');
				doc.autoTable(invoiceItemsColumns, this.invoiceItems, {
				    margin: {top: 140},
				});
        		doc.save('invoice.pdf');
        	},
        },
        watch: { 
      		invoiceID: function(newVal, oldVal) { // watch it
      			if(newVal != null){
      				this.invoice.id = newVal;
      				this.getInvoiceItems();
      			}
			}
		},
    };
</script>