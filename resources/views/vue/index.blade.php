@extends('master')

@section('title', 'Restaurant Management')

@section('content')

<router-link to="/menu">Menu</router-link> -
<router-link to="/login"  v-show="!this.$store.state.user">Login</router-link> -
<router-link to="/logout" v-show="this.$store.state.user">Logout</router-link>
<br>
<em>User: @{{this.$store.state.user != null ? this.$store.state.user.name : " - None - " }}</em>

<router-view></router-view>

@endsection
@section('pagescript')
<script src="js/vue.js"></script>
@stop  
