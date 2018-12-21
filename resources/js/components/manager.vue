<template>
    <div class="container-fluid">
    <div class="row">
        <div class="col-sm-2 bg-dark text-white managerLeftMenu">
        		<h2>Manager</h2>
                <div class="list-group">
                        <router-link class="list-group-item list-group-item-action list-group-item-dark" to="/manager/managerItemList">Items</router-link>
                        <router-link class="list-group-item list-group-item-action list-group-item-dark" to="/manager/managerTableList">Tables</router-link>
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

