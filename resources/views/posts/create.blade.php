@extends('layouts.base')
@section('content')
    <form action="{!! route('posts.store') !!}" method="post">
    	{{ csrf_field() }}
    	{{ method_field('POST') }}
    	<fieldset class="form-group">
    		<label>タイトル</label>
    		<input type="text" name="title" value="" class="form-control">
    	</fieldset>
    	<fieldset class="form-group">
    		<label>本文</label>
    		<textarea name="content" cols="30" rows="10" class="form-control"></textarea>
    	</fieldset>
    	<input type="submit" value="登録" class="btn btn-primary">
    </form>
@endsection