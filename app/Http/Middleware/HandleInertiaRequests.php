<?php

namespace App\Http\Middleware;

use App\Actions\Site\GetSiteData;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'site' => fn () => [
                'settings' => GetSiteData::settings(),
                'categories' => GetSiteData::categories(),
            ],
            'auth' => [
                'user' => fn () => ($u = $request->user())
                    ? ['id' => $u->id, 'name' => $u->name, 'email' => $u->email]
                    : null,
            ],
            'appName' => config('app.name', 'Mango&Coco'),
            'lemonConfigured' => (bool) (env('LEMON_SQUEEZY_API_KEY') && env('LEMON_SQUEEZY_STORE_ID')),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
