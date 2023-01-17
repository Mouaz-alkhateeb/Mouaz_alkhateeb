<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function store(CommentRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->user()->id;
        $comment = Comment::create($validatedData);
        return response()->json(['data' => $comment, 'message' => 'Comment added Successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $comment = Comment::where([
            ['id', $request->id],
            ['user_id', auth()->user()->id],
        ])->first();
        if ($comment) {
            $comment->delete();
            return response()->json(['message' => 'Comment deleted Successfully.']);
        } else {
            return response()->json(['message' => 'Operation failed!']);
        }
    }
}