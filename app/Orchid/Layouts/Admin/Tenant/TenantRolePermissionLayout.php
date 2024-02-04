<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Admin\Tenant;

use Illuminate\Support\Collection;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Layouts\Rows;
use Throwable;

class TenantRolePermissionLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @throws Throwable
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return $this->generatedPermissionFields(
            $this->query->getContent('permission')
        );
    }

    private function generatedPermissionFields(Collection $permissionsRaw): array
    {
        $permissionsFiltered = $permissionsRaw->forget('Admin');
        $permissionsFiltered = $permissionsRaw
            ->map(fn (Collection $permissions, $title) => $this->makeCheckBoxGroup($permissions, $title))
            ->flatten()
            ->toArray();
        
        return $permissionsFiltered;
    }

    private function makeCheckBoxGroup(Collection $permissions, string $title): Collection
    {
        return $permissions
            ->map(fn (array $chunks) => $this->makeCheckBox(collect($chunks)))
            ->flatten()
            ->map(fn (CheckBox $checkbox, $key) => $key === 0
                ? $checkbox->title($title)
                : $checkbox)
            ->chunk(4)
            ->map(fn (Collection $checkboxes) => Group::make($checkboxes->toArray())
                ->alignEnd()
                ->autoWidth());
    }

    private function makeCheckBox(Collection $chunks): CheckBox
    {
        return CheckBox::make('permissions.' . base64_encode($chunks->get('slug')))
            ->placeholder($chunks->get('description'))
            ->value($chunks->get('active'))
            ->sendTrueOrFalse();
    }
}
