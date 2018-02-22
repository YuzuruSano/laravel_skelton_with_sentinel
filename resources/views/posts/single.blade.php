@extends('layouts.base')
@section('content')
	<!-- Display Validation Errors -->
    {{-- @include('commons.errors') --}}
    <h2>{{$post->title}}</h2>
    <p>{{$post->content}}</p>
@endsection