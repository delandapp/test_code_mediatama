<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Dislike\Dislike;
use App\Models\Komentar\Komentar;
use App\Models\Like\Like;
use App\Models\Materi\Materi;
use App\Models\RequestVideo\RequestVideo;
use App\Models\Simpan\Simpan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    public function materis()
    {
        return $this->belongsToMany(Materi::class, 'video_requests', 'user_id', 'video_material_id');
    }

    public function videoRequests()
    {
        return $this->hasMany(RequestVideo::class);
    }

    public function komentars()
    {
        return $this->hasMany(Komentar::class);
    }

    public function dislikes()
    {
        return $this->hasMany(Dislike::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function simpans()
    {
        return $this->hasMany(Simpan::class);
    }
}
