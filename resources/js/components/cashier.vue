<template>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">Table</th>
            <th scope="col">Waiter</th>
            <th scope="col">Total Price</th>

        </tr>
        </thead>
        <tbody>
        <tr class="color-box" v-for="invoice in invoices">
            <td>{{invoice.table_number}}</td>
            <td>{{invoice.name}}</td>
            <td>{{invoice.total_price}}</td>


        </tr>
        </tbody>
    </table>
</template>
<script>
    module.exports= {
        data: function () {
            return {
                invoices: []
            }
        },
        methods: {

            getInvoicesNotPaid(){
                axios.get('api/cashier').
                then(response=>{
                    this.invoices = response.data.data;
                    console.log(response.data.data);
                    console.log(response.data.data);
                })
            },getInvoicesAll(){
                axios.get('api/cashier_all').
                then(response=>{
                    this.invoices = response.data.data;
                    console.log(response.data.data);
                    console.log(response.data.data);
                })
            },
        },
        mounted() {
            this.getInvoicesNotPaid();
        },
        sockets: {
            },

    }
</script>
