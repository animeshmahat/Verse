<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use Illuminate\Http\Request;
use App\Models\Posts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class PostController extends BaseController
{
    protected $base_route = "admin.post.index";
    protected $view_path = "admin.post";
    protected $panel = "Blog";
    protected $model;

    public function __construct()
    {
        $this->model = new Posts;
    }
    public function index()
    {
        $posts = Posts::with(['user', 'category'])->get();

        // Initialize Guzzle client
        $client = new Client();

        foreach ($posts as $post) {
            if ($post->title) {
                try {
                    // Send the title to the Flask API
                    $response = $client->post('http://127.0.0.1:5000/predict', [
                        'json' => ['text' => $post->title]
                    ]);

                    // Get the sentiment from the API response
                    $result = json_decode($response->getBody(), true);

                    // Attach the sentiment to the post object
                    $post->sentiment = $result['sentiment'] ?? 'unknown'; // 'positive', 'negative', or 'neutral'

                } catch (\Exception $e) {
                    // If the API call fails, set sentiment to 'unknown'
                    $post->sentiment = 'unknown';
                }
            } else {
                // Default value if title is missing
                $post->sentiment = 'unknown';
            }
        }

        $data['row'] = $posts;
        return view(parent::loadDefaultDataToView($this->view_path . '.index'), compact('data'));
    }
    public function create()
    {
        $category = $this->model->getCategory();
        $tags = $this->model->getTags();
        $data = [
            'category' => $category,
            'tags' => $tags,
        ];
        return view(parent::loadDefaultDataToView($this->view_path . '.create'), compact('data'));
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validator = $this->model->getRules($request->all());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create a new post model instance and set its properties
        $model = $this->model;
        $model->user_id = auth()->user()->id;
        $model->category_id = $request->category_id;
        $model->unique_id = Str::uuid();
        $model->title = $request->title;
        $model->description = $request->description;
        $model->status = $request->status ? true : false;
        $model->slug = Str::slug($request->title);
        $model->views = 0;
        $model->comments_count = 0;
        $model->likes_count = 0;

        // Ensure the upload directory exists
        $folderPath = public_path('uploads/post');
        $this->createFolderIfNotExist($folderPath);

        // Handle file upload
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $imageName = time() . '.' . $thumbnail->getClientOriginalExtension();
            $thumbnail->move($folderPath, $imageName);
        } else {
            $imageName = null;
        }
        $model->thumbnail = $imageName;

        // Save the model to the database first before syncing tags
        DB::beginTransaction();
        try {
            $model->save();
            \Log::info('Tags to sync', ['tags' => $request->tags]);
            $model->tags()->sync($request->tags);

            DB::commit();

            // Set the success message and redirect
            $request->session()->flash('success', $this->panel . ' successfully added.');
            return redirect()->route($this->base_route);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging purposes
            \Log::error('Tag sync failed', ['error' => $e->getMessage(), 'tags' => $request->tags]);
            return redirect()->back()->withErrors(['tags' => 'Tag sync failed.'])->withInput();
        }
    }
    public function view(Request $request, $id)
    {
        $data['row'] = Posts::with(['user', 'category', 'tags'])->findOrFail($id);

        // Initialize Guzzle client
        $client = new Client();

        if (isset($data['row']->title)) {
            try {
                // Send the title to the Flask API
                $response = $client->post('http://127.0.0.1:5000/predict', [
                    'json' => ['text' => $data['row']->title]
                ]);

                // Get the sentiment from the API response
                $result = json_decode($response->getBody(), true);

                // Attach the sentiment to the post object
                $data['row']->sentiment = $result['sentiment'] ?? 'unknown'; // 'positive', 'negative', or 'neutral'
            } catch (\Exception $e) {
                // If the API call fails, set sentiment to 'unknown'
                $data['row']->sentiment = 'unknown';
            }
        } else {
            // Default value if title is missing
            $data['row']->sentiment = 'unknown';
        }

        return view(parent::loadDefaultDataToView($this->view_path . '.view'), compact('data'));
    }
    public function edit($id)
    {
        $post = $this->model->findOrFail($id);
        if (Auth::id() !== $post->user_id) {
            return redirect()->route('admin.post.index')->with('error', 'You are not authorized to edit this post.');
        }

        $data = [];
        $data['category'] = $this->model->getCategory();
        $data['tags'] = $this->model->getTags();
        $data['row'] = $post;
        return view(parent::loadDefaultDataToView($this->view_path . '.edit'), compact('data'));
    }
    public function update(Request $request, $id)
    {
        $post = $this->model->findOrFail($id);
        if (Auth::id() !== $post->user_id) {
            return redirect()->route('admin.post.index')->with('error', 'You are not authorized to update this post.');
        }

        $validator = $this->model->getRules($request->all());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $post->category_id = $request->category_id;
        $post->title = $request->title;
        $post->description = $request->description;
        $post->status = $request->status ? true : false;

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($post->thumbnail) {
                $image_path = public_path("uploads/post/{$post->thumbnail}");
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            $thumbnail = $request->file('thumbnail');
            $imageName = time() . '.' . $thumbnail->getClientOriginalExtension();
            $thumbnail->move(public_path('uploads/post'), $imageName);
            $post->thumbnail = $imageName;
        }

        DB::beginTransaction();
        try {
            $post->save();
            \Log::info('Tags to sync', ['tags' => $request->tags]);
            $post->tags()->sync($request->tags);
            DB::commit();

            $request->session()->flash('update_success', $this->panel . ' successfully updated.');
            return redirect()->route($this->base_route);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Tag sync failed', ['error' => $e->getMessage(), 'tags' => $request->tags]);
            return redirect()->back()->withErrors(['tags' => 'Tag sync failed.'])->withInput();
        }
    }
    public function delete($id)
    {
        $post = $this->model->findOrFail($id);
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'superadmin') {
            return redirect()->route('admin.post.index')->with('error', 'You are not authorized to delete this post.');
        }

        if ($post->thumbnail) {
            $image_path = public_path("uploads/post/{$post->thumbnail}");
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $success = $post->delete();

        if ($success) {
            return redirect()->route($this->base_route)->with('delete_success', $this->panel . ' successfully deleted.');
        }
    }
}
