<template>
    <div>
        <h1>Orders to prepare</h1>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-9">
                    <div class="control">
                        <select class="custom-select col-sm-1" v-model="tableData.length" @change="getOrders()">
                            <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <datatable>
                <tbody>
                <tr v-for="order in orders" :key="order.id">
                    <td>{{order.start}}</td>
                    <img :src="'/imgItems/' + order.photo_url" class="rounded-circle border border-warning" width="25" height="25" >
                    <td>{{order.name}}</td>
                    <td>{{order.state}}</td>
                    <td>{{order.type}}</td>
                    <td>{{order.table_number}}</td>
                    <td>
                        <a class="btn btn-sm btn-success" v-on:click.prevent="prepare(order.id, 'prepared')">Prepared</a>
                        <a class="btn btn-sm btn-success" v-show="order.state === 'confirmed'" v-on:click.prevent="prepare(order.id, 'in preparation')">Start preparing</a>
                    </td>
                </tr>
                </tbody>
            </datatable>
            <pagination :pagination="pagination" @prev="getOrders(pagination.prevPageUrl)"
                        @next="getOrders(pagination.nextPageUrl)">
            </pagination>
        </div>
    </div>
</template>

<script>
    import Datatable from '././datatable.vue';
    import Pagination from '././Pagination.vue';
    export default{
        components:{
            datatable: Datatable,
            pagination: Pagination
        },
        mounted() {
            this.getOrders();
        },
        data(){
            return{
                orders: [],
                perPage: ['5','10', '20', '30', '50'],
                tableData: {
                    draw: 0,
                    length: 5,
                },
                pagination: {
                    lastPage: '',
                    currentPage: '',
                    total: '',
                    lastPageUrl: '',
                    nextPageUrl: '',
                    prevPageUrl: '',
                    from: '',
                    to: ''
                },
            }
        },
        methods:{
            prepare(id, state){
                axios.put('/api/orders/'+id, {'state':state, 'responsible_cook_id' : this.$store.state.user.id})
                    .then(response=>{
                        this.$socket.emit('orderPreparing', response.data.data.id, response.data.data.waiter_id);//atualiza a lista e remove do waiter responsavel
                        this.getOrders();
                        if (response.data.data.state === 'in preparation'){
                            this.sortOrders();
                        } else{
                            this.$socket.emit('orderPrepared', response.data.data, response.data.data.waiter_id);
                        }
                    });
            },
            getOrders(url = 'api/users/cook/'+this.$store.state.user.id+'/orders') {
                this.tableData.draw++;
                axios.get(url, {params: this.tableData})
                    .then(response => {
                        this.orders = response.data.data.data;
                        this.configPagination(response.data.data);
                    })
                    .catch(errors => {
                        console.log(errors);
                    });
            },
            configPagination(data) {
                this.pagination.lastPage = data.last_page;
                this.pagination.currentPage = data.current_page;
                this.pagination.total = data.total;
                this.pagination.lastPageUrl = data.last_page_url;
                this.pagination.nextPageUrl = data.next_page_url;
                this.pagination.prevPageUrl = data.prev_page_url;
                this.pagination.from = data.from;
                this.pagination.to = data.to;
            }
        },
        sockets: {
            cookUpdateOrders() {
                let toast = this.$toasted.show("orders updated", {
                    theme: "outline",
                    position: "top-right",
                    duration: 2500
                });
                let url = this.pagination.lastPageUrl;
                this.getOrders(url.slice(0, -1) + this.pagination.currentPage);
            }
        }
    };
</script>
