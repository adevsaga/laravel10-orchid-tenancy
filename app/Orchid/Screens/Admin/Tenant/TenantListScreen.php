<?php

namespace App\Orchid\Screens\Admin\Tenant;

use App\Models\Tenant;
use App\Orchid\Layouts\Admin\Tenant\TenantFiltersLayout;
use App\Orchid\Layouts\Admin\Tenant\TenantListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class TenantListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'tenants' => Tenant::filters(TenantFiltersLayout::class)
                ->defaultSort('id', 'desc')
                ->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Tenant Management');
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'A comprehensive list of all registered tenants.';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.admin.tenants',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('bs.plus-circle')
                ->route('platform.admin.tenants.create'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            TenantListLayout::class,
        ];
    }

    public function remove(Request $request): void
    {
        Tenant::findOrFail($request->get('id'))->delete();

        Toast::info(__('Tenant was removed.'));
    }
}
