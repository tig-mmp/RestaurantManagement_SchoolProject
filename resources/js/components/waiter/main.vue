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
                pendingOrdersToRemove: [],
                preparedOrdersToRemove: [],
                mealIdToRemove: null,
            }
        },
        methods: {
            newMeal(meal){//envia a nova meal do createMeal para o meals
                this.meal = meal;
            },
            startOrder(id) {
                this.mealId = id;
            },
            createOrder(id){
                axios.post('/api/orders', {'item_id': id, 'meal_id': this.mealId})
                .then(response=>{
                    this.order = response.data;
                    axios.get('api/meals/'+response.data.meal_id+'/invoice')
                    .then(response=>{
                        if (response.data === '') {
                            axios.post('/api/invoices', {'meal_id': this.order.meal_id}).then(response => {
                                //enviar para o manager response.data
                            });
                        }
                    });
                }).catch(function (error) {
                    this.createOrder(id);
                });
            },
            showSummary(id){
                this.mealSummary = id;
            },
            endMeal(id){
                this.pendingOrdersToRemove = [];
                this.preparedOrdersToRemove = [];
                var price = 0;
                var i = 0;
                axios.all([this.getOnGoingOrders(id), this.getInvoiceId(id)])
                .then(axios.spread((orders, invoice) => {
                    if (invoice.data !== ''){
                        axios.get('api/invoices/'+invoice.data.id+'/totalPrice')
                        .then(response=>{
                            axios.put('api/invoices/'+invoice.data.id, {'total_price' : response.data})
                                .then(response=>{});
                        });
                    }
                    orders.data.forEach((order) => {
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
                        price = +price + +order.item.price;
                        i++;
                        if(i === orders.data.length) {//espera que calcule o preço
                            this.terminarMeal(id, price);
                        }
                    });
                    if (orders.data.length === 0) {//se todas as orders estão terminadas
                        this.terminarMeal(id, price);
                    }
                }));
            },
            getOnGoingOrders(id){
                return axios.get('api/meals/'+id+'/onGoingOrders');
            },
            getInvoiceId(id){
                return axios.get('api/meals/'+id+'/invoice');
            },
            terminarMeal(mealId, price){
                axios.put('api/meals/'+mealId, {'price' : -price, 'state': 'terminated'})
                .then(response=>{
                    this.mealIdToRemove = mealId;
                    this.$socket.emit('mealRemoved', response.data.data.table_number);
                });
            }
        }
    }
</script>

