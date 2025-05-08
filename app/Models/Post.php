<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $table = 'posts';
    protected $fillable = [
        'user_id',
        'title',
        'caption',
        'description',
        'image'
    ];

    public function chapters() {
        return $this->hasMany(Chapter::class); // UM Post pode POSSUIR MUITOS Capitulos
    }
    public function comments() {
        return $this->hasMany(Comment::class); // UM Post pode POSSUIR MUITOS Comentarios
    }
    public function likePosts() {
        return $this->hasMany(LikePost::class); // UM Post pode POSSUIR MUITOS Likes
    }
    public function access() {
        return $this->hasMany(Access::class); // UM Post pode POSSUIR MUITOS Acessos
    }
    public function user() {
        return $this->belongsTo(User::class); // UM Post PERTENCE à UM Usuario;
    }
}