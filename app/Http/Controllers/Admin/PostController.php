<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use Illuminate\Http\Request;
use App\Models\Posts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $data['row'] = Posts::with(['user', 'category'])->get();
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
        return view(parent::loadDefaultDataToView($this->view_path . '.view'), compact('data'));
    }

    public function edit($id)
    {
        $data = [];
        $data['category'] = $this->model->getCategory();
        $data['tags'] = $this->model->getTags();
        $data['row'] = $this->model->findOrFail($id);
        return view(parent::loadDefaultDataToView($this->view_path . '.edit'), compact('data'));
    }

    public function update(Request $request, $id)
    {
        $validator = $this->model->getRules($request->all());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $this->model::findOrFail($id);
        $data->category_id = $request->category_id;
        $data->title = $request->title;
        $data->description = $request->description;
        $data->status = $request->status ? true : false;

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($data->thumbnail) {
                $image_path = public_path("uploads/post/{$data->thumbnail}");
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            $thumbnail = $request->file('thumbnail');
            $imageName = time() . '.' . $thumbnail->getClientOriginalExtension();
            $thumbnail->move(public_path('uploads/post'), $imageName);
            $data->thumbnail = $imageName;
        }

        // Save the updated post
        DB::beginTransaction();
        try {
            $data->save();
            // Sync tags
            \Log::info('Tags to sync', ['tags' => $request->tags]);
            $data->tags()->sync($request->tags);
            DB::commit();

            // Set the update success message and redirect
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
        $model = $this->model;
        $data = $model::findOrFail($id);

        if ($data->thumbnail) {
            $image_path = public_path("uploads/post/{$data->thumbnail}");
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $success = $data->delete();

        if ($success) {
            // Set the delete success message and redirect
            return redirect()->route($this->base_route)->with('delete_success', $this->panel . ' successfully deleted.');
        }
    }
}
