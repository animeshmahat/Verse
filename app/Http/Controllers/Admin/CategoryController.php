<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends BaseController
{
    protected $base_route = 'admin.category.index';
    protected $view_path = 'admin.category';
    protected $panel = 'Category';
    protected $model;

    public function __construct()
    {
        $this->model = new Category;
    }
    public function index()
    {
        $data['row'] = DB::table('categories')->get();
        return view(parent::loadDefaultDataToView($this->view_path . '.index'), compact('data'));
    }

    public function create()
    {
        return view(parent::loadDefaultDataToView($this->view_path . '.create'));
    }

    public function store(Request $request)
    {
        $validator = $this->model->getRules($request->all());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $model = $this->model;
        $model->name = $request->name;
        $model->description = $request->description;

        $success = $model->save();

        if ($success) {
            $request->session()->flash('success', $this->panel . ' successfully added.');
            return redirect()->route($this->base_route);
        } else {
            $request->session()->flash('error', $this->panel . ' could not be added.');
            return redirect()->route($this->base_route);
        }
    }

    public function edit($id)
    {
        $data = [];
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
        $data->name = $request->name;
        $data->description = $request->description;
        $success = $data->save();

        if ($success) {
            $request->session()->flash('update_success', $this->panel . ' successfully updated.');
            return redirect()->route($this->base_route);
        } else {
            $request->session()->flash('error', $this->panel . ' could not be updated.');
            return redirect()->route($this->base_route);
        }
    }

    public function delete($id)
    {
        $model = $this->model;
        $data = $model::findOrFail($id);
        $success = $data->delete();

        if ($success) {
            return redirect()->route($this->base_route)->with('delete_success', $this->panel . ' successfully deleted.');
        }
    }
}
