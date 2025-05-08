<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\Rules;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'usertype',
        'photo'
    ];

    public function posts() {
        return $this->hasMany(Post::class); // UM Usuario pode POSSUIR MUITOS Posts;
    }
    public function comments() {
        return $this->hasMany(Comment::class); // UM Usuario pode POSSUIR MUITOS Comentarios;
    }
    public function access() {
        return $this->hasMany(Access::class); // UM Usuario pode POSSUIR MUITOS Acessos;
    }
    public function likePosts() {
        return $this->hasMany(LikePost::class); // UM Usuario pode POSSUIR MUITOS Likes em Postagens;
    }
    public function likeComments() {
        return $this->hasMany(LikeComment::class); // UM Usuario pode POSSUIR MUITOS Likes em Comentários;
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
