<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Admin\BaseController;
use Illuminate\Http\Request;
use App\Models\Posts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;

class PostController extends BaseController
{
    protected $base_route = "site.post.index";
    protected $view_path = "site.post";
    protected $panel = "Blog";
    protected $model;

    public function __construct()
    {
        $this->model = new Posts;
    }
    public function index()
    {
        $data['row'] = Posts::with(['user', 'category'])->where('user_id', Auth::id())->paginate('10');
        Paginator::useBootstrap();
        return view(parent::loadDefaultDataToView($this->view_path . '.index'), compact('data'));
    }
    public function write()
    {
        $category = $this->model->getCategory();
        $tags = $this->model->getTags();
        $data = [
            'category' => $category,
            'tags' => $tags,
        ];
        return view('site.post.write', compact('data'));
    }
    public function store(Request $request)
    {
        $validator = $this->model->getRules($request->all());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

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

        $folderPath = public_path('uploads/post');
        $this->createFolderIfNotExist($folderPath);

        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $imageName = time() . '.' . $thumbnail->getClientOriginalExtension();
            $thumbnail->move($folderPath, $imageName);
        } else {
            $imageName = null;
        }
        $model->thumbnail = $imageName;

        DB::beginTransaction();
        try {
            $model->save();
            \Log::info('Tags to sync', ['tags' => $request->tags]);
            $model->tags()->sync($request->tags);

            DB::commit();

            $request->session()->flash('success', $this->panel . ' successfully added.');
            return redirect()->route($this->base_route);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Tag sync failed', ['error' => $e->getMessage(), 'tags' => $request->tags]);
            return redirect()->back()->withErrors(['tags' => 'Tag sync failed.'])->withInput();
        }
    }
    public function view(Request $request, $id)
    {
        $post = Posts::findOrFail($id);
        if (Auth::id() !== $post->user_id) {
            return redirect()->route($this->base_route)->with('error', 'You are not authorized to view this post.');
        }
        return redirect()->route('site.single_post', ['slug' => $post->slug]);
    }
    public function edit($id)
    {
        $post = $this->model->findOrFail($id);
        if (Auth::id() !== $post->user_id) {
            return redirect()->route($this->base_route)->with('error', 'You are not authorized to edit this post.');
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
            return redirect()->route($this->base_route)->with('error', 'You are not authorized to update this post.');
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
        if (Auth::id() !== $post->user_id) {
            return redirect()->route($this->base_route)->with('error', 'You are not authorized to delete this post.');
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
