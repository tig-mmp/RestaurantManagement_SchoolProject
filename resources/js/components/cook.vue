<template>
    <div>
        <h3>Orders to prepare</h3>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">start</th>
                    <th scope="col">image</th>
                    <th scope="col">name</th>
                    <th scope="col">state</th>
                    <th scope="col">table_number</th>
                    <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr class="color-box" v-for="order in orders"
                    v-bind:style="[order.state === 'confirmed' ? {'background-color' : 'Crimson'} : {'background-color' : 'LightSkyBlue'}]">
                    <td>{{order.start}}</td>
                    <img :src="'/imgItems/' + order.photo_url" class="rounded-circle border border-warning" width="25" height="25" >
                    <td>{{order.name}}</td>
                    <td>{{order.state}}</td>
                    <td>{{order.table_number}}</td>
                    <td>
                        <a class="btn btn-sm btn-success"
                           v-on:click.prevent="prepare(order.id, order.state, 'prepared')">Prepared</a>
                        <a class="btn btn-sm btn-success" v-show="order.state === 'confirmed'"
                           v-on:click.prevent="prepare(order.id, order.state, 'in preparation')">Start preparing</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
    module.exports= {
        data: function () {
            return {
                orders: []
            }
        },
        methods: {
            prepare(id, oldState, state){
                axios.put('/api/orders/'+id, {'state':state, 'responsible_cook_id' : this.$store.state.user.id})
                .then(response=>{
                    this.$socket.emit('updateOrder', response.data.data.meal_id);
                    if (oldState === 'confirmed') {
                        this.$socket.emit('orderRemoveWaiterPendingAddAllCook', response.data.data.id, response.data.data.waiter_id);
                        if (state === 'prepared'){
                            this.$socket.emit('orderAddWaiterPrepared', response.data.data, response.data.data.waiter_id);
                        }
                    } else{
                        this.removeOrder(id);
                        this.$socket.emit('orderAddWaiterPrepared', response.data.data, response.data.data.waiter_id);
                    }
                });
            },
            getOrders(){
                axios.get('api/users/cook/'+this.$store.state.user.id+'/orders').
                then(response=>{
                    this.orders = response.data.data;
                })
            },
            sortOrders(){
                this.orders.sort(function(a, b){
                    if (a.state.charAt(0) !== b.state.charAt(0)) {
                        return a.state.charAt(0) === 'c' ? 1 : -1;
                    }
                    return b.start > a.start ? -1 : 1;
                });
            },
            addSortedOrder(order){
                this.orders.unshift(order);
                this.sortOrders();
            },
            removeOrder(id){
                this.orders.splice(this.orders.findIndex(order => order.id === id), 1);
            }
        },
        mounted() {
            this.getOrders();
        },
        sockets: {
            cookNewOrder(order) {
                let toast = this.$toasted.show("new order", {
                    theme: "outline",
                    position: "top-right",
                    duration: 3000
                });
                this.orders.push(order);
            },
            cookRemoveOrder(orderId) {
                let toast = this.$toasted.show("removing order", {
                    theme: "outline",
                    position: "top-right",
                    duration: 3000
                });
                this.getOrders();
            },
            mealTerminated() {
                let toast = this.$toasted.show("meal terminated", {
                    theme: "outline",
                    position: "top-right",
                    duration: 3000
                });
                this.getOrders();
            }
        }
    };
</script>
