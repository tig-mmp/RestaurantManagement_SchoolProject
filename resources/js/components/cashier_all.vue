<template>
    <div >
    <invoiceToPdf :invoiceID="selectedInvoice"></invoiceToPdf>
    <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders"
               @sort="sortBy">


        <tbody>

        <tr class="color-box" v-for="invoice in invoices_all">

               <td> <p >{{invoice.table}}</p></td>

            <td><p >{{invoice.name}}</p></td>
            <td><p >{{invoice.price_total}}</p></td>
            <td>
                <p >{{invoice.date}}</p></td>

            <td>
        <tr v-for="item in invoice.items">{{'Quant: ' + item.quantity + ' Name:' + item.name + ' Unit Price:' + item.unit_price + ' Sub-Total:' + item.sub_total_price}}</tr></td>

        <td><a class="btn btn-warning" title="download pdf" v-if="invoice.state=='paid'" @click="exportToPdf(invoice.id)">
            <i class="fas fa-file-pdf"></i>
        </a></td>


        </tr>
        </tbody>
    </datatable>
    <pagination :pagination="pagination" @prev="getInvoicesAll(pagination.prevPageUrl)"
                @next="getInvoicesAll(pagination.nextPageUrl)">
    </pagination>

    </div>
</template>

<script>
    import PaidInvoicesPDF from './downloadPaidInvoicesPDF.vue';
    import Datatable from './datatable.vue';
    import Pagination from './Pagination.vue';
    export default{
        components:{
            datatable: Datatable,
            pagination: Pagination,
            invoiceToPdf: PaidInvoicesPDF
        },
        data: function () {
            let sortOrders = {};
            let columns = [
                {width: '7%', label: 'Table', name: 'Table', order:true},
                {width: '20%', label: 'Responsable waiter', name: 'name', order:true },
                {width: '12%', label: 'Total Price', name: 'total_price', order:true},
                {width: '12%', label: 'Date', name: 'date', order:true},
                {width: '35%', label: 'Item', name: 'item', order:true},
                {width: '5%', label: 'Actions', name: 'actions', order:false},
            ];
            columns.forEach((column) => {
                sortOrders[column.name] = -1;
            });
            return {

                selectedInvoice:null,
                invoices_all: [],

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
        methods: {
            exportToPdf(invoiceID){
                this.selectedInvoice = null;

                setTimeout(() => {
                    this.selectedInvoice = invoiceID;
                }, 1);
            },
            getInvoicesAll(url = 'api/cashier_all'){
                this.tableData.draw++;
                axios.get(url,{params: this.tableData}).
                then(response=> {
                    this.tableData.draw++;
                        this.invoices_all = response.data.data;
                        console.log(response.data);
                        this.configPagination(response.data);
                    }
                )
                    .catch(errors => {
                        console.log(errors);
                    });
            },
            configPagination(data) {
                this.pagination.lastPage = data.meta.last_page;
                this.pagination.currentPage = data.meta.current_page;
                this.pagination.total = data.meta.total;
                this.pagination.lastPageUrl = data.links.last;
                this.pagination.nextPageUrl = data.links.next;
                this.pagination.prevPageUrl = data.links.prev;
                this.pagination.from = data.meta.from;
                this.pagination.to = data.meta.to;
            },

            sortBy(key) {
                this.sortKey = key;
                this.sortOrders[key] = this.sortOrders[key] * -1;
                this.tableData.column = this.getIndex(this.columns, 'name', key);
                this.tableData.dir = this.sortOrders[key] === 1 ? 'asc' : 'desc';
                this.getInvoicesAll();
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
        mounted() {
            this.getInvoicesAll();
        },
        sockets: {
            },

    }
</script>
