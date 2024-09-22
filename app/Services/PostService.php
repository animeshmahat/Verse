<?php

namespace App\Services;

use App\Models\Posts;
use Carbon\Carbon;

class PostService
{
    public function getTrendingPosts($limit = 7)
    {
        return Posts::where('status', 1)->whereHas('user', function ($query) {
            $query->where('status', 1); // Ensure user status is 1
        })->select('posts.id', 'posts.title', 'posts.category_id', 'posts.description', 'posts.category_id', 'posts.thumbnail', 'posts.slug', 'posts.user_id', 'posts.views', 'posts.created_at', 'posts.updated_at')
            ->selectRaw('(posts.views + (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id)) as popularity_score')
            ->leftJoin('post_views', function ($join) {
                $join->on('posts.id', '=', 'post_views.post_id')
                    ->where('post_views.viewed_at', '>=', Carbon::now()->subHours(48));
            })
            ->groupBy('posts.id', 'posts.title', 'posts.category_id', 'posts.description', 'posts.category_id', 'posts.thumbnail', 'posts.slug', 'posts.user_id', 'posts.views', 'posts.created_at', 'posts.updated_at') // Group by all selected columns
            ->orderByDesc('popularity_score')
            ->take($limit)
            ->get();
    }
}
