<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'nisn', 'nip', 'username', 'foto'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    public function guru()
    {
        return $this->hasOne(Guru::class, 'user_id');
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'user_id');
    }

    public function isGuru()
    {
        return $this->role === 'guru';
    }

    public function isWaliKelas()
    {
        return $this->hasRole('wali_kelas');
    }

    public function isSiswa()
    {
        return $this->role === 'siswa';
    }

    public function isOrangTua()
    {
        return $this->role === 'orang_tua';
    }

    public function isAdmin()
    {
        return $this->role === 'admin' || $this->role === 'superadmin';
    }

    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }

    public function hasRole($role)
    {
        if ($this->role === $role) {
            return true;
        }

        if ($role === 'wali_kelas' && $this->role === 'guru') {
            $guru = $this->guru;
            if ($guru && $guru->kelasWali) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyRole($roles)
    {
        foreach ((array) $roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        
        return false;
    }
}
