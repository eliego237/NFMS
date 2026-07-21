<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'first_name',
    'last_name',
    'email',
    'phone',
    'photo',
    'status',
    'last_login_at',
    'password',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;

    protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'status'            => 'boolean',
        'password'          => 'hashed',
    ];
}
}