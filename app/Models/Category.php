<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'status'];
    public function getRules(array $validate)
    {
        return validator($validate, [
            'name'          => 'required|max:25|min:2',
            'description'   => 'required|max:1000|min:2',
            'status'        => 'nullable|boolean',
        ]);
    }
    public function posts()
    {
        return $this->hasMany(Posts::class);
    }
}
