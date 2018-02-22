<?php
namespace App\Http\Controllers;

use App\Posts;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostsController extends Controller
{
    public function __construct(){
        $this->middleware('auth')->except(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Response $response)
    {
        $posts = Posts::latest()->get();
        return view('posts.index',['posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post = new Posts();
        $isSuccess = $post->create($request->all());
        if($isSuccess) {
            $attributes = $isSuccess->getAttributes();
            return redirect('posts/'.$attributes['id'])->with('success', '登録に成功しました');
        }

        return redirect('posts')->with('failed', '登録に失敗しました');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Posts  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Posts $post)
    {
        return view('posts.single')->with('post',$post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Posts  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Posts $post)
    {
        return view('posts.edit',['post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Posts  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Posts $post)
    {
        $isSuccess = $post->update($request->all());

        if($isSuccess) {
            return redirect('posts/'.$post->id)->with('success', '更新しました');
        }

        return redirect('posts')->with('failed', '更新できませんでした');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Posts  $posts
     * @return \Illuminate\Http\Response
     */
    public function destroy(Posts $post)
    {
        $post->delete();
        return redirect('posts')->with('success', '削除しました');
    }
}
