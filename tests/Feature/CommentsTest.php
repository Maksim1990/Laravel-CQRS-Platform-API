<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CommentsTest extends TestCase
{
    use RefreshDatabase;

    private const COMMENTS_ENDPOINT = '/api/v1/comments';

    public function setUp(): void
    {
        parent::setUp();
        $this->createCourse()->getOriginalContent();
        $this->createLesson()->getOriginalContent();
    }

    /** @test */
    public function not_auth_user_can_not_create_a_comment()
    {
        $this
            ->withEnabledTokenAutentication()
            ->actingAs(Auth::loginUsingId(1))
            ->createComment()
            ->assertUnauthorized();
    }

    /** @test */
    public function auth_user_can_create_a_comment()
    {
        $this->createComment()->assertCreated();
    }

    /** @test */
    public function auth_user_can_get_list_of_comments()
    {
        $firstComment = $this->createComment();
        $this->getJson(self::COMMENTS_ENDPOINT . "/{$firstComment->getOriginalContent()->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                "id" => $firstComment->getOriginalContent()->id,
                "message" => "My first comment",
                "type"=> "COURSE",
                "model_id"=> 1,
            ]);

        $secondComment = $this->createComment([
            "message" => "My SECOND comment",
            "type" => "LESSON",
            "model_id" => 1
        ]);
        $this->getJson(self::COMMENTS_ENDPOINT . "/{$secondComment->getOriginalContent()->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                "id" => $secondComment->getOriginalContent()->id,
                "message" => "My SECOND comment",
                "type" => "LESSON",
                "model_id" => 1
            ]);

        $this->getJson(self::COMMENTS_ENDPOINT)
            ->assertStatus(200)
            ->assertSee('comments')
            ->assertJsonFragment(['type' => 'comments'])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                    ]
                ]
            ]);
    }

    /** @test */
    public function auth_user_can_see_specific_comment()
    {
        $this->getJson(self::COMMENTS_ENDPOINT . "/{$this->createComment()->getOriginalContent()->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'message',
                    'type',
                    'model_id',
                    'user_id',
                ]
            ])
            ->assertJsonFragment([
                "message" => "My first comment",
                "type" => "COURSE",
                "model_id" => 1
            ]);
    }

    /** @test */
    public function auth_user_can_update_a_comment()
    {
        $this->patchJson(self::COMMENTS_ENDPOINT . "/{$this->createComment()->getOriginalContent()->id}", [
            "message" => "My first comment UPDATED",
        ])->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'message',
                    'type',
                    'model_id',
                    'user_id',
                ]
            ])
            ->assertJsonFragment(["message" => "My first comment UPDATED"]);
    }

    /** @test */
    public function auth_user_can_delete_specific_comment()
    {
        $comment = $this->createComment()->getOriginalContent();
        $this->getJson(self::COMMENTS_ENDPOINT . "/{$comment->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'message',
                    'type',
                    'model_id',
                    'user_id',
                ]
            ])
            ->assertJsonFragment([
                "message" => "My first comment",
                "type" => "COURSE",
                "model_id" => 1
            ]);

        $this->deleteJson(self::COMMENTS_ENDPOINT . "/{$comment->id}")
            ->assertStatus(200)->assertJsonFragment(['status' => 'Comment deleted']);

        $this->getJson(self::COMMENTS_ENDPOINT . "/{$comment->id}")->assertNotFound();
    }

    protected function createComment($data = null)
    {
        return $this->postJson(self::COMMENTS_ENDPOINT, $data ?? [
                "message" => "My first comment",
                "type" => "COURSE",
                "model_id" => 1
            ]);
    }
}
