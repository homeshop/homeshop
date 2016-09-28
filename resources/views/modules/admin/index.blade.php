@extends('admin::layouts.master')

@section('content')
	
	<h1>Hello World</h1>
	
	<p>
		My Test This view is loaded from module: {!! config('admin.name') !!}
	</p>

@stop