<template>
    <div class="container-fluid">
        <h4 class="box-content right">Table nº<b>{{meal.table_number}}</b></h4>
        <h4 class="box-content right">Actual price: <b>{{meal.total_price_preview}}</b></h4>
        <div class="row">
            <div class="col-sm-9">
                <input class="form-control col-sm-2" type="text" v-model="tableData.search" placeholder="Search Table" @input="getItems()">
                <div class="control">
                    <select class="custom-select col-sm-1" v-model="tableData.length" @change="getItems()">
                        <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
                    </select>
                </div>
            </div>
        </div>
        <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy">
            <tbody>
            <tr v-for="item in items" :key="item.id">
                <td><img :src="'/imgItems/' + item.item.photo_url" width="40" height="40" ></td>
                <td>{{item.item.name}}</td>
                <td>{{item.item.type}}</td>
                <td>{{item.item.price}}</td>
                <td>{{item.state}}</td>
            </tr>
            </tbody>
        </datatable>
        <pagination :pagination="pagination" @prev="getItems(pagination.prevPageUrl)"
                    @next="getItems(pagination.nextPageUrl)">
        </pagination>
    </div>
</template>

<script>
    import Datatable from './../datatable.vue';
    import Pagination from './../Pagination.vue';
    export default{
        components:{
            datatable: Datatable,
            pagination: Pagination
        },
        props: ['mealId'],
        mounted() {
            this.getItems();
        },
        data(){
            let sortOrders = {};
            let columns = [
                {width: '20%', label: 'Image', name: 'img', order:false},
                {width: '20%', label: 'Name', name: 'name', order:true },
                {width: '20%', label: 'Type', name: 'type', order:true},
                {width: '20%', label: 'Price', name: 'price', order:true},
                {width: '20%', label: 'State', name: 'state', order:false},
            ];
            columns.forEach((column) => {
                sortOrders[column.name] = -1;
            });
            return{
                meal: '',
                items: [],
                columns: columns,
                sortKey: 'name',
                sortOrders: sortOrders,
                perPage: ['5','10', '20', '30'],
                tableData: {
                    draw: 0,
                    length: 5,
                    search: '',
                    column: 0,
                    dir: 'desc',
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
            createOrder(id){
                this.$emit('create-order', id);
            },
            getItems(url = 'api/meals/'+this.mealId+'/items') {
                this.tableData.draw++;
                axios.get(url, {params: this.tableData})
                    .then(response => {
                        let data = response.data;
                        if (this.tableData.draw == data.draw) {
                            this.items = data.data.data;
                            this.configPagination(data.data);
                        }
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
            },
            sortBy(key) {
                this.sortKey = key;
                this.sortOrders[key] = this.sortOrders[key] * -1;
                this.tableData.column = this.getIndex(this.columns, 'name', key);
                this.tableData.dir = this.sortOrders[key] === 1 ? 'asc' : 'desc';
                this.getItems();
            },
            getIndex(array, key, value) {
                return array.findIndex(i => i[key] == value)
            },
        },
        watch: {
            mealId: function (mealSummary) {
                this.mealId = mealSummary;
                axios.get('api/meals/'+this.mealId).then(response=>{
                    this.meal = response.data;
                });
                if (this.mealId !== null) {
                    this.getItems()
                }
            }
        }
    };
</script>