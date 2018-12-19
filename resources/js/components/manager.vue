<template>
    <div class="row">
        <div class="col-sm-2 bg-dark text-white managerLeftMenu">
        	<div class="container">
        		<h2>Manager</h2>
            	<router-link to="/manager/managerItemList">Items</router-link>
        	</div>
            
            
        </div>
        <div class="col-sm">
        	<div class="container">
        		<router-view></router-view>
        	</div>
        </div>
    </div>
</template>
