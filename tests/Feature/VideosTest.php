<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class VideosTest extends TestCase
{
    use RefreshDatabase;

    private const VIDEOS_ENDPOINT = '/api/v1/videos';

    public function setUp(): void
    {
        parent::setUp();
        $this->createCourse()->getOriginalContent();
        $this->createLesson()->getOriginalContent();
    }

    /** @test */
    public function not_auth_user_can_not_create_a_video()
    {
        $this
            ->withEnabledTokenAutentication()
            ->actingAs(Auth::loginUsingId(1))
            ->createVideo()
            ->assertUnauthorized();
    }

    /** @test */
    public function auth_user_can_create_a_video()
    {
        $this->createVideo()->assertCreated();
    }

    /** @test */
    public function auth_user_can_get_list_of_videos()
    {
        $firstVideo = $this->createVideo();
        $this->getJson(self::VIDEOS_ENDPOINT . "/{$firstVideo->getOriginalContent()->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                "id" => $firstVideo->getOriginalContent()->id,
                "title" => "Lesson's first VIDEO",
                "description" => "This is a first video",
                "link" => "http://example.com"
            ]);

        $secondVideo = $this->createVideo([
            "title" => "Lesson's second VIDEO",
            "description" => "This is a second video",
            "link" => "http://example.com",
            "lesson_id" => 1
        ]);
        $this->getJson(self::VIDEOS_ENDPOINT . "/{$secondVideo->getOriginalContent()->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                "id" => $secondVideo->getOriginalContent()->id,
                "title" => "Lesson's second VIDEO",
                "description" => "This is a second video",
                "link" => "http://example.com",
            ]);

        $this->getJson(self::VIDEOS_ENDPOINT)
            ->assertStatus(200)
            ->assertSee('videos')
            ->assertJsonFragment(['type' => 'videos'])
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
    public function auth_user_can_see_specific_video()
    {
        $this->getJson(self::VIDEOS_ENDPOINT . "/{$this->createVideo()->getOriginalContent()->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'lesson_id',
                    'link',
                ]
            ])
            ->assertJsonFragment([
                "title" => "Lesson's first VIDEO",
                "description" => "This is a first video",
                "link" => "http://example.com"
            ]);
    }

    /** @test */
    public function auth_user_can_update_a_video()
    {
        $this->patchJson(self::VIDEOS_ENDPOINT . "/{$this->createVideo()->getOriginalContent()->id}", [
            "title" => "Lesson's first VIDEO UPDATED",
            "description" => "This is a first video UPDATED",
        ])->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'lesson_id',
                    'link',
                ]
            ])
            ->assertJsonFragment([
                "title" => "Lesson's first VIDEO UPDATED",
                "description" => "This is a first video UPDATED",
            ]);
    }

    /** @test */
    public function auth_user_can_delete_specific_video()
    {
        $video = $this->createVideo()->getOriginalContent();
        $this->getJson(self::VIDEOS_ENDPOINT . "/{$video->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'lesson_id',
                    'link',
                ]
            ])
            ->assertJsonFragment([
                "title" => "Lesson's first VIDEO",
                "description" => "This is a first video",
            ]);

        $this->deleteJson(self::VIDEOS_ENDPOINT . "/{$video->id}")
            ->assertStatus(200)->assertJsonFragment(['status' => 'Video deleted']);

        $this->getJson(self::VIDEOS_ENDPOINT . "/{$video->id}")->assertNotFound();
    }

    protected function createVideo($data = null)
    {
        return $this->postJson(self::VIDEOS_ENDPOINT, $data ?? [
                "title" => "Lesson's first VIDEO",
                "description" => "This is a first video",
                "lesson_id" => 1,
                "link" => "http://example.com"
            ]);
    }
}
