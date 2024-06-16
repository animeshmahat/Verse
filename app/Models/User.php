<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'image',
        'password',
        'username',
        'mobile',
        'status',
        'role',
    ];

    public function getRules(array $validate)
    {
        return validator($validate, [
            'name'          => 'required|max:40|min:2',
            'email'         => 'required|email|unique:users',
            'image'         => 'image|required|mimes:jpeg,png,jpg,gif,svg|max:5000',
            'password'      => 'required|min:6|max:50|same:confirm-password',
            'username'      => 'required|username|unique:users',
            'mobile'        => 'required|max:20|min:7|mobile|unique:users',
            'status'        => 'boolean'
        ]);
    }
    public function posts()
    {
        return $this->hasMany(Posts::class);
    }
    public function getPost()
    {
        return DB::table('posts')->get();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
