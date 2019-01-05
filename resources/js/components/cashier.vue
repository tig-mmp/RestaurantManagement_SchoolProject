<template>
    <div v-if="invoiceFill != null">
        <invoice-fill @invoice-filled="invoiceFilled" @cancel="setFill" :invoiceRecived="invoiceFill"></invoice-fill>
    </div>
    <div v-else>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Table</th>
                <th scope="col">Waiter</th>
                <th scope="col">Total Price</th>
                <th scope="col">Actions</th>
            </tr>
            </thead>
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
        </table>
    </div>
</template>

<script>
    import invoiceFill from './invoiceFill.vue';
    export default {
        components: {
            'invoice-fill': invoiceFill,
        },
        data: function () {
            return {
                invoices: [],
                invoices_all: [],
                invoiceFill: null
            }
        },
        methods: {
            getInvoicesNotPaid(){
                axios.get('api/cashier').
                then(response=>{
                    console.log(response.data.data);
                    this.invoices = response.data.data;
                })
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
            getInvoicesAll(){
                axios.get('api/cashier_all').
                then(response=>{
                    this.invoices_all = response.data.data;
                })
            }
        },
        mounted() {
            this.getInvoicesNotPaid();
            //this.getInvoicesAll();
        },
        sockets: {
            newInvoice(invoice){
                let toast = this.$toasted.show("new invoice", {
                    theme: "outline",
                    position: "top-right",
                    duration: 10000
                });
                this.invoices.push(invoice);
            }
        },

    }
</script>
