<?php

namespace App\Http\Controllers\Admin;

use App\Models\Posts;
use App\Models\User;

class DashboardController extends BaseController
{
    protected $base_route = "admin.index";
    protected $view_path = "admin.index";
    protected $panel = "Pensieve";
    public function index()
    {
        $data['post'] = Posts::where('status', 1)->count();
        $data['user'] = User::where('status', 1)->where('role', null)->count();
        return view(parent::loadDefaultDataToView($this->base_route), compact('data'));
    }
}
