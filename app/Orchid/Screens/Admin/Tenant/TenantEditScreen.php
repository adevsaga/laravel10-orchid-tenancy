<?php

namespace App\Orchid\Screens\Admin\Tenant;

use App\Models\Tenant;
use App\Models\User;
use App\Orchid\Layouts\Admin\Tenant\TenantEditLayout;
use App\Orchid\Layouts\Admin\Tenant\TenantUserPasswordLayout;
use App\Orchid\Layouts\Admin\Tenant\TenantRolePermissionLayout;
use App\Orchid\Layouts\Admin\Tenant\TenantUserEditLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class TenantEditScreen extends Screen
{
    /**
     * @var Tenant
     */
    public $tenant;

    /**
     * @var User
     */
    public $user;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Tenant $tenant): iterable
    {
        $tenant->load(['domains']);

        $domain = $tenant->getDomain();
        // Force the tenant to have a user to use permission function
        $tenantUser = $tenant->getOwner();

        return [
            'user' => $tenantUser,
            'tenant' => $tenant,
            'domain' => $domain,
            'permission' => $tenantUser->getStatusPermission(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return $this->tenant->exists ? __('Edit Tenant') : __('Create Tenant');
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return __('Tenant profile and domains.');
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
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Remove'))
                ->icon('bs.trash3')
                ->confirm(__('Once the tenant is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                ->method('remove')
                ->canSee($this->tenant->exists),

            Button::make(__('Save'))
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    /**
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::block(TenantEditLayout::class)
                ->title(__('Tenant Information'))
                ->description(__('Update tenant domain\'s information'))
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->tenant->exists)
                        ->method('save')
                ),

            Layout::block(TenantUserEditLayout::class)
                ->title(__('Tenant User Information'))
                ->description(__('Update tenant account\'s profile information and email address.'))
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->tenant->exists)
                        ->method('save')
                ),

            Layout::block(TenantUserPasswordLayout::class)
                ->title(__('Password'))
                ->description(__('Ensure tenant account is using a long, random password to stay secure.'))
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->tenant->exists)
                        ->method('save')
                ),

            Layout::block(TenantRolePermissionLayout::class)
                ->title(__('Permissions'))
                ->description(__('Allow the tenant to perform some actions'))
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->tenant->exists)
                        ->method('save')
                ),
        ];
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Tenant $tenant, Request $request)
    {
        $request->validate([
            'domain.domain' => [
                'required',
                Rule::unique(config('tenancy.domain_model'), 'domain')->ignore($tenant->getDomain()),
            ],
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($tenant->getOwner()),
            ],
        ]);

        $tenant->save();

        $domain = $tenant->domains()->first();

        if (empty($domain)) {
            $tenant->domains()->create([
                'domain' => $request->input('domain.domain'),
            ]);
        } else {
            $domain->update([
                'domain' => $request->input('domain.domain'),
            ]);
        }

        $tenant->run(function () use ($request) {
            $permissions = collect($request->get('permissions'))
                ->map(fn ($value, $key) => [base64_decode($key) => $value])
                ->collapse()
                ->toArray();

            $user = User::where('type', 'owner')->first();

            if (empty($user)) {
                $user = new User();
                $user->type = 'owner';
            }

            $user->when($request->filled('user.password'), function (Builder $builder) use ($request) {
                $builder->getModel()->password = Hash::make($request->input('user.password'));
            });

            $user
                ->fill($request->collect('user')->except(['password', 'permissions', 'roles'])->toArray())
                ->fill(['permissions' => $permissions])
                ->fill(['type' => 'owner'])
                ->save();

            $user->replaceRoles($request->input('user.roles'));
        });

        Toast::info(__('Tenant was saved.'));

        return redirect()->route('platform.admin.tenants');
    }

    /**
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Tenant $tenant)
    {
        $tenant->delete();

        Toast::info(__('Tenant was removed'));

        return redirect()->route('platform.admin.tenants');
    }
}
