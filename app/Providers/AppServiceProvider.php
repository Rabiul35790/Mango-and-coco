<?php

namespace App\Providers;

use App\Actions\Catalog\GetActiveProducts;
use App\Listeners\RecordShopOrder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\VideoOrder;
use App\Observers\ProductObserver;
use App\Observers\VideoOrderObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Speed + safety defaults (modern Laravel strategy)
        Model::preventLazyLoading(! app()->isProduction());
        Model::automaticallyEagerLoadRelationships();

        Product::observe(ProductObserver::class);
        VideoOrder::observe(VideoOrderObserver::class);

        // Lemon Squeezy → shop orders mirror for the customer panel.
        Event::listen(
            \LemonSqueezy\Laravel\Events\OrderCreated::class,
            RecordShopOrder::class,
        );
        foreach ([Category::class, Setting::class] as $model) {
            $model::saved(fn () => GetActiveProducts::flush());
            $model::deleted(fn () => GetActiveProducts::flush());
        }
    }
}
