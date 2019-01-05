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
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/2.3.5/jspdf.plugin.autotable.min.js"></script>
@stop  
