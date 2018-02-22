@extends('layouts.base')
@section('content')
<div class="list-group">
    @if (count($posts) > 0)
		@foreach($posts as $post)
		<div class="list-group-item">
			<a href="{{ url('posts/'.$post->id) }}">
				<h2>{{$post->title}}</h2>
	    		<p>{{$post->content}}</p>
	    	</a>
	    	<form action="{{ url('posts/'.$post->id) }}" method="post">
				{{ csrf_field() }}
    			{{ method_field('DELETE') }}
				<a class="btn btn-primary" href="{{ url('posts/'.$post->id) }}/edit">編集</a>
				<input class="btn btn-danger" type="submit" value="削除">
			</form>
		</div>
		@endforeach
	@endif
</div>
@endsection

