<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Admin\Tenant;

use App\Models\Tenant;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class TenantListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'tenants';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('id', __('ID'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn (Tenant $tenant) => $tenant->id),

            TD::make('owner', __('Owner'))
                ->render(fn (Tenant $tenant) => $tenant->run(function () {
                    return User::where('type', 'owner')->first()->name;
                })),

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),

            TD::make('updated_at', __('Last edit'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->sort(),

            TD::make(__('Actions'))
                ->cantHide()
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Tenant $tenant) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Edit'))
                            ->route('platform.admin.tenants.edit', $tenant->id)
                            ->icon('bs.pencil'),

                        Button::make(__('Delete'))
                            ->icon('bs.trash3')
                            ->confirm(__('Once the tenant is deleted, all of its resources and data will be permanently deleted.'))
                            ->method('remove', [
                                'id' => $tenant->id,
                            ]),
                    ])),
        ];
    }
}
