<?php

namespace App\Http\Controllers\Admin;

use App\Models\Posts;
use App\Models\PostView;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    protected $base_route = "admin.index";
    protected $view_path = "admin.index";
    protected $panel = "Verse";

    public function index()
    {
        $data['post'] = Posts::where('status', 1)->count();
        $data['user'] = User::where('status', 1)->where('role', null)->count();
        $data['views_today'] = PostView::whereDate('created_at', Carbon::today())->count();

        // Fetch and preprocess views data grouped by categories
        $viewsByCategory = Posts::select(DB::raw('categories.name as category, SUM(views) as total_views'))
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->groupBy('category')
            ->get();

        // Create arrays for labels and data
        $data['categoryLabels'] = $viewsByCategory->pluck('category')->toArray();
        $data['categoryViews'] = $viewsByCategory->pluck('total_views')->toArray();

        return view(parent::loadDefaultDataToView($this->base_route), compact('data'));
    }
}
