<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',           // ← Tambahkan ini
    ];

    /**
     * Role yang tersedia di sistem
     */
    public const ROLE_ADMIN    = 'admin';
    public const ROLE_OPERATOR = 'operator';
    public const ROLE_MANAGER  = 'manager';   // ← Role baru

    /**
     * Check Role
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isOperator(): bool
    {
        return $this->role === self::ROLE_OPERATOR;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;   // ← Tambahkan ini
    }

    /**
     * Helper untuk cek apakah user boleh mengakses dashboard laporan
     */
    public function canAccessReports(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_OPERATOR,
            self::ROLE_MANAGER
        ]);
    }

    /**
     * Helper untuk cek apakah user boleh mengubah data deteksi
     * (Admin & Operator boleh, Manager TIDAK boleh)
     */
    public function canModifyDetection(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_OPERATOR
        ]);
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
}