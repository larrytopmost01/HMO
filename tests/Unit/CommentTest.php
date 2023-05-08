<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\Comment;


class CommentTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_comment_for_user()
    {
    
        DB::table('comments')->insert([
            [
                'user_id' => '2',
                'comment' => 'this is the comment for you',
            ]
        ]);

        $this->assertTrue(true);
     


    }
}
