<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Posts;

class CheckPostOwner
{
    public function handle($request, Closure $next)
    {
        $postId = $request->route('id');
        $post = Posts::findOrFail($postId);

        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'superadmin') {
            return redirect()->route('admin.post.index')->with('error', 'You are not authorized to access this page.');
        }

        return $next($request);
    }
}
