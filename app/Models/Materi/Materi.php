<?php

namespace App\Models\Materi;

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
}
