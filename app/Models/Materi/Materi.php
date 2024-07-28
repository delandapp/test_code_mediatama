<?php

namespace App\Models\Materi;

use App\Models\Komentar\Komentar;
use App\Models\RequestVideo\RequestVideo;
use App\Models\User;
use App\Observers\MateriObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([MateriObserver::class])]
class Materi extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'video_materials';
    protected $guarded = ['id'];
    public $timestamps = true;

    public function custommers()
    {
        return $this->belongsToMany(User::class, 'video_requests', 'video_material_id', 'user_id');
    }

    public function videoRequests()
    {
        return $this->hasMany(RequestVideo::class, 'video_material_id', 'id');
    }

    public function komentars()
    {
        return $this->hasMany(Komentar::class, 'materi_id', 'id');
    }
}
