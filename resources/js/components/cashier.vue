<template>
    <div v-if="invoiceFill != null">
        <invoice-fill @invoice-filled="invoiceFilled" @cancel="setFill" :invoiceRecived="invoiceFill"></invoice-fill>
    </div>
    <div v-else>
        <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders"
                   @sort="sortBy">

            <tbody>
            <tr class="color-box" v-for="invoice in invoices">
                <td>{{invoice.table_number}}</td>
                <td>{{invoice.waiter}}</td>
                <td>{{invoice.total_price}}</td>
                <td>
                    <a v-show="invoiceFill !== invoice.id" class="btn btn-sm btn-success"
                       v-on:click.prevent="setFill(invoice)">fill</a>
                    <a v-show="invoice.name !== null && invoice.nif !== null" class="btn btn-sm btn-success"
                       v-on:click.prevent="close(invoice)">close invoice</a>
                </td>
            </tr>
            </tbody>
        </datatable>
            <pagination :pagination="pagination" @prev="getInvoicesNotPaid(pagination.prevPageUrl)"
                        @next="getInvoicesNotPaid(pagination.nextPageUrl)">
            </pagination>
    </div>
</template>

<script>
    import invoiceFill from './invoiceFill.vue';
    import Datatable from './datatable.vue';
    import Pagination from './Pagination.vue';
    export default {
        components: {
            datatable: Datatable,
            pagination: Pagination,
            'invoice-fill': invoiceFill,
        },
        data: function () {
            let sortOrders = {};
            let columns = [
                {width: '7%', label: 'Table', name: 'Table', order:true},
                {width: '20%', label: 'Responsable waiter', name: 'name', order:true },
                {width: '12%', label: 'Total Price', name: 'total_price', order:true},
                {width: '5%', label: 'Actions', name: 'actions', order:false},
            ];
            columns.forEach((column) => {
                sortOrders[column.name] = -1;
            });
            return {
                invoices: [],
                invoiceFill: null,

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
            getInvoicesNotPaid(url = 'api/cashier'){
                this.tableData.draw++;
                axios.get(url,{params: this.tableData}).
                then(response=> {
                        this.tableData.draw++;
                        this.invoices = response.data.data;
                        console.log(response.data);
                        this.configPagination(response.data);
                    }
                )
                    .catch(errors => {
                        console.log(errors);
                    });
            },
            setFill(id){
                this.invoiceFill = id;
            },
            invoiceFilled(invoiceFilled){
                this.invoices.splice(this.invoices.findIndex(invoice => invoice.id === invoiceFilled.id), 1);
                this.invoices.unshift(invoiceFilled);
            },
            close(invoice){
                axios.put('api/meals/'+invoice.meal_id, {'state': 'paid'})
                    .then(response=>{});
                axios.put('api/invoices/'+invoice.id, {'state': 'paid'})
                    .then(response=>{
                        this.invoices.splice(this.invoices.findIndex(x => x.id === invoice.id), 1);
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
                this.getInvoicesNotPaid();
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
            }
        },
        mounted() {
            this.getInvoicesNotPaid();
        },
        sockets: {
            newInvoice(){
                let toast = this.$toasted.show("new invoice", {
                    theme: "outline",
                    position: "top-right",
                    duration: 10000
                });
                this.getInvoicesNotPaid();
            },
            updateInvoicesNotPaid(){
                let toast = this.$toasted.show("a manager changed a invoice", {
                    theme: "outline",
                    position: "top-right",
                    duration: 5000
                });
                this.getInvoicesNotPaid();
            }
        },

    }
</script>
