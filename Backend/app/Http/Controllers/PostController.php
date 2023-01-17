<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['allPosts']]);
    }

    // get all posts to display to guests
    public function allPosts()
    {
        $posts = Post::orderBy('id', 'DESC')->get();
        return response()->json(['data' => $posts, 'message' => 'Posts retreived Successfully.']);
    }

    // get posts of the current user
    public function userPosts()
    {
        $posts = Post::where('user_id', auth()->user()->id)->orderBy('id', 'DESC')->get();
        return response()->json(['data' => $posts, 'message' => 'Posts retreived Successfully.']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->user()->id;
        $post = Post::create($validatedData);
        return response()->json(['data' => $post, 'message' => 'Post Created Successfully.']);
    }

    // post details with its comments
    public function show(Request $request)
    {
        $post = Post::with('comments')->where(
            [
                ['id', $request->id],
                ['user_id', auth()->user()->id],
            ]
        )->first();
        if (isset($post->comments)) {
            foreach ($post->comments as $comment) {
                $comment->username = $comment->user->name;
            }
        }
        return response()->json(['data' => $post, 'message' => 'Post details retreived Successfully']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->user()->id;
        DB::table('posts')
            ->where([
                ['id', $request->id],
                ['user_id', auth()->user()->id],
            ])
            ->update([
                'title' => $request->title,
                'content' => $request->content,
                'image' => $request->image,
                'user_id' => auth()->user()->id,

            ]);

        return response()->json(['data' => Post::findOrfail($request->id), 'message' => 'Post updated Successfully ']);
    }

    public function destroy(Request $request)
    {
        $post = Post::where([
            ['id', $request->id],
            ['user_id', auth()->user()->id],
        ])->first();
        if ($post) {
            $post->delete();
            return response()->json(['message' => 'Post deleted Successfully ']);
        } else {
            return response()->json(['message' => 'Operation failed!']);
        }

    }
}