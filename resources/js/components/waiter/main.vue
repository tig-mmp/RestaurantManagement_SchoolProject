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
                invoiceItems : []
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
                }).catch(function (error) {});
            },
            showSummary(id){
                this.mealSummary = id;
            },
            endMeal(id){
                this.pendingOrdersToRemove = [];
                this.preparedOrdersToRemove = [];
                var priceInvalid = 0;
                var i = 0;
                var invoiceItemsIndice = this.invoiceItems.length;
                this.invoiceItems[invoiceItemsIndice] = [];
                axios.get('api/meals/'+id+'/orders')
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
                        if (order.state === 'delivered') {
                            this.getInvoiceItem(invoiceItemsIndice, order);
                        } else{//se onGoing não foi entregue
                            axios.put('/api/orders/'+order.id, {'state': 'not delivered'}).then(response=>{});
                            priceInvalid = Math.floor((+priceInvalid + +order.item.price) * 100) / 100;
                        }
                        i++;
                        if(i === response.data.length) {//espera que calcule o preço a remover
                            this.terminarMeal(id, priceInvalid);
                            this.createInvoice(invoiceItemsIndice, id);
                        }
                    });
                    if (response.data.length === 0) {//se todas as orders estão terminadas
                        this.terminarMeal(invoiceItemsIndice, id, priceInvalid);
                        this.createInvoice(id);
                    }
                }).catch(function (error) {
                    console.log(error)}
                );
            },
            getInvoiceItem(i, order){
                let obj = this.invoiceItems[i].find(x => x.item_id === order.item_id);
                if (obj === undefined){
                    this.invoiceItems[i].push(
                        {
                            item_id : order.item_id,
                            quantity : 1,
                            unit_price : Number(order.item.price),
                            sub_total_price : Number(order.item.price),
                        }
                    );
                } else{
                    let index = this.invoiceItems[i].indexOf(obj);
                    this.invoiceItems[i][index].quantity = Number(this.invoiceItems[i][index].quantity) + 1;
                    this.invoiceItems[i][index].sub_total_price = Math.floor((this.invoiceItems[i][index].sub_total_price +
                        this.invoiceItems[i][index].unit_price) * 100) / 100; //para os arredondamentos
                }
            },
            terminarMeal(mealId, price){
                axios.put('api/meals/'+mealId, {'price' : -price, 'state': 'terminated'})
                    .then(response=>{
                        this.mealIdToRemove = mealId;
                        this.$socket.emit('mealRemoved', response.data.data.table_number, mealId);
                    });
            },
            createInvoice(indice, mealId){
                var i = 0;
                var price = 0;
                axios.post('/api/invoices', {'meal_id': mealId})
                    .then(response => {
                        this.invoiceItems[indice].forEach((invoiceItem) => {
                            invoiceItem.invoice_id = response.data.id;
                            axios.post('/api/invoiceItems', invoiceItem)
                                .then(response => {console.log(response.data)});
                            i++;
                            price = Math.floor((+price + +invoiceItem.sub_total_price) * 100) / 100;
                            if(i === this.invoiceItems[indice].length) {//espera que percorra tudo
                                axios.put('api/invoices/'+response.data.id, {'total_price' : price})
                                    .then(response=>{console.log(response.data)});
                            }
                        });
                    });
            }
        }
    }
</script>

