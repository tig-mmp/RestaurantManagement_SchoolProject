<template>
    <div>
        <h3>Pending Orders</h3>
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
            <tr class="color-box" v-for="order in orders" :key="order.id"
                v-bind:style="[order.state === 'confirmed' ? {'background-color' : 'Crimson'} : {'background-color' : 'LightSkyBlue'}]">
                <img :src="'/imgItems/' + order.photo_url" class="rounded-circle border border-warning" width="25" height="25" >
                <td>{{order.name}}</td>
                <td>{{order.state}}</td>
                <td>{{order.table_number}}</td>
                <td>
                    <a v-show="issetButton(order.id)" class="btn btn-sm btn-success" v-on:click.prevent="deleteOrder(order.id)">Cancel</a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
    module.exports= {
        props: ['newOrder', 'userId'],
        data: function () {
            return {
                orders: [],
                deleteButton: []
            }
        },
        methods: {
            deleteOrder(id){
                axios.delete('/api/orders/' + id)
                    .then(function (response) {})
                    .catch(function (error) {
                        console.log(error);
                    });
                this.orders.splice(this.orders.findIndex(v => v.id === id), 1);
                this.hideButton(id);
            },
            hideButton(id){
                this.deleteButton.splice(this.deleteButton.findIndex(v => v.id === id), 1);
            },
            timeoutOrder: function(id){
                setTimeout(() => {
                    this.hideButton(id);
                    this.confirmOrder(id);
                }, 5000)
            },
            confirmOrder(id){
                axios.put('/api/order/'+id, {'state' : 'confirmed'})
                    .then(response=>{
                        this.$socket.emit('orderConfirmed', response.data.data);
                        this.orders.splice(this.orders.findIndex(v => v.id === id), 1);
                        this.orders.unshift(response.data.data);
                        axios.put('/api/meal/'+response.data.data.meal_id, {'price' : this.newOrder.price}).
                            then(response=>{});
                    });
            },
            issetButton(id){
                return this.deleteButton.find(function(val){return val === id;});
            }
        },
        mounted() {
            axios.get('api/users/waiter/'+this.userId+'/pendingOrders').then(response=>{
                this.orders = response.data.data;
            });
        },
        watch: {
            newOrder: function (order) {
                this.orders.unshift(order);
                this.deleteButton.push(order.id);
                this.timeoutOrder(order.id);
            }
        }
    }
</script>
