<template>

    <table>
        <thead>
        <tr>
            <th>id</th>
            <th>start</th>
            <th>end</th>
            <th>item_id</th>
            <th>meal_id</th>
            <th>name</th>
            <th>state</th>
            <th>table_number</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="order in orders">
            <td>{{order.id}}</td>
            <td>{{order.start}}</td>
            <td>{{order.end}}</td>
            <td>{{order.item_id}}</td>
            <td>{{order.meal_id}}</td>
            <td>{{order.name}}</td>
            <td>{{order.state}}</td>
            <td>{{order.table_number}}</td>
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
        },
        mounted() {
            axios.get( 'api/users/'+this.$store.state.user.id+'/orders').
            then(response=>{
                this.orders = response.data.data;
            })
        }
    }
</script>
