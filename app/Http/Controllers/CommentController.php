<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Order;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Order $order)
    {
        $comments = $order->comments()->with('user')->latest()->get();
        return response()->json(['data' => $comments]);
    }

    public function store(Request $request, Order $order)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        $comment = $order->comments()->create([
            'user_id' => auth()->user()->id,
            'comment_text' => $request->text,
        ]);

        return response()->json(['data' => $comment->load('user')], 201);
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
