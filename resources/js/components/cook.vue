<template>
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
            <tr class="color-box" v-for="order in orders" :key="order.id"
                v-bind:style="[order.state === 'confirmed' ? {'background-color' : 'Crimson'} : {'background-color' : 'LightSkyBlue'}]">
                <td>{{order.start}}</td>
                <img :src="'/imgItems/' + order.photo_url" class="rounded-circle border border-warning" width="25" height="25" >
                <td>{{order.name}}</td>
                <td>{{order.state}}</td>
                <td>{{order.table_number}}</td>
                <td>
                    <a v-show="order.state === 'in preparation'" class="btn btn-sm btn-success" v-on:click.prevent="prepare(order.id, 'prepared')">Prepared</a>
                    <a v-show="order.state === 'confirmed'" class="btn btn-sm btn-success" v-on:click.prevent="prepare(order.id, 'in preparation')">Start preparing</a>
                </td>
            </tr>
        </tbody>
    </table>
</template>
<script>
    module.exports= {
        data: function () {
            return {
                orders: []
            }
        },
        methods: {
            prepare(id, state){
                axios.put('/api/order/'+id, {'state':state, 'responsible_cook_id' : this.$store.state.user.id})
                .then(response=>{
                    this.orders.splice(this.orders.findIndex(v => v.id === id), 1);
                    if (response.data.data.state === 'in preparation'){
                        this.orders.push(response.data.data);
                        this.sortOrders();
                    } else{
                        this.$socket.emit('orderPrepared', response.data.data, response.data.data.waiter_id);
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
                    return b.start > a.start ? 1 : -1;
                });
            }
        },
        mounted() {
            this.getOrders();
        },
        sockets: {
            updateConfirmedOrder(order){
                this.orders.push(order);
                this.sortOrders();
            },
        },
    }
</script>
