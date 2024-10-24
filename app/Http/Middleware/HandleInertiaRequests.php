<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'appName' => config('app.name'),
            'logoutUrl' => fn () => auth()->user() ? route('auth.logout.current') : null,
            'pageUris' => [
                'resume' => route(name: 'builder.resume.index', absolute: false),
                'about' => route(name: 'aboutPage', absolute: false),
            ],
            'auth.user' => function () {
                if (!auth()->user()) {
                    return null;
                }

                $user = auth()->user()->load('userProfile', 'roles');
                return [
                    'email_verified' => (bool) $user->email_verified_at,
                    'username' => $user->username,
                    'email' => $user->email,
                    'full_name' => $user->userProfile->full_name,
                    'profile_picture_url' => $user->userProfile->profile_picture_url,
                    'roles' => $user
                        ->roles
                        ->pluck('name')
                        ->map(fn (string $role) => Str::title($role))
                        ->toArray(),
                ];
            },
        ]);
    }
}
