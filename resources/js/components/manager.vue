<template>
    <div class="container-fluid">
    <div class="row">
        <div class="col-sm-2 bg-dark text-white managerLeftMenu">
        	<div class="container">
        		<h2>Manager</h2>
                <router-link to="/manager/managerItemList">Items</router-link>
            	<!-- <router-link to="/manager/editItem">edit</router-link> -->
        	</div>
            
            
        </div>
        <div class="col-sm">
        	<div class="container-fluid mt-5">
        		<router-view></router-view>
        	</div>
        </div>
    </div>
    </div>
</template>
<script>
    export default {
        data: function(){
            return { 
                editedItem: null,
            }
        },
    }
</script>

