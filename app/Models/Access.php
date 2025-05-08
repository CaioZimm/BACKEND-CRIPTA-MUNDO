<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    use HasFactory;
    protected $table = 'access';
    protected $fillable = [
        'user_id',
        'post_id'
    ];

    public function user(){
        return $this->belongsTo(User::class); // UM acesso PERTENCE à UM User
    }
    public function posts(){
        return $this->belongsTo(Post::class); // UM acesso PERTENCE à UM Post
    }
}
