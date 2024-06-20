<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comments;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'parent_id' => 'nullable|exists:comments,id',
            'comment' => 'required|string',
        ]);

        $comment = Comments::create([
            'post_id' => $request->post_id,
            'parent_id' => $request->parent_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        $post = Posts::findOrFail($request->post_id);
        $post->increment('comments_count');

        // Create notification
        $post->user->notifications()->create([
            'type' => 'comment',
            'data' => json_encode([
                'message' => Auth::user()->name . " commented on your post ({$post->title})."
            ])
        ]);

        return redirect()->back()->with('success', 'Comment successfully added.');
    }
    public function destroy($id)
    {
        $comment = Comments::findOrFail($id);
        $post = Posts::findOrFail($comment->post_id);

        // Check if the authenticated user is the owner of the comment
        if ($comment->user_id != Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to delete this comment.');
        }

        // Delete the comment
        $comment->delete();

        // Decrement the comments_count field in the associated post
        $post->decrement('comments_count');

        return redirect()->back()->with('success', 'Comment successfully deleted.');
    }
}
