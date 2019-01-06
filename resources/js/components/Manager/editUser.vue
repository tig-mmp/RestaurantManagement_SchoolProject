<template>
	<div class="container-fluid">
		<div>
			<h1>Edit User</h1>
			<div class="alert alert-danger" v-if="alertFail.show">
	             
	            <button type="button" class="close-btn" v-on:click="alertFail.show=false">&times;</button>
	            <ul v-for="error in alertFail.message">
	            	<li>
	            		<strong  alert.message>{{ error }}</strong>
	            	</li>
	            </ul>
	        </div>
			<div class="row">
				<div class="col-sm-8">
			        <div class="form-group">
			            <label for="inputName">Name</label>
			            <input
			                    type="text" class="form-control" v-model="editedUser.name"
			                    name="name" id="inputName"
			                    placeholder="name"/>
			        </div>
			        <div class="form-group">
			            <label for="inputUsername">Username</label>
			            <input
			                    type="text" class="form-control" v-model="editedUser.username"
			                    name="name" id="inputUsername"
			                    placeholder="username"/>
			        </div>
			        <div class="form-group">
			            <label for="inputEmail">Email</label>
			            <input
			                    type="text" class="form-control" v-model="editedUser.email"
			                    name="name" id="inputEmail"
			                    placeholder="email"/>
			        </div>
			        <div class="form-group">
			            <label for="inputType">Type</label>
			            <select
			                    type="text" class="form-control" v-model="editedUser.type"
			                    name="name" id="inputType"
			                    placeholder="type">
			                    <option>manager</option>
			                    <option>waiter</option>
			                    <option>cook</option>
			                    <option>cashier</option>
			            </select>
			        </div>
		        </div>
		        <div class="col-sm">
			        <div class="form-group">
			        	<img class="rounded" v-if="avatar == ''" :src="'/imgProfiles/' + editedUser.photo_url" width="300" height="300" >
			        	<img class="rounded" v-else :src="avatar" width="300" height="300" >
			            <label>
			                <input type="file" id="file" ref="file" v-on:change="handleFileUpload"/>
			            </label>
			        </div>
		        </div>
	        </div>


	        <div class="row">
	        	<div class="form-group col-sm">
		            <label for="inputLast_shift_start">Last shift start</label>
		            <input
			                type="text" class="form-control" v-model="editedUser.last_shift_start" disabled
			                name="name" id="inputLast_shift_start"
			                placeholder="last_shift_start"/>
		        </div>
		        <div class="form-group col-sm">
		            <label for="inputDescription">Last shift end</label>
		            <input
			                type="text" class="form-control" v-model="editedUser.last_shift_end" disabled
			                name="name" id="inputLast_shift_end"
			                placeholder="last_shift_end"/>
		        </div>
	        </div>deleted_at


	        <div class="row">
	        	<div class="form-group col-sm">
		            <label for="inputEmail_verified_at">Email verified at</label>
		            <input
			                type="text" class="form-control" v-model="editedUser.email_verified_at" disabled
			                name="name" id="inputEmail_verified_at" 
			                placeholder="email_verified_at"/>
		        </div>
		        <div class="form-group col-sm" >
		            <label for="inputBlocked">Blocked</label>
		            <select
			                    type="text" class="form-control" v-model="editedUser.blocked"
			                    name="name" id="inputType"
			                    placeholder="type"
			                    v-if="editedUser.username==this.$store.state.user.username"
			                    disabled>
			                    <option value="1">yes</option>
			                    <option value="0">no</option>
			        </select>
			        <select
			                    type="text" class="form-control" v-model="editedUser.blocked"
			                    name="name" id="inputType"
			                    placeholder="type"
			                    v-else>
			                    <option value="1">yes</option>
			                    <option value="0">no</option>
			        </select>
		        </div>
		        <div class="form-group col-sm">
		            <label for="inputShift_active">Shift active</label>
		            <input
			                type="text" class="form-control" v-if="editedUser.shift_active" value="Yes" disabled
			                name="name" id="inputShift_active"
			                placeholder="shift_active"/>
			        <input
			                type="text" class="form-control" v-else value="No" disabled
			                name="name" id="inputShift_active"
			                placeholder="shift_active"/>
		        </div>
		        <div class="form-group col-sm">
		            <label for="inputDeleted_at">Deleted</label>
		            <input
			                type="text" class="form-control" v-if="editedUser.deleted_at == null" value="No" disabled
			                name="name" id="inputDeleted_at"
			                placeholder="deleted_at"/>
			        <input
			                type="text" class="form-control" v-else v-model="editedUser.deleted_at" disabled
			                name="name" id="inputDeleted_at"
			                placeholder="deleted_at"/>
		        </div>
	        </div>
	       
	        <div class="form-group">
	            <a class="btn btn-primary" v-on:click.prevent="save()">Save</a>
	            <a class="btn btn-light" v-on:click.prevent="cancelEdit()">Cancel</a>
	        </div>
	    </div>
	</div>
</template>
<script>
    export default {
        data: function(){
            return { 
                editedUser: '',
                file: '',
                avatar: '',
                alertFail:{
					show:false,
					isFail:false,
					message:'',
				},
            }
        },
        mounted(){
        	this.editedUser = this.$parent.selectedUser;
        },
        methods:{
        	save: function(){
        		if (this.file !== ''){
                    let formData = new FormData();
                    formData.append('file', this.file);
                    axios.post( 'api/users/' + this.editedUser.id + '/uploadFile',
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }
                    ).then(response=>{
                    	
                    })
                    .catch(error =>{
                		this.buildErrorMessage(error);
        				return;
                	});
                }
                axios.put('/api/manager/editUser/'+this.editedUser.id, this.editedUser)
                .then(response=>{
                    this.$router.push('/manager/managerUserList');
                }).catch(error =>{
                		this.buildErrorMessage(error);
        				return;
                });
        	},
        	cancelEdit:function(){
        		this.$router.push('/manager/managerUserList');
        	},
        	handleFileUpload(e){
                this.file = e.target.files[0];
                let image = e.target.files[0];
                let reader = new FileReader();
                reader.readAsDataURL(image);
                reader.onload = e => {
                	this.avatar = e.target.result;
                }
            },
            buildErrorMessage(error){
        		this.alertFail.show = true;
				this.alertFail.isFail = true;
				this.alertFail.isSuccess = false;
				this.alertFail.message = [];
        		for(var prop in error.response.data.errors){
        			this.alertFail.message.push(error.response.data.errors[prop].join('and'));
        		}
        	},
        }
    }
</script>