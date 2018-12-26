<template>
	<div class="container-fluid">
		<div>
			<h1>Create Item</h1>
			<div class="alert" :class="{'alert-success':alert.isSuccess, 'alert-danger':alert.isFail }" v-if="alert.show">
	             
	            <button type="button" class="close-btn" v-on:click="alert.show=false">&times;</button>
	            <ul v-for="error in alert.message">
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
			                    type="text" class="form-control" v-model="item.name"
			                    name="name" id="inputName"
			                    placeholder="name"/>
			        </div>
			        <div class="form-group">
			            <label for="inputType">Type</label>
			            <select
			                    type="text" class="form-control" v-model="item.type"
			                    name="name" id="inputType"
			                    placeholder="type">
			                    <option>dish</option>
			                    <option>drink</option>
			            </select>
			        </div>
			        <div class="form-group">
			            <label for="inputPrice">Price</label>
			            <input
			                    type="text" class="form-control" v-model="item.price"
			                    name="name" id="inputPrice"
			                    placeholder="price"/>
			        </div>
		        </div>
		        <div class="col-sm">
			        <div class="form-group">
			        	<img :src="avatar" width="200" height="200" >
			            <label>
			                <input type="file" id="file" ref="file" @change="handleFileUpload"/>
			            </label>
			        </div>
		        </div>
	        </div>
	        <div class="form-group">
	            <label for="inputDescription">Description</label>
	            <textarea
	                    type="text" class="form-control" v-model="item.description"
	                    name="name" id="inputDescription"
	                    placeholder="description"/>
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
            	alert:{
					show:false,
					successMessage:'',
					isSuccess:true,
					isFail:false,
					message:[],
				},
                item: {
                    name:"",
                    type:"",
                    description:"",
                    price:"",
                    file: '',
                },
                avatar: null
            }
        },
        mounted(){
        },
        methods:{
        	save: function(){
        		let formData = new FormData();
	            formData.append('file', this.item.file);
	            formData.append('name', this.item.name);
	            formData.append('type', this.item.type);
	            formData.append('price', this.item.price);
	            formData.append('description', this.item.description);
        		axios.post('/api/manager/createItem', formData,
	                    {
	                    	headers: {
	                    		'Content-Type': 'multipart/form-data'
	                    	}
	                    })
        		.then(response=>{
					this.$router.push('/manager/managerItemList');
                }).catch(error =>{
        				this.buildError(error);
        				return;
                });
        	},
        	buildError(error){
        		this.alert.show = true;
				this.alert.isFail = true;
				this.alert.isSuccess = false;
				this.alert.message = [];
        		for(var prop in error.response.data.errors){
        			this.alert.message.push(error.response.data.errors[prop].join('and'));
        		}
        	},
        	cancelEdit:function(){
        		this.$router.push('/manager/managerItemList');
        	},
        	handleFileUpload: function(e){
        		this.item.file = e.target.files[0];
                let image = e.target.files[0];
                let reader = new FileReader();
                reader.readAsDataURL(image);
                reader.onload = e => {
                	this.avatar = e.target.result;
                }
            }
        }
    }
</script>