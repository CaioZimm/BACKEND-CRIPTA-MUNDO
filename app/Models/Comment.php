<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';
    protected $fillable = [
        'user_id',
        'post_id',
        'description'
    ];

    public function posts() {
        return $this->belongsTo(Post::class); // UM comentario PERTENCE à UM Post;
    }
    public function user() {
        return $this->belongsTo(User::class); // UM comentario PERTENCE à UM User
    }
    public function likeComments() {
        return $this->hasMany(LikeComment::class); // UM Comentario pode POSSUIR MUITOS Likes
    }
}
