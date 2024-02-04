<?php

declare(strict_types=1);

use App\Orchid\Screens\Admin\Tenant\TenantEditScreen;
use App\Orchid\Screens\Admin\Tenant\TenantListScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

// Platform > Admin > Tenants > Create
Route::screen('tenants/create', TenantEditScreen::class)
    ->name('platform.admin.tenants.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.admin.tenants')
        ->push(__('Create'), route('platform.admin.tenants.create')));

// Platform > Admin > Tenants > Edit
Route::screen('tenants/{tenant}/edit', TenantEditScreen::class)
    ->name('platform.admin.tenants.edit')
    ->breadcrumbs(fn (Trail $trail, $tenant) => $trail
        ->parent('platform.admin.tenants')
        ->push($tenant->id, route('platform.admin.tenants.edit', $tenant)));

// Platform > Admin > Tenants
Route::screen('tenants', TenantListScreen::class)
    ->name('platform.admin.tenants')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Tenants'), route('platform.admin.tenants')));
