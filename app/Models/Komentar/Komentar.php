<?php

namespace App\Models\Komentar;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Komentar extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'komentar';
    protected $guarded = ['id'];
    public $timestamps = true;
}
