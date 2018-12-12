<template>
<table class="table">
        <thead>
        <tr>
            <th scope="col">start</th>
            <th scope="col">name</th>
            <th scope="col">state</th>
            <th scope="col">table_number</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody> <!--v-bind:bind:backgrounds="[order.state === 'confirmed' ? 'bg-red' : 'bg-green']"-->
        <tr class="color-box" v-for="order in orders" :key="order.id"
            v-bind:style="[order.state === 'confirmed' ? {'background-color' : 'Crimson'} : {'background-color' : 'LightSkyBlue'}]">
            <td>{{order.start}}</td>
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
                console.log(id);
                console.log(state);
                axios.put('/api/order/'+id, {'state':state})
                    .then(response=>{
                        console.log(response);
                        this.getOrders();
                    });
            },
            getOrders(){
                axios.get( 'api/users/'+this.$store.state.user.id+'/orders').
                then(response=>{
                    this.orders = response.data.data;
                })
            },
        },
        mounted() {
            this.getOrders();
        },
        sockets: {
            updateOrder(){
                console.log("updating");
                this.getOrders();
            },
        },
    }
</script>
