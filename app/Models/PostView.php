<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostView extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'viewed_at',
    ];

    public function post()
    {
        return $this->belongsTo(Posts::class, 'post_id');
    }
}
