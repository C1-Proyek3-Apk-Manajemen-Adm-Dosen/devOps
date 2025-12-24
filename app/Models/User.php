<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'role',
        'google_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke ProfilDosen
     */
    public function profilDosen()
    {
        return $this->hasOne(ProfilDosen::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke Dokumen
     */
    public function dokumens()
    {
        return $this->hasMany(Dokumen::class, 'id_user', 'id_user');
    }

    /**
     * Cek apakah user adalah dosen
     */
    public function isDosen(): bool
    {
        return $this->role === 'dosen';
    }

    /**
     * Cek apakah user adalah kaprodi
     */
    public function isKaprodi(): bool
    {
        return $this->role === 'koordinator';
    }

    /**
     * Cek apakah user adalah TU
     */
    public function isTu(): bool
    {
        return $this->role === 'tu';
    }

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'administrator';
    }
}