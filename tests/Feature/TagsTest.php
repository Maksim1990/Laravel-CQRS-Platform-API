<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TagsTest extends TestCase
{
    use RefreshDatabase;

    private const TAGS_ENDPOINT = '/api/v1/tags';

    public function setUp(): void
    {
        parent::setUp();
        $this->createCourse()->getOriginalContent();
        $this->createLesson()->getOriginalContent();
    }

    /** @test */
    public function not_auth_user_can_not_create_a_tag()
    {
        $this
            ->withEnabledTokenAutentication()
            ->actingAs(Auth::loginUsingId(1))
            ->createTag()
            ->assertUnauthorized();
    }

    /** @test */
    public function auth_user_can_create_a_tag()
    {
        $this->createTag()->assertCreated();
    }

    /** @test */
    public function auth_user_can_get_list_of_tags()
    {
        $firstTag = $this->createTag();
        $this->getJson(self::TAGS_ENDPOINT . "/{$firstTag->getOriginalContent()->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                "id" => $firstTag->getOriginalContent()->id,
                "name" => "Test FIRST tag",
                "description" => "Test FIRST description tag",
            ]);

        $secondTag = $this->createTag([
            "name" => "Test FIRST lesson tag",
            "description" => "Test FIRST lesson description tag",
        ]);

        $this->getJson(self::TAGS_ENDPOINT . "/{$secondTag->getOriginalContent()->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                "id" => $secondTag->getOriginalContent()->id,
                "name" => "Test FIRST lesson tag",
                "description" => "Test FIRST lesson description tag",
            ]);

        $this->getJson(self::TAGS_ENDPOINT)
            ->assertStatus(200)
            ->assertSee('tags')
            ->assertJsonFragment(['type' => 'tags'])
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
    public function auth_user_can_see_specific_tag()
    {
        $this->getJson(self::TAGS_ENDPOINT . "/{$this->createTag()->getOriginalContent()->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'user_id',
                ]
            ])
            ->assertJsonFragment([
                "name" => "Test FIRST tag",
                "description" => "Test FIRST description tag",
            ]);
    }

    /** @test */
    public function auth_user_can_update_a_tag()
    {
        $this->patchJson(self::TAGS_ENDPOINT . "/{$this->createTag()->getOriginalContent()->id}", [
            "name" => "Test FIRST tag UPDATED",
            "description" => "Test FIRST description tag UPDATED",
        ])->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'user_id',
                ]
            ])
            ->assertJsonFragment([
                "name" => "Test FIRST tag UPDATED",
                "description" => "Test FIRST description tag UPDATED",
            ]);
    }

    /** @test */
    public function auth_user_can_delete_specific_tag()
    {
        $tag = $this->createTag()->getOriginalContent();
        $this->getJson(self::TAGS_ENDPOINT . "/{$tag->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'user_id',
                ]
            ])
            ->assertJsonFragment([
                "name" => "Test FIRST tag",
                "description" => "Test FIRST description tag",
            ]);

        $this->deleteJson(self::TAGS_ENDPOINT . "/{$tag->id}")
            ->assertStatus(200)->assertJsonFragment(['status' => 'Tag deleted']);

        $this->getJson(self::TAGS_ENDPOINT . "/{$tag->id}")->assertNotFound();
    }

    protected function createTag($data = null)
    {
        return $this->postJson(self::TAGS_ENDPOINT, $data ?? [
                "name" => "Test FIRST tag",
                "description" => "Test FIRST description tag",
                "type" => "COURSE",
                "model_id" => 1
            ]);
    }
}
