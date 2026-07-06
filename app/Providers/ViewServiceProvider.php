<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\AuctionProperty;
use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        View::composer(['front-end/*'], function ($view) {

            $view->with('categories', cache()->remember('categories', '60', function () {
                return Category::withCount('auctions')->where('status', 'published')->get();
            }));

        });
    }
}
