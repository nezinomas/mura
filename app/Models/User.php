<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'email',
        'password',
    ];

    /**
     * The Name mutator
     * Automatically trim spaces and force lowercase.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => strtolower(trim($value)),
        );
    }

    /**
     * The Email mutator
     * Automatically trim spaces
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => trim($value),
        );
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->display_name)) {
                $user->display_name = $user->name;
            }
        });
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function grabs()
    {
        return $this->belongsToMany(Quote::class)->withTimestamps();
    }
}
