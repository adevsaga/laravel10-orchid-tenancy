<?php

namespace App\Orchid\Layouts\Admin\Tenant;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Password;
use Orchid\Screen\Layouts\Rows;

class TenantUserPasswordLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        $tenant = $this->query->get('tenant');

        $placeholder = $tenant?->user?->exists
            ? __('Leave empty to keep current password')
            : __('Enter the password to be set');

        return [
            Password::make('user.password')
                ->placeholder($placeholder)
                ->title(__('Password')),
        ];
    }
}
