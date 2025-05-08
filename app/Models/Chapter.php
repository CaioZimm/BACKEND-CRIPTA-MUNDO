<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;
    protected $table = 'chapters';
    protected $fillable = [
        'post_id',
        'title',
        'content',
        'image',
    ];

    public function posts() {
        return $this->belongsTo(Post::class); // UM Capitulo PERTENCE à UM Post;
    }
}
