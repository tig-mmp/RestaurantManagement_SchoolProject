<template>
    <div>
        <create-meal @meal-created="newMeal" v-bind:userId="userId">Create Meal</create-meal>
        <meals @start-order="startOrder" @show-summary="showSummary" @end-meal="endMeal"
               :newMeal="meal" :mealIdToRemove="mealIdToRemove" v-bind:userId="userId">Create Meal</meals>
        <meal-summary v-show="mealSummary !== null" :mealId="mealSummary"></meal-summary>
        <items v-show="mealId !== null" @create-order="createOrder"></items>
        <orders-pending :newOrder="order" :mealId="mealId" :removeOrders="pendingOrdersToRemove"
                v-bind:userId="userId"></orders-pending>
        <orders-prepared :removeOrders="preparedOrdersToRemove" v-bind:userId="userId"></orders-prepared>
    </div>
</template>

<script type="text/javascript">
    import createMeal from './createMeal.vue';
    import meals from './meals.vue';
    import ordersPrepared from './ordersPrepared.vue';
    import items from './items.vue';
    import ordersPending from './ordersPending.vue';
    import summary from './summary.vue';

    export default {
        components: {
            'create-meal': createMeal,
            'meals': meals,
            'orders-prepared': ordersPrepared,
            'items': items,
            'orders-pending': ordersPending,
            'meal-summary': summary
        },
        props: ['userId'],
        data: function(){
            return {
                meal: '',
                mealId: null,
                order: '',
                mealSummary: null,
                pendingOrdersToRemove: null,
                preparedOrdersToRemove: null,
                mealIdToRemove: null,
            }
        },
        methods: {
            newMeal(meal){
                this.meal = meal;
            },
            startOrder(id) {
                this.mealId = id;
            },
            createOrder(id){
                axios.post('/api/orders/create', {'item_id': id, 'meal_id': this.mealId, 'responsible_cook_id': this.userId})
                .then(response=>{
                    this.order = response.data;
                });
            },
            showSummary(id){
                this.mealSummary = id;
            },
            endMeal(id){
                this.pendingOrdersToRemove = [];
                this.preparedOrdersToRemove = [];
                var price = 0;
                axios.get('api/meals/'+id+'/onGoingOrders')
                .then(response=>{
                    response.data.forEach((order) => {
                        if (order.state === 'pending') {
                            this.pendingOrdersToRemove.push(order.id);
                        }
                        if (order.state === 'confirmed'){
                            this.pendingOrdersToRemove.push(order.id);
                            this.$socket.emit('removePendingOrder', order.id);
                        }
                        if (order.state === 'in preparation'){
                            this.$socket.emit('removeInPreparationOrder', order.id, order.responsible_cook_id);
                        }
                        if (order.state === 'prepared') {
                            this.preparedOrdersToRemove.push(order.id);
                        }
                        axios.put('/api/orders/'+order.id, {'state': 'not delivered'}).then(response=>{});
                        price = Math.round(+price + +order.item.price);
                    });
                })
                .then(response=>(
                    axios.put('/api/meals/'+id, {'price' : -price, 'state': 'terminated'})
                    .then(response=>{
                        this.mealIdToRemove = id;
                        this.$socket.emit('mealRemoved', response.data.data.table_number);
                    })
                ));
            }
        },
    }
</script>

