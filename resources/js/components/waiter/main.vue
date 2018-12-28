<template>
    <div>
        <create-meal @meal-created="newMeal" v-bind:userId="userId">Create Meal</create-meal>
        <meals @start-order="startOrder" @show-summary="showSummary" :newMeal="meal" v-bind:userId="userId">Create Meal</meals>
        <meal-summary v-show="mealSummary !== null" :mealId="mealSummary"></meal-summary>
        <items v-show="mealId !== null" @create-order="createOrder"></items>
        <orders-pending :newOrder="order" :mealId="mealId" v-bind:userId="userId"></orders-pending>
        <orders-prepared v-bind:userId="userId"></orders-prepared>
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
                mealSummary: null
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
            }
        },
    }
</script>

