<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikeComment extends Model
{
    use HasFactory;
    protected $table = 'like_comments';
    public function user(){
        return $this->belongsTo(User::class); // UM like PERTENCE à UM User
    }
    public function comments(){
        return $this->belongsTo(Comment::class, 'comment_id'); // UM like PERTENCE à UM Comentário
    }
}
