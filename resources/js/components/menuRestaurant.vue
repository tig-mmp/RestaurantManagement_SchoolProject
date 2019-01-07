<template>
    <div class="container col-md-8">
        <div class="text-center text-danger">
            <h1>Menu Restaurant</h1>
        </div>
        <div class="control">
            <select class="custom-select col-sm-1" v-model="tableData.length" @change="getItems()">
                <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
            </select>
        </div>
        <pagination :pagination="pagination" @prev="getItems(pagination.prevPageUrl)"
            @next="getItems(pagination.nextPageUrl)">
        </pagination>
        <div class="row justify-content-center mt-5" v-for="item in items">

                <div class="col-sm-2">
                    <img :src="'/imgItems/' + item.photo_url" class="rounded-circle border border-danger" width="100" height="100" >
                </div>
                <div class="col-sm-8">
                    <div class="col-sm font-weight-bold">
                        {{item.name}}
                    </div>
                    <div class="col-sm text-secondary">
                        {{item.description}}
                    </div>
                </div>
                <div class="col-sm-2 text-danger">
                  <h2>{{item.price}}€</h2>
                </div>
        </div>
    </div>
</template>

<script type="text/javascript">
    import Pagination from './Pagination.vue';
    
    export default {
        components:{
            pagination: Pagination
        },
        data: function(){
            return { 
                items: [],
                tableData: {
                    draw: 0,
                    length: 5
                },
                perPage: ['5','10', '20', '30'],
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
        methods: {
            getItems(url = 'api/items/paginate') {
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
          
        },
        mounted() {
            this.getItems();
        }      
    }
</script>