<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    use HasFactory;
    protected $fillable = ['post_id', 'parent_id', 'user_id'];

    public function post()
    {
        return $this->belongsTo(Posts::class, 'post_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function parent()
    {
        return $this->belongsTo(Comments::class, 'parent_id');
    }
    public function replies()
    {
        return $this->hasMany(Comments::class, 'parent_id');
    }
}
