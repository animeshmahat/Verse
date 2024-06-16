<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    public function getRules(array $validate)
    {
        return validator($validate, [
            'name' => 'required | string | min:2 | max:100 | unique:tags',
        ]);
    }
    public function posts()
    {
        return $this->belongsToMany(Posts::class, 'post_tag', 'tag_id', 'post_id');
    }
}
