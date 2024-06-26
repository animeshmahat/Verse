<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $all_view['category'] = DB::table('categories')->get();
        $all_view['tags'] = DB::table('tags')->get();
        $all_view['setting'] = DB::table('settings')->first();
        View::share(compact('all_view'));
    }
}
