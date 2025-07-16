<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'comment_text' => 'required|string',
        ]);

        $comment = Comment::create([
            'user_id' => auth()->user()->id,
            'job_id' => $request->job_id,
            'comment_text' => $request->comment_text,
        ]);

        return response()->json($comment->load('user'), 201);
    }

    public function destroy(Comment $comment)
    {
        $user = auth()->user();

        if ($user->id === $comment->user_id || in_array($user->role, ['trainer', 'admin'])) {
            $comment->delete();
            return response()->json(null, 204);
        }

        return response()->json(['message' => 'Unauthorized to delete this comment.'], 403);
    }
}
