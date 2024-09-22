<?php

namespace App\Http\Controllers\Admin;

use App\Models\Comments;
use App\Models\Posts;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends BaseController
{

    protected $base_route = 'admin.comment.index';
    protected $view_path = 'admin.comment';
    protected $panel = 'Comment';
    protected $model;
    public function index()
    {
        // Fetch all posts with their related comments and users
        $posts = Posts::with(['comments', 'comments.user'])->get();

        // Initialize Guzzle client
        $client = new Client();

        // Retrieve cached sentiment data from session
        $cachedSentiments = session()->get('sentiment_data', []);

        foreach ($posts as $post) {
            $postId = $post->id;
            $positiveCount = 0;
            $negativeCount = 0;
            $neutralCount = 0;

            foreach ($post->comments as $comment) {
                $commentId = $comment->id;

                // Check if sentiment for this comment is already cached
                if (isset($cachedSentiments[$postId][$commentId])) {
                    $sentiment = $cachedSentiments[$postId][$commentId];
                } else {
                    try {
                        // Send the comment text to the Flask API
                        $response = $client->post('http://127.0.0.1:5000/predict', [
                            'json' => ['text' => $comment->comment]
                        ]);

                        // Get the sentiment from the API response
                        $result = json_decode($response->getBody(), true);
                        $sentiment = $result['sentiment'] ?? 'unknown'; // 'positive', 'negative', or 'neutral'

                        // Cache the sentiment result in the session
                        $cachedSentiments[$postId][$commentId] = $sentiment;
                    } catch (\Exception $e) {
                        // Handle API errors gracefully
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

            // Attach the counts to the post object
            $post->comments_count = $post->comments->count();
            $post->positive_count = $positiveCount;
            $post->negative_count = $negativeCount;
            $post->neutral_count = $neutralCount;
        }

        // Store updated sentiment data in the session
        session()->put('sentiment_data', $cachedSentiments);

        $data['row'] = $posts;
        return view(parent::loadDefaultDataToView($this->view_path . '.index'), compact('data'));
    }
    public function view(Request $request, $id)
    {
        // Fetch all comments related to the post, including their replies
        $data['row'] = Posts::with([
            'comments' => function ($query) {
                $query->with('user', 'replies.user');
            }
        ])->findOrFail($id);

        // Initialize Guzzle client
        $client = new Client();

        // Retrieve cached sentiment data from session
        $cachedSentiments = session()->get('sentiment_data', []);

        // Analyze sentiment for each comment
        foreach ($data['row']->comments as $comment) {
            $commentId = $comment->id;

            // Check if the sentiment for the comment is cached
            if (isset($cachedSentiments[$commentId])) {
                $comment->sentiment = $cachedSentiments[$commentId];
            } else {
                try {
                    // Call Flask API for sentiment analysis
                    $response = $client->post('http://127.0.0.1:5000/predict', [
                        'json' => ['text' => $comment->comment]
                    ]);

                    $result = json_decode($response->getBody(), true);
                    $comment->sentiment = $result['sentiment'] ?? 'unknown';

                    // Cache the sentiment result
                    $cachedSentiments[$commentId] = $comment->sentiment;
                } catch (\Exception $e) {
                    $comment->sentiment = 'unknown';
                }
            }

            // Analyze sentiment for each reply
            foreach ($comment->replies as $reply) {
                $replyId = $reply->id;

                // Check if the sentiment for the reply is cached
                if (isset($cachedSentiments[$replyId])) {
                    $reply->sentiment = $cachedSentiments[$replyId];
                } else {
                    try {
                        // Call Flask API for sentiment analysis of the reply
                        $response = $client->post('http://127.0.0.1:5000/predict', [
                            'json' => ['text' => $reply->comment]
                        ]);

                        $result = json_decode($response->getBody(), true);
                        $reply->sentiment = $result['sentiment'] ?? 'unknown';

                        // Cache the sentiment result
                        $cachedSentiments[$replyId] = $reply->sentiment;
                    } catch (\Exception $e) {
                        $reply->sentiment = 'unknown';
                    }
                }
            }
        }

        // Store updated sentiment data in session
        session()->put('sentiment_data', $cachedSentiments);

        return view(parent::loadDefaultDataToView($this->view_path . '.view'), compact('data'));
    }

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

        // Create notification with only the date
        $date = Carbon::now()->format('Y-m-d');
        $message = Auth::user()->name . " commented on your post ({$post->title}) on {$date}.";

        // Ensure unique notification
        $post->user->notifications()->updateOrCreate(
            [
                'type' => 'comment',
                'data' => json_encode(['message' => $message]),
                'user_id' => $post->user->id
            ],
            [
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

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
