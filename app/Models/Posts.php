<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Posts extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'user_id',
        'unique_id',
        'title',
        'slug',
        'thumbnail',
        'description',
        'status',
        'views',
        'comments_count',
        'likes_count'
    ];

    public function getRules(array $validate)
    {
        return validator($validate, [
            'category_id' => 'required',
            'title' => 'required|string|min:2|max:100',
            'description' => 'required|string',
            'status' => 'nullable|boolean',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
            'tags' => 'array',
            'tags.*' => 'integer|exists:tags,id',
        ]);
    }

    public function getUser()
    {
        return DB::table('users')->get();
    }

    public function getCategory()
    {
        return DB::table('categories')->get();
    }

    public function getTags()
    {
        return DB::table('tags')->get();
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'post_id');
    }
    public function likes()
    {
        return $this->hasMany(Likes::class);
    }
    public function tags()
    {
        return $this->belongsToMany(Tags::class, 'post_tag', 'post_id', 'tag_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function postTags()
    {
        return DB::table('post_tag')->where('post_id', $this->model('id'))->get();
    }
}
