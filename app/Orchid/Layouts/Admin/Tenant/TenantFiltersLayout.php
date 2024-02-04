<?php

namespace App\Orchid\Layouts\Admin\Tenant;

use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class TenantFiltersLayout extends Selection
{
    /**
     * @return string[]|Filter[]
     */
    public function filters(): array
    {
        return [];
    }
}
