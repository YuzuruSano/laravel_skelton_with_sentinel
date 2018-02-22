@extends('layouts.base')
@section('content')
    <form action="{{ url('posts/'.$post->id) }}" method="post">
    	{{ csrf_field() }}
    	{{ method_field('PUT') }}
    	<fieldset class="form-group">
    		<label>タイトル</label>
    		<input type="text" name="title" value="{{ $post->title }}" class="form-control">
    	</fieldset>
    	<fieldset class="form-group">
    		<label>本文</label>
    		<textarea name="content" cols="30" rows="10" class="form-control">{{ $post->content }}</textarea>
    	</fieldset>
    	<input type="submit" value="更新" class="btn btn-primary">
    </form>
@endsection