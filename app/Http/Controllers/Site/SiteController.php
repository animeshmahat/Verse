<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Category;
use App\Models\Comments;
use App\Models\Likes;
use App\Models\Posts;
use App\Models\PostView;
use App\Models\Tags;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\PostService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class SiteController extends BaseController
{
    protected $base_route = "site";
    protected $view_path  = "site";
    protected $panel = "Verse";
    protected $postService;
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }
    public function index(Request $request)
    {
        $post = Posts::where('status', 1)->get();

        // Sidebar info 
        $categories = Category::get();
        $categoriesWithMostPosts = Category::withCount(['posts' => function ($query) {
            $query->where('status', 1);
        }])->orderBy('posts_count', 'DESC')->get();
        $tagsWithMostPosts = Tags::withCount(['posts' => function ($query) {
            $query->where('status', 1);
        }])->orderBy('posts_count', 'DESC')->get();
        $popularPosts = Posts::orderBy('views', 'DESC')->take(7)->get();
        $trendingPosts = $this->postService->getTrendingPosts();

        $data = [
            'post' => $post,
            'categoriesWithMostPosts' => $categoriesWithMostPosts,
            'tagsWithMostPosts' => $tagsWithMostPosts,
            'popularPosts' => $popularPosts,
            'trendingPosts' => $trendingPosts,
        ];

        return view(parent::loadDefaultDataToView($this->view_path . '.index'), compact('data'));
    }
    public function search(Request $request)
    {
        $search = $request->input('search');
        $results = Posts::where('title', 'like', "%$search%")
            ->orWhereHas('category', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->orWhereHas('tags', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->where('status', 1)
            ->orderBy('views', 'DESC')
            ->paginate('10');
        Paginator::useBootstrap();

        // Sidebar info 
        $categories = Category::get();
        $categoriesWithMostPosts = Category::withCount(['posts' => function ($query) {
            $query->where('status', 1);
        }])->orderBy('posts_count', 'DESC')->get();
        $tagsWithMostPosts = Tags::withCount(['posts' => function ($query) {
            $query->where('status', 1);
        }])->orderBy('posts_count', 'DESC')->get();
        $popularPosts = Posts::orderBy('views', 'DESC')->take(7)->get();
        $trendingPosts = $this->postService->getTrendingPosts();

        $data = [
            'categoriesWithMostPosts' => $categoriesWithMostPosts,
            'tagsWithMostPosts' => $tagsWithMostPosts,
            'popularPosts' => $popularPosts,
            'trendingPosts' => $trendingPosts,
        ];
        return view(parent::loadDefaultDataToView($this->view_path . '.search'), compact('data'), ['results' => $results, 'search' => $search,]);
    }
    public function autocomplete(Request $request)
    {
        $search = $request->input('search');
        $suggestions = Posts::where('title', 'like', "%$search%")
            ->where('status', 1)
            ->orderByRaw("CASE 
                            WHEN title LIKE ? THEN 1
                            ELSE 2
                          END, title", ["$search%"])
            ->limit(5) // Limit the number of suggestions to 5
            ->get(['title', 'slug']); // Retrieve both title and slug

        return response()->json($suggestions);
    }
    public function single_post(Request $request, $slug)
    {
        $post = Posts::where('slug', $slug)->firstOrFail();

        // Check if the user has viewed this post in this session
        $viewedPosts = session()->get('viewed_posts', []);
        if (!in_array($post->id, $viewedPosts)) {
            // Increment the views count if not viewed in this session
            $post->increment('views');
            // Store the post ID in the session to mark it as viewed
            session()->push('viewed_posts', $post->id);
        }

        // Store the datetime of the view
        PostView::create([
            'post_id' => $post->id,
            'viewed_at' => now(),
        ]);

        $post_id = $post->id;
        $comments = Comments::where('post_id', $post_id)->get();

        // Sidebar info 
        $categories = Category::get();
        $categoriesWithMostPosts = Category::withCount(['posts' => function ($query) {
            $query->where('status', 1);
        }])->orderBy('posts_count', 'DESC')->get();
        $tagsWithMostPosts = Tags::withCount(['posts' => function ($query) {
            $query->where('status', 1);
        }])->orderBy('posts_count', 'DESC')->get();
        $popularPosts = Posts::orderBy('views', 'DESC')->take(7)->get();
        $trendingPosts = $this->postService->getTrendingPosts();

        // Calculate reading time (words per minute)
        $wordCount = str_word_count(strip_tags($post->description));
        $readingTime = ceil($wordCount / 238); // Assuming 200 words per minute

        $data = [
            'post' => $post,
            'post_id' => $post_id,
            'comments' => $comments,
            'categoriesWithMostPosts' => $categoriesWithMostPosts,
            'tagsWithMostPosts' => $tagsWithMostPosts,
            'popularPosts' => $popularPosts,
            'trendingPosts' => $trendingPosts,
            'readingTime' => $readingTime,
        ];

        return view(parent::loadDefaultDataToView($this->view_path . '.single-post'), compact('data'));
    }
    public function category(Request $request, $name)
    {
        $category = Category::where('name', $name)->firstOrFail();
        $category_id = $category->id;
        $post = Posts::where('category_id', $category_id)->where('status', 1)->paginate('10');
        Paginator::useBootstrap();

        // Sidebar info 
        $categories = Category::get();
        $categoriesWithMostPosts = Category::withCount(['posts' => function ($query) {
            $query->where('status', 1);
        }])->orderBy('posts_count', 'DESC')->get();
        $tagsWithMostPosts = Tags::withCount(['posts' => function ($query) {
            $query->where('status', 1);
        }])->orderBy('posts_count', 'DESC')->get();
        $popularPosts = Posts::orderBy('views', 'DESC')->take(7)->get();
        $trendingPosts = $this->postService->getTrendingPosts();

        $data = [
            'category' => $category,
            'category_id' => $category_id,
            'post' => $post,
            'categoriesWithMostPosts' => $categoriesWithMostPosts,
            'tagsWithMostPosts' => $tagsWithMostPosts,
            'popularPosts' => $popularPosts,
            'trendingPosts' => $trendingPosts,
        ];

        return view(parent::loadDefaultDataToView($this->view_path . '.category'), compact('data'));
    }
    public function follow($userId)
    {
        $user = User::find($userId);

        if ($user && !Auth::user()->isFollowing($userId)) {
            Auth::user()->follow($userId);
            return response()->json(['message' => 'Successfully followed the user.']);
        }

        return response()->json(['message' => 'Failed to follow the user.'], 400);
    }

    public function unfollow($userId)
    {
        $user = User::find($userId);

        if ($user && Auth::user()->isFollowing($userId)) {
            Auth::user()->unfollow($userId);
            return response()->json(['message' => 'Successfully unfollowed the user.']);
        }

        return response()->json(['message' => 'Failed to unfollow the user.'], 400);
    }
    public function likePost($id)
    {
        $post = Posts::findOrFail($id);
        $user = Auth::user();

        if ($post->likes()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Already liked'], 400);
        }

        $post->likes()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $post->increment('likes_count');

        return response()->json(['message' => 'Post liked'], 200);
    }

    public function unlikePost($id)
    {
        $post = Posts::findOrFail($id);
        $user = Auth::user();

        $like = $post->likes()->where('user_id', $user->id)->first();

        if (!$like) {
            return response()->json(['message' => 'Not liked yet'], 400);
        }

        $like->delete();

        $post->decrement('likes_count');

        return response()->json(['message' => 'Post unliked'], 200);
    }
}
