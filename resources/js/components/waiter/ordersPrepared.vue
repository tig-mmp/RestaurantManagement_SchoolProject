<template>
    <div>
        <h3>Prepared orders</h3>
        <a class="btn btn-sm btn-success" v-show="!hide" v-on:click.prevent="setHide(true)">hide</a>
        <a class="btn btn-sm btn-success" v-show="hide" v-on:click.prevent="setHide(false)">show</a>
        <table class="table" v-show="!hide">
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
                    <a class="btn btn-sm btn-success" v-on:click.prevent="deliver(order.id)">Delivered</a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
    module.exports= {
        props: ['userId', 'removeOrders'],
        data: function () {
            return {
                orders: [],
                hide: false
            }
        },
        methods: {
            setHide(decisao){
                this.hide = decisao;
            },
            deliver(id){
                axios.put('/api/orders/'+id, {'state' : 'delivered'})
                .then(response=> {
                    this.$socket.emit('updateOrder', response.data.data.meal_id);
                    this.orders.splice(this.orders.findIndex(v => v.id === id), 1);
                });
            },
            getPreparedOrders(){
                axios.get('api/users/waiter/'+this.userId+'/preparedOrders').then(response=>{
                    if (response.data != ''){
                        this.orders = response.data.data;
                    }
                }).catch(function (error) {
                    console.log(error);
                });
            }
        },
        mounted() {
            this.getPreparedOrders();
        },
        watch: {
            removeOrders: function (ordersRecived) {
                ordersRecived.forEach((orderId) => {
                    this.orders.splice(this.orders.findIndex(order => order.id === orderId), 1);
                });
            }
        },
        sockets: {
            orderPrepared(order){
                this.orders.push(order);
            },
            waiterUpdateOrders(){
                this.orders = [];
                this.getPreparedOrders();
            },
        }
    }
</script>