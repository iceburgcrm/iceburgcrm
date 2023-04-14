<?php

use App\Models\Connector;
use App\Models\Datalet;
use App\Models\Module;
use App\Models\ModuleSubpanel;
use App\Models\Permission;
use App\Models\Relationship;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('modules', function () {
    return Inertia::render('Admin/Modules', [
        'themes' => Module::where('status', 1)->get(),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Admin', 'url' => '', 'svg' => 'admin'],
            ['name' => 'Modules', 'url' => '', 'svg' => 'settings']),
    ]);
})->middleware(['auth', 'verified'])->name('admin_modules');

Route::get('connectors', function () {

    return Inertia::render('Admin/Connectors', [
        'connectors' => Connector::with('endpoints')->get(),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Admin', 'url' => '', 'svg' => 'admin'],
            ['name' => 'Connectors', 'url' => '', 'svg' => 'settings']),
    ]);
})->middleware(['auth', 'verified'])->name('connectors');

Route::get('data', function () {

    return Inertia::render('Admin/Data', [
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Admin', 'url' => '', 'svg' => 'admin'],
            ['name' => 'Data', 'url' => '', 'svg' => 'settings']),
    ]);
})->middleware(['auth', 'verified'])->name('data');

Route::get('scheduler', function () {

    return Inertia::render('Admin/Schedule', [
        'schedule' => Schedule::where('user_id', auth()->user()->id)
            ->where('status', 1)
            ->get(),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Admin', 'url' => '', 'svg' => 'admin'],
            ['name' => 'Connectors', 'url' => '', 'svg' => 'settings']),
    ]);
})->middleware(['auth', 'verified'])->name('scheduler');

Route::get('connector/{connector_id}', function ($connector_id) {

    return Inertia::render('Admin/Connector', [
        'connector' => Connector::with('endpoints')->first(),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Admin', 'url' => '', 'svg' => 'admin'],
            ['name' => 'Connectors', 'url' => '', 'svg' => 'settings']),
    ]);
})->middleware(['auth', 'verified'])->name('connector');

Route::get('workflow', function () {

    return Inertia::render('Admin/Workflow', [
        'permissions' => Permission::with('modules')->with('roles')->get(),
        'roles' => Role::all(),
        'modules' => Module::where('status', 1)->get(),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Admin', 'url' => '', 'svg' => 'admin'],
            ['name' => 'Permissions', 'url' => '', 'svg' => 'settings']),
    ]);
})->middleware(['auth', 'verified'])->name('permissions');

Route::get('subpanels', function () {

    return Inertia::render('Admin/Subpanels', [
        'subpanels' => ModuleSubpanel::where('status', 1)->get(),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Admin', 'url' => '', 'svg' => 'admin'],
            ['name' => 'Subpanels', 'url' => '', 'svg' => 'settings']),
    ]);
})->middleware(['auth', 'verified'])->name('admin_subpanels');

Route::get('datalets', function () {

    return Inertia::render('Admin/Datalets', [
        'subpanels' => Dashlet::where('status', 1)->get(),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Admin', 'url' => '', 'svg' => 'admin'],
            ['name' => 'Datalets', 'url' => '', 'svg' => 'settings']),
    ]);
})->middleware(['auth', 'verified'])->name('admin_datalets');

Route::get('builder', function () {

    return Inertia::render('Admin/Builder', [
        'modules' => Module::with('fields')
        ->with('subpanels')
        ->get(),
        'datalets' => Datalet::get()->toArray(),
        'relationships' => Relationship::get()->toArray(),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Admin', 'url' => '', 'svg' => 'admin'],
            ['name' => 'Builder', 'link' => '', 'svg' => 'settings']),
    ]);
})->middleware(['auth', 'verified'])->name('builder');

Route::get('permissions', function () {

    return Inertia::render('Admin/Permissions', [
        'permissions' => Permission::with('modules')->with('roles')->get(),
        'roles' => Role::all(),
        'modules' => Module::where('status', 1)->get(),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Admin', 'url' => '', 'svg' => 'admin'],
            ['name' => 'Permissions', 'url' => '', 'svg' => 'settings']),
    ]);
})->middleware(['auth', 'verified'])->name('permissions');
