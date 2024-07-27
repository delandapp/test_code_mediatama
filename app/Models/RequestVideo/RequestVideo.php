<?php

namespace App\Models\RequestVideo;

use App\Models\Materi\Materi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestVideo extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'video_requests';
    protected $guarded = ['id'];
    public $timestamps = true;

    public function custommers()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function materis()
    {
        return $this->belongsTo(Materi::class, 'video_material_id', 'id');
    }
}
