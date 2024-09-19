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
use App\Services\TextRankService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class SiteController extends BaseController
{
    protected $base_route = "site";
    protected $view_path = "site";
    protected $panel = "Verse";
    protected $postService;
    protected $textRankService;
    public function __construct(PostService $postService, TextRankService $textRankService)
    {
        $this->postService = $postService;
        $this->textRankService = $textRankService;
    }
    public function index(Request $request)
    {
        $user = auth()->user();

        $allPosts = Posts::where('status', 1)->orderBy('created_at', 'DESC')->paginate('10');

        $followingPosts = collect();
        if ($user) {
            $followingPosts = Posts::where('status', 1)
                ->whereIn('user_id', $user->followings()->pluck('followed_id'))->paginate('10');
        }
        Paginator::useBootstrap();

        // Sidebar info
        $categoriesWithMostPosts = Category::withCount([
            'posts' => function ($query) {
                $query->where('status', 1);
            }
        ])->orderBy('posts_count', 'DESC')->get();

        $tagsWithMostPosts = Tags::withCount([
            'posts' => function ($query) {
                $query->where('status', 1);
            }
        ])->orderBy('posts_count', 'DESC')->get();

        $popularPosts = Posts::orderBy('views', 'DESC')->take(7)->get();
        $trendingPosts = $this->postService->getTrendingPosts();

        $data = [
            'allPosts' => $allPosts,
            'followingPosts' => $followingPosts,
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
        $categoriesWithMostPosts = Category::withCount([
            'posts' => function ($query) {
                $query->where('status', 1);
            }
        ])->orderBy('posts_count', 'DESC')->get();
        $tagsWithMostPosts = Tags::withCount([
            'posts' => function ($query) {
                $query->where('status', 1);
            }
        ])->orderBy('posts_count', 'DESC')->get();
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
        $post_id = $post->id;

        // Get viewed posts data from the session
        $viewedPosts = session()->get('viewed_posts', []);

        // Check if the post has been viewed in the session
        if (isset($viewedPosts[$post_id])) {
            $viewData = $viewedPosts[$post_id];
            $lastViewed = $viewData['last_viewed'];
            $viewCount = $viewData['count'];

            // Check the time interval and view count
            if ($viewCount < 5 && now()->diffInSeconds($lastViewed) >= 30) {
                // Increment the views count
                $post->increment('views');

                // Update the view data
                $viewedPosts[$post_id]['last_viewed'] = now();
                $viewedPosts[$post_id]['count']++;
            }
        } else {
            // Increment the views count for the first view in the session
            $post->increment('views');

            // Initialize the view data for this post
            $viewedPosts[$post_id] = [
                'last_viewed' => now(),
                'count' => 1,
            ];
        }

        // Store the updated viewed posts data in the session
        session()->put('viewed_posts', $viewedPosts);

        // Store the datetime of the view
        PostView::create([
            'post_id' => $post_id,
            'viewed_at' => now(),
        ]);

        $comments = Comments::where('post_id', $post_id)->get();

        // Sidebar info 
        $categories = Category::get();
        $categoriesWithMostPosts = Category::withCount([
            'posts' => function ($query) {
                $query->where('status', 1);
            }
        ])->orderBy('posts_count', 'DESC')->get();
        $tagsWithMostPosts = Tags::withCount([
            'posts' => function ($query) {
                $query->where('status', 1);
            }
        ])->orderBy('posts_count', 'DESC')->get();
        $popularPosts = Posts::orderBy('views', 'DESC')->take(7)->get();
        $trendingPosts = $this->postService->getTrendingPosts();

        //summary
        $summaries = $this->textRankService->summarizeText($post->title, $post->description);

        // Calculate reading time (words per minute)
        $wordCount = str_word_count(strip_tags($post->description));
        $readingTime = ceil($wordCount / 238); // Assuming 238 words per minute

        $data = [
            'post' => $post,
            'post_id' => $post_id,
            'comments' => $comments,
            'categoriesWithMostPosts' => $categoriesWithMostPosts,
            'tagsWithMostPosts' => $tagsWithMostPosts,
            'popularPosts' => $popularPosts,
            'trendingPosts' => $trendingPosts,
            'paragraph_summary' => $summaries['paragraph'] ?? '',
            'bullet_point_summary' => $summaries['bullet_points'] ?? [],
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
        $categoriesWithMostPosts = Category::withCount([
            'posts' => function ($query) {
                $query->where('status', 1);
            }
        ])->orderBy('posts_count', 'DESC')->get();
        $tagsWithMostPosts = Tags::withCount([
            'posts' => function ($query) {
                $query->where('status', 1);
            }
        ])->orderBy('posts_count', 'DESC')->get();
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

            // Check for existing notification
            $notificationData = [
                'type' => 'follow',
                'data' => json_encode([
                    'message' => Auth::user()->name . " started following you."
                ])
            ];

            $existingNotification = $user->notifications()
                ->where('data', $notificationData['data'])
                ->first();

            if ($existingNotification) {
                // Update the existing notification's timestamp to now
                $existingNotification->touch();
            } else {
                // Create new notification
                $user->notifications()->create($notificationData);
            }

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

    public function followers($id)
    {
        $user = User::findOrFail($id);
        $followers = $user->followers()->get();
        return response()->json($followers);
    }
    public function following($id)
    {
        $user = User::findOrFail($id);
        $following = $user->followings()->get();
        return response()->json($following);
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

        // Create notification
        $post->user->notifications()->create([
            'type' => 'like',
            'data' => json_encode([
                'message' => "{$user->name} liked your post.",
                'post_title' => $post->title // Include the post title here
            ])
        ]);

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
    public function profile($id)
    {
        $user = User::findOrFail($id);
        $data['row'] = $user;
        $data['post'] = Posts::where('status', 1)->where('user_id', $id)->paginate('10');
        $data['followersCount'] = $user->followers()->count();
        $data['followingCount'] = $user->followings()->count();
        Paginator::useBootstrap();

        return view(parent::loadDefaultDataToView($this->view_path . '.profile'), compact('data'));
    }
}
