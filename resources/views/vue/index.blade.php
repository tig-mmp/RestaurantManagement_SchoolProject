@extends('master')

@section('title', 'Restaurant Management')
@section('menu')
	<navbar></navbar>
@endsection
@section('content')	

	<router-view></router-view>

@endsection
@section('pagescript')
	<script src="js/vue.js"></script>
@stop  
