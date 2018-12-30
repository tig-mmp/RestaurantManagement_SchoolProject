<template>
    <div>
        <h3>Prepared orders</h3>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">image</th>
                <th scope="col">name</th>
                <th scope="col">state</th>
                <th scope="col">table_number</th>
                <th scope="col">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr class="color-box" v-for="order in orders" :key="order.id">
                <img :src="'/imgItems/' + order.photo_url" class="rounded-circle border border-warning" width="25" height="25" >
                <td>{{order.name}}</td>
                <td>{{order.state}}</td>
                <td>{{order.table_number}}</td>
                <td>
                    <a class="btn btn-sm btn-success" v-on:click.prevent="delivered(order.id)">Delivered</a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
    module.exports= {
        props: ['userId'],
        data: function () {
            return {
                orders: [],
            }
        },
        methods: {
            delivered(id){
                axios.put('/api/orders/'+id, {'state' : 'delivered'})
                    .then(response=>{
                        this.orders.splice(this.orders.findIndex(v => v.id === id), 1);
                    });
            }
        },
        mounted() {
            axios.get('api/users/waiter/'+this.userId+'/preparedOrders').then(response=>{
                this.orders = response.data.data;
            });
        },
        watch: {
            removeOrders: function (ordersRecived) {
                ordersRecived.forEach((orderId) => {
                    this.orders.splice(this.orders.findIndex(order => order.id === orderId), 1);
                });
            }
        },
        sockets: {
            orderPreparedUpdate(order){
                this.orders.push(order);
            },
        }
    }
</script>