<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use App\Models\ContentFlag;

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
        Paginator::defaultView('layouts.partials.pagination');

        try {
            $flags = Cache::rememberForever('content_flags_all', function () {
                return ContentFlag::orderBy('flag_name')->get();
            });

            View::share('content_flags', $flags);
        } catch (\Throwable $e) {
        }

        Model::updated(function (Model $model) {
            if ($model instanceof Activity) return;

            activity()
            ->performedOn($model)
            //->causedBy(auth()->user())
            ->withProperties([
                'old' => array_intersect_key($model->getOriginal(), $model->getChanges()),
                'attributes' => $model->getChanges(),
            ])
            ->log('updated');
        });

        Model::created(fn ($model) => activity()->performedOn($model)->log('created'));
        Model::deleted(fn ($model) => activity()->performedOn($model)->log('deleted'));
    }
}
