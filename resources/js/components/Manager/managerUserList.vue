<template>
	<div >
		<h1>Users</h1>
        <div class="container-fluid">
        	<div class="alert alert-success" v-if="alertSucces.show">
	            <button type="button" class="close-btn" v-on:click="alertSucces.show=false">&times;</button>
	            <strong  alert.message>{{ alertSucces.message }}</strong>
	        </div>
        	<div class="row">
				<div class="col-sm-9">
					<input class="form-control col-sm-2" type="text" v-model="tableData.search" placeholder="Search Table" @input="getUsers()">

					<div class="control">
						<select class="custom-select col-sm-1" v-model="tableData.length" @change="getUsers()">
			                <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
			            </select>
			        </div>
			    </div>
			    <div class="col-sm-3" style="text-align: right;">
			    	<a class="btn btn-sm btn-primary font-weight-bold" title="Edit" v-on:click.prevent="createUser()"><i class="fas fa-plus is-20"></i> Create User</a>
			    </div>
		    </div>
			<datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" 
			@sort="sortBy">
				<tbody>
					<tr v-for="user in users" :key="user.id">
						<td>{{user.name}}</td>
						<td>{{user.username}}</td>
						<td>{{user.email}}</td>
						<td>{{user.type}}</td>
						<td v-if="user.blocked">Yes</td>
						<td v-else >No</td>
						<td v-if="user.deleted_at == null">No</td>
						<td v-else >Yes</td>
						<td><img :src="'/imgProfiles/' + user.photo_url" width="40" height="40" ></td>
						<td>
							<a class="btn btn-sm btn-primary" title="Edit" v-on:click.prevent="editUser(user)"><i class="fas fa-edit is-20"></i></a>
							<a class="btn btn-sm btn-danger" :class="{'disabled':user.deleted_at!=null}" title="Remove" v-on:click.prevent="deleteUser(user)"><i class="fas fa-trash-alt is-20"></i></a>
						</td>
					</tr>
				</tbody>
			</datatable>
			<pagination :pagination="pagination" @prev="getUsers(pagination.prevPageUrl)"
				@next="getUsers(pagination.nextPageUrl)">
			</pagination>
		</div>
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
		created() {
	        this.getUsers();
	    },
		data(){
			let sortOrders = {};
	        let columns = [
	            {width: '20%', label: 'Name', name: 'name', order:true },
	            {width: '14%', label: 'Username', name: 'username', order:true},
	            {width: '14%', label: 'E-mail', name: 'email', order:true},
	            {width: '10%', label: 'Type', name: 'type', order:false},
	            {width: '10%', label: 'Blocked', name: 'blocked', order: true},
	            {width: '7%', label: 'Deleted', name: 'deleted_at', order: false},
	            {width: '8%', label: 'Image', name: 'img', order:false},
	            {width: '14%', label: 'Actions', name: 'actions', order:false}
	        ];
	        columns.forEach((column) => {
	           sortOrders[column.name] = -1;
	        });
			return{
				alertSucces:{
					show:false,
					Message:'',
				},
				users: [],
	            columns: columns,
	            sortKey: 'name',
	            sortOrders: sortOrders,
	            perPage: ['5','10', '20', '30'],
	            tableData: {
	                draw: 0,
	                length: 5,
	                search: '',
	                column: 0,
	                dir: 'desc'
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
			getUsers(url = 'api/manager/usersDataTable') {
	            this.tableData.draw++;
	            axios.get(url, {params: this.tableData})
	                .then(response => {
	                    let data = response.data;
	                    if (this.tableData.draw == data.draw) {
	                        this.users = data.data.data;
	                        this.configPagination(data.data);
	                    }
	                })
	                .catch(errors => {
	                    console.log(errors);
	                });
	        },
	        statistcsUser(user){
	        	this.$parent.selectedUser = user;
	        	this.$router.push('/manager/userStatistics');
	        },
	        editUser(user){
	        	this.$parent.selectedUser = user;
	        	this.$router.push('/manager/editUser');
	        },
	        deleteUser(user){
	        	if (user.username == this.$store.state.user.username) {
	        		alert('You cannot delete your self');
	        		return;
	        	}
	        	axios.delete('api/manager/managerUserList/'+user.id)
                    .then(response => {
                    	this.buildSuccessMessage("Item deleted");
                        this.getUsers();
                });
	        },
	        createUser(){
	        	this.$router.push('/manager/createUser');
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
	            this.getUsers();
	        },

	        getIndex(array, key, value) {
	            return array.findIndex(i => i[key] == value)
	        },
	        buildSuccessMessage(message){
        		this.alertSucces.show = true;
				this.alertSucces.message = message;
				setTimeout(() => {
                        this.alertSucces.show = false;
                }, 2000);
        	},
		}
	};
</script>