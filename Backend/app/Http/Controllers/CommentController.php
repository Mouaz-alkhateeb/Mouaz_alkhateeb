<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Services\PostService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function store(CommentRequest $request)
    {
        $comment = PostService::addComment($request);
        return response()->json(['data' => $comment, 'message' => 'Comment added Successfully.']);
    }

    public function destroy(Request $request)
    {
        $isDeleted = PostService::deleteComment($request->id);
        if ($isDeleted) {
            return response()->json(['message' => 'Comment deleted Successfully ']);
        } else {
            return response()->json(['message' => 'Operation failed!']);
        }
    }

}