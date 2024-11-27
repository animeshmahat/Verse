<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Category;
use App\Models\Comments;
use GuzzleHttp\Client;
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

        $allPosts = Posts::where('status', 1)
            ->whereHas('user', function ($query) {
                $query->where('status', 1); // Ensure user status is 1
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(5);

        $followingPosts = collect();
        if ($user) {
            $followingPosts = Posts::where('status', 1)->whereHas('user', function ($query) {
                $query->where('status', 1); // Ensure user status is 1
            })->whereIn('user_id', $user->followings()->pluck('followed_id'))->paginate('10');
        }
        Paginator::useBootstrap();

        // Sidebar info
        $categories = Category::get();

        $popularPosts = Posts::orderBy('views', 'DESC')->whereHas('user', function ($query) {
            $query->where('status', 1); // Ensure user status is 1
        })->take(7)->get();
        $trendingPosts = $this->postService->getTrendingPosts();

        $data = [
            'allPosts' => $allPosts,
            'followingPosts' => $followingPosts,
            'categories' => $categories,
            'popularPosts' => $popularPosts,
            'trendingPosts' => $trendingPosts,
        ];

        return view(parent::loadDefaultDataToView($this->view_path . '.index'), compact('data'));
    }
    public function search(Request $request)
    {
        $search = $request->input('search');

        $results = Posts::where('status', 1) // Ensure post status is 1
            ->whereHas('user', function ($query) {
                $query->where('status', 1); // Ensure user status is 1
            })
            ->where(function ($query) use ($search) {
                // Apply the search conditions within a nested query
                $query->where('title', 'like', "%$search%")
                    ->orWhereHas('category', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                })
                    ->orWhereHas('tags', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                });
            })
            ->orderBy('views', 'DESC')
            ->paginate(10);

        Paginator::useBootstrap();

        // Sidebar info
        $categoriesWithMostPosts = Category::withCount([
            'posts' => function ($query) {
                $query->where('status', 1); // Only count posts with status 1
            }
        ])->orderBy('posts_count', 'DESC')->get();

        $tagsWithMostPosts = Tags::withCount([
            'posts' => function ($query) {
                $query->where('status', 1); // Only count posts with status 1
            }
        ])->orderBy('posts_count', 'DESC')->get();

        $popularPosts = Posts::where('status', 1) // Ensure popular posts have status 1
            ->orderBy('views', 'DESC')
            ->take(7)
            ->get();

        $trendingPosts = $this->postService->getTrendingPosts();

        $data = [
            'categories' => $categories,
            'popularPosts' => $popularPosts,
            'trendingPosts' => $trendingPosts,
        ];

        return view(parent::loadDefaultDataToView($this->view_path . '.search'), compact('data'), ['results' => $results, 'search' => $search]);
    }
    public function autocomplete(Request $request)
    {
        $search = $request->input('search');
        $suggestions = Posts::where('title', 'like', "%$search%")
            ->where('status', 1)->whereHas('user', function ($query) {
                $query->where('status', 1); // Ensure user status is 1
            })
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

        // Get viewed posts and summaries data from the session
        $viewedPosts = session()->get('viewed_posts', []);
        $summarizedPosts = session()->get('summarized_posts', []);
        $cachedSentiments = session()->get('sentiment_data', []);

        // Check if the post has been viewed in the session
        if (isset($viewedPosts[$post_id])) {
            $viewData = $viewedPosts[$post_id];
            $lastViewed = $viewData['last_viewed'];
            $viewCount = $viewData['count'];

            // Check the time interval and view count
            if ($viewCount < 5 && now()->diffInSeconds($lastViewed) >= 30) {
                $post->increment('views');
                $viewedPosts[$post_id]['last_viewed'] = now();
                $viewedPosts[$post_id]['count']++;
            }
        } else {
            $post->increment('views');
            $viewedPosts[$post_id] = [
                'last_viewed' => now(),
                'count' => 1,
            ];
        }
        session()->put('viewed_posts', $viewedPosts);

        // Log the view in the database
        PostView::create([
            'post_id' => $post_id,
            'viewed_at' => now(),
        ]);

        $comments = Comments::where('post_id', $post_id)->get();

        // Sentiment Analysis
        $positiveCount = 0;
        $negativeCount = 0;
        $neutralCount = 0;
        $client = new Client();

        foreach ($comments as $comment) {
            $commentId = $comment->id;

            // Check if sentiment for this comment is cached
            if (isset($cachedSentiments[$post_id][$commentId])) {
                $sentiment = $cachedSentiments[$post_id][$commentId];
            } else {
                try {
                    // Send the comment text to the Flask API
                    $response = $client->post('http://127.0.0.1:5000/predict', [
                        'json' => ['text' => $comment->comment]
                    ]);
                    $result = json_decode($response->getBody(), true);
                    $sentiment = $result['sentiment'] ?? 'unknown'; // 'positive', 'negative', or 'neutral'

                    // Cache the sentiment result in the session
                    $cachedSentiments[$post_id][$commentId] = $sentiment;
                } catch (\Exception $e) {
                    $sentiment = 'unknown';
                }
            }

            // Count sentiments
            if ($sentiment === 'positive') {
                $positiveCount++;
            } elseif ($sentiment === 'negative') {
                $negativeCount++;
            } elseif ($sentiment === 'neutral') {
                $neutralCount++;
            }
        }

        session()->put('sentiment_data', $cachedSentiments);

        // Sidebar and other existing functionality
        $categories = Category::get();
        $popularPosts = Posts::orderBy('views', 'DESC')->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->take(7)->get();
        $trendingPosts = $this->postService->getTrendingPosts();

        // Summarization
        $summaries = [];
        if (isset($summarizedPosts[$post_id])) {
            $summaries = $summarizedPosts[$post_id];
        } else {
            $summaries = $this->textRankService->summarizeText($post->title, $post->description);
            try {
                $apiUrl = "http://localhost:5000/summarize";
                $response = $client->post($apiUrl, [
                    'json' => [
                        'title' => $post->title,
                        'description' => $post->description
                    ]
                ]);
                if ($response->getStatusCode() == 200) {
                    $apiSummary = json_decode($response->getBody(), true);
                    if (isset($apiSummary['paragraph']) && isset($apiSummary['bullet_points'])) {
                        $summaries['paragraph'] = $apiSummary['paragraph'];
                        $summaries['bullet_points'] = $apiSummary['bullet_points'];
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error calling Flask API: ' . $e->getMessage());
            }
            $summarizedPosts[$post_id] = $summaries;
            session()->put('summarized_posts', $summarizedPosts);
        }

        $wordCount = str_word_count(strip_tags($post->description));
        $readingTime = ceil($wordCount / 200);

        $data = [
            'post' => $post,
            'post_id' => $post_id,
            'comments' => $comments,
            'categories' => $categories,
            'popularPosts' => $popularPosts,
            'trendingPosts' => $trendingPosts,
            'paragraph_summary' => $summaries['paragraph'] ?? '',
            'bullet_point_summary' => $summaries['bullet_points'] ?? [],
            'readingTime' => $readingTime,
            'positiveCount' => $positiveCount,
            'negativeCount' => $negativeCount,
            'neutralCount' => $neutralCount,
        ];

        return view(parent::loadDefaultDataToView($this->view_path . '.single-post'), compact('data'));
    }

    public function category(Request $request, $name)
    {
        $category = Category::where('name', $name)->firstOrFail();
        $category_id = $category->id;
        $post = Posts::where('category_id', $category_id)->whereHas('user', function ($query) {
            $query->where('status', 1); // Ensure user status is 1
        })->where('status', 1)->paginate('10');
        Paginator::useBootstrap();

        // Sidebar info 
        $categories = Category::get();
        $popularPosts = Posts::orderBy('views', 'DESC')->whereHas('user', function ($query) {
            $query->where('status', 1); // Ensure user status is 1
        })->take(7)->get();
        $trendingPosts = $this->postService->getTrendingPosts();

        $data = [
            'category' => $category,
            'category_id' => $category_id,
            'post' => $post,
            'categories' => $categories,
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

        // Delete the notification
        $notification = $post->user->notifications()
            ->where('type', 'like')
            ->whereJsonContains('data', ['message' => "{$user->name} liked your post."])
            ->first();

        if ($notification) {
            $notification->delete();
        }

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
