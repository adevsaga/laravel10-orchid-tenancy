<?php

namespace App\Models;

use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;
use Stancl\Tenancy\Contracts\Domain;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use AsSource, Filterable, HasDatabase, HasDomains;

    protected $fillable = [
        'user_id',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'user_id'
        ];
    }

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'updated_at',
        'created_at',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'id'         => Like::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    public function getDomain(): Domain|null
    {
        return $this->domains()->first();
    }

    public function getOwner(): User
    {
        if (empty($this->getDomain())) {
            return new User();
        }

        return self::run(function () {
            return User::where('type', 'owner')->first();
        });
    }
}
