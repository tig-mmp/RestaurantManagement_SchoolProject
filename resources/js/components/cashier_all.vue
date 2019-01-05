<template>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">Table</th>
            <th scope="col">Waiter</th>
            <th scope="col">Total Price</th>
            <th scope="col">Date</th>
            <th scope="col">Item</th>

        </tr>
        </thead>
        <tbody>
        <tr class="color-box" v-for="invoice in invoices_all">

               <td> <p >{{invoice.table}}</p></td>

            <td><p >{{invoice.name}}</p></td>
            <td><p >{{invoice.price_total}}</p></td>
            <td>
                <p >{{invoice.date}}</p></td>

            <td>
        <tr v-for="item in invoice.items">{{'Quant: ' + item.quantity + ' Name:' + item.name + ' Unit Price:' + item.unit_price + ' Sub-Total' + item.sub_total_price}}</tr></td>




        </tr>
        </tbody>
    </table>


</template>

<script>
    module.exports= {
        data: function () {
            return {
                invoices_all: [],

            }
        },
        methods: {

            getInvoicesAll(){
                axios.get('api/cashier_all').
                then(response=>{
                    this.invoices_all = response.data.data;
                    console.log(response.data);
                })
            },
            setTest(table){
                this.test = table;
            },
        },
        mounted() {
            this.getInvoicesAll();
        },
        sockets: {
            },

    }
</script>
