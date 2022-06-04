<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TasksTest extends TestCase
{
    use RefreshDatabase;

    private const TASKS_ENDPOINT = '/api/v1/tasks';

    public function setUp(): void
    {
        parent::setUp();
        $this->createCourse()->getOriginalContent();
        $this->createLesson()->getOriginalContent();
    }

    /** @test */
    public function not_auth_user_can_not_create_a_task()
    {
        $this
            ->withEnabledTokenAutentication()
            ->actingAs(Auth::loginUsingId(1))
            ->createTask()
            ->assertUnauthorized();
    }

    /** @test */
    public function auth_user_can_create_a_task()
    {
        $this->createTask()->assertCreated();
    }

    /** @test */
    public function auth_user_can_get_list_of_tasks()
    {
        $firstTask = $this->createTask();
        $this->getJson(self::TASKS_ENDPOINT . "/{$firstTask->getOriginalContent()->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                "id" => $firstTask->getOriginalContent()->id,
                "title" => "Test task",
                "description" => "This is test task",
            ]);

        $secondTask = $this->createTask([
            "title" => "Test task SECOND",
            "description" => "This is test task SECOND",
            "lesson_id" => 1
        ]);
        $this->getJson(self::TASKS_ENDPOINT . "/{$secondTask->getOriginalContent()->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                "id" => $secondTask->getOriginalContent()->id,
                "title" => "Test task SECOND",
                "description" => "This is test task SECOND",
            ]);

        $this->getJson(self::TASKS_ENDPOINT)
            ->assertStatus(200)
            ->assertSee('tasks')
            ->assertJsonFragment(['type' => 'tasks'])
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
    public function auth_user_can_see_specific_task()
    {
        $this->getJson(self::TASKS_ENDPOINT . "/{$this->createTask()->getOriginalContent()->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'lesson_id',
                ]
            ])
            ->assertJsonFragment([
                "title" => "Test task",
                "description" => "This is test task",
                "lesson_id" => 1
            ]);
    }

    /** @test */
    public function auth_user_can_update_a_task()
    {
        $this->patchJson(self::TASKS_ENDPOINT . "/{$this->createTask()->getOriginalContent()->id}", [
            "title" => "Test task UPDATED",
            "description" => "This is test task UPDATED",
        ])->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'lesson_id',
                ]
            ])
            ->assertJsonFragment([
                "title" => "Test task UPDATED",
                "description" => "This is test task UPDATED",
            ]);
    }

    /** @test */
    public function auth_user_can_delete_specific_task()
    {
        $task = $this->createTask()->getOriginalContent();
        $this->getJson(self::TASKS_ENDPOINT . "/{$task->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'lesson_id',
                ]
            ])
            ->assertJsonFragment([
                "title" => "Test task",
                "description" => "This is test task",
            ]);

        $this->deleteJson(self::TASKS_ENDPOINT . "/{$task->id}")
            ->assertStatus(200)->assertJsonFragment(['status' => 'Task deleted']);

        $this->getJson(self::TASKS_ENDPOINT . "/{$task->id}")->assertNotFound();
    }

    protected function createTask($data = null)
    {
        return $this->postJson(self::TASKS_ENDPOINT, $data ?? [
                "title" => "Test task",
                "description" => "This is test task",
                "lesson_id" => 1
            ]);
    }
}
