<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'company_cell_number',
        'company_address',
        'email',
        'role',
        'client_type',
        'img_path',
        'url_sharepoint',
        'socials',
        'assets',
        'project_manager',
        'password',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function personInContact()
    {
        return $this->hasMany(PersonInContact::class, 'user_id');
    }

    public function clientRequest()
    {
        return $this->hasMany(ClientRequest::class, 'user_id');
    }

    public function moreInfo()
    {
        return $this->hasMany(MoreInfoValue::class, 'user_id');
    }

    public function invoice()
    {
        return $this->hasMany(Invoice::class, 'user_id');
    }

    public function scopeClient(Builder $query)
    {
        return $query->where('role', 'client');
    }
}
