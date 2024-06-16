<?php

namespace App\Http\Controllers\Admin;

use App\Models\Posts;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

class DashboardController extends BaseController
{
    protected $base_route = "admin.index";
    protected $view_path = "admin.index";
    protected $panel = "Pensieve";
    public function index()
    {
        return view(parent::loadDefaultDataToView($this->base_route));
    }
}
