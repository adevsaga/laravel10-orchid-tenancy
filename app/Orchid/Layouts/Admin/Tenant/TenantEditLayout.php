<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Admin\Tenant;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class TenantEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('domain.domain')
                ->type('text')
                ->max(63)
                ->required()
                ->title(__('Domain'))
                ->placeholder(__('Domain')),
        ];
    }
}
