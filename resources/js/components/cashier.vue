<template>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">Table</th>
            <th scope="col">Id Waiter</th>
            <th scope="col">Total Price</th>

        </tr>
        </thead>
        <tbody>
        <tr class="color-box" v-for="meal in meals">
            <td>{{meal.table_number}}</td>
            <td>{{meal.responsible_waiter_id}}</td>
            <td>{{meal.total_price_preview}}</td>


        </tr>
        </tbody>
    </table>
</template>
<script>
    module.exports= {
        data: function () {
            return {
                meals: []
            }
        },
        methods: {

            getMealsNotPaid(){
                axios.get( 'api/users/cashier').
                then(response=>{
                    this.meals = response.data.data;
                })
            },
        },
        mounted() {
            this.getMealsNotPaid();
        },
        sockets: {
            },

    }
</script>
