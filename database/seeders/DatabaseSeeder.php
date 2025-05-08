<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Chapter;
use App\Models\Comment;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        Post::factory(3)->create();
        Chapter::factory(10)->create();
        Comment::factory(10)->create();
    }
}
