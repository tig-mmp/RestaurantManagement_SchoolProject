<template>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">Table</th>
            <th scope="col">Waiter</th>
            <th scope="col">Total Price</th>
            <th scope="col">Date</th>
            <th scope="col">Item</th>
            <th scope="col">Quantity</th>
            <th scope="col">Unit Price</th>
            <th scope="col">Sub-Total Price</th>

        </tr>
        </thead>
        <tbody>
        <tr class="color-box" v-for="invoice in invoices_all">
            <td v-if="this.table !=invoice.table_number">

                <p >{{this.table = invoice.table_number}}</p></td>
            <td v-else></td>
            <td v-if="this.namee != invoice.name">

            <p >{{this.namee = invoice.name}}</p></td>
            <td v-else></td>
            <td v-if="this.price_total !=invoice.total_price">

            <p >{{this.price_total = invoice.total_price}}</p></td>
            <td v-else></td>
            <td>
                <p >{{invoice.date}}</p></td>
            <td>
                <p >{{invoice.item}}</p></td>
            <td>
                <p >{{invoice.quantity}}</p></td>
            <td>
                <p >{{invoice.unit_price}}</p></td>
            <td>
                <p >{{invoice.sub_total_price}}</p></td>



        </tr>
        </tbody>
    </table>


</template>

<script>
    module.exports= {
        data: function () {
            return {
                invoices_all: [],
                table: "",
                namee: "",
                price_total: "",
            }
        },
        methods: {

            getInvoicesAll(){
                axios.get('api/cashier_all').
                then(response=>{
                    this.invoices_all = response.data.data;
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
