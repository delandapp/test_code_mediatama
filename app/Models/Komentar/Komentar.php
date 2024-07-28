<?php

namespace App\Models\Komentar;

use App\Models\Materi\Materi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Komentar extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'komentar';
    protected $guarded = ['id'];
    public $timestamps = true;

    public function users() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function materis() {
        return $this->belongsTo(Materi::class, 'materi_id', 'id');
    }
}
