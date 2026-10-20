<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// email และ role ไม่อยู่ใน Fillable → กันผู้ใช้ส่งค่า role มาเองผ่านฟอร์ม
#[Fillable(['name', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    const string ROLE_ADMIN = 'ADMIN';
    const string ROLE_USER = 'USER';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // ตั้งรหัสแบบ plain text แล้ว Laravel hash ให้อัตโนมัติ
        ];
    }

    /** กิจกรรมที่ผู้ใช้คนนี้สมัคร (ตาราง pivot activity_user) */
    function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class)->withTimestamps();
    }

    function isAdministrator(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    function isMember(): bool
    {
        return $this->role === self::ROLE_USER;
    }
}
