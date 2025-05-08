<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikePost extends Model
{
    use HasFactory;
    protected $table = 'like_posts';

    public function user(){
        return $this->belongsTo(User::class); // UM like PERTENCE à UM User
    }
    public function posts(){
        return $this->belongsTo(Post::class, 'post_id'); // UM like PERTENCE à UM Post
    }
}
