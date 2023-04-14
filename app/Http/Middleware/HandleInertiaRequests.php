<?php

namespace App\Http\Middleware;

use App\Models\ModuleGroup;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array
     */
    public function share(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user['role'] = Role::where('id', $user->role_id)->value('name');
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
                'system_settings' => Setting::getSettings(),
                'modules' => ModuleGroup::with('modules')->get(),
            ],
            'ziggy' => function () {
                return (new Ziggy)->toArray();
            },
        ]);
    }
}
