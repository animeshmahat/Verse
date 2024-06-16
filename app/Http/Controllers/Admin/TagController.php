<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    protected $base_route = 'tags.index';
    protected $view_path = 'admin.tags';
    protected $panel = 'Tags';
    protected $model;

    public function __construct()
    {
        $this->model = new Tags;
    }

    public function index()
    {
        $data['row'] = DB::table('tags')->get();
        return view($this->view_path . '.index', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = $this->model->getRules($request->all());

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 422);
        }

        $tags = explode(',', $request->name);
        foreach ($tags as $tag) {
            $model = new Tags;
            $model->name = trim($tag);
            $model->save();
        }

        return response()->json(['success' => 'Tags successfully added.']);
    }

    public function update(Request $request, $id)
    {
        $validator = $this->model->getRules($request->all());
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 422);
        }

        $data = $this->model::findOrFail($id);
        $data->name = $request->name;
        $data->save();

        return response()->json(['success' => 'Tag successfully updated.']);
    }

    public function delete($id)
    {
        $data = $this->model::findOrFail($id);
        $data->delete();

        return response()->json(['success' => 'Tag deleted successfully.']);
    }
}
