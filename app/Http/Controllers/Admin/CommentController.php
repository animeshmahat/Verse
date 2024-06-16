<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'parent_id' => 'nullable|exists:comments,id',
            'comment' => 'required|string',
        ]);

        // Create and save the comment
        Comments::create([
            'post_id' => $request->post_id,
            'parent_id' => $request->parent_id,
            'user_id' => Auth::id(), // Assuming the user is authenticated
            'comment' => $request->comment,
        ]);

        // Redirect back to the post view with a success message
        return redirect()->back()->with('success', 'Comment successfully added.');
    }
}
