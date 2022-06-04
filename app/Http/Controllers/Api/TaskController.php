<?php

namespace App\Http\Controllers\Api;

use App\Domain\Tag\Requests\AddTagsRequest;
use App\Domain\Task\Requests\CreateTaskRequest;
use App\Domain\Task\Requests\UpdateTaskRequest;
use App\Domain\Task\TaskAggregateRoot;
use App\Http\Resources\AbstractCollection;
use App\Http\Resources\Tasks\TaskCollection;
use App\Http\Resources\Tasks\TaskResource;
use App\Models\Task;
use App\Services\TagsManager;
use App\Utils\ModelUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @OA\Get(
     *      path="/tasks",
     *      operationId="getTasks",
     *      tags={"Task"},
     *      summary="Get all tasks",
     *      description="Returns all tasks details",
     * @OA\Parameter(
     *          name="page",
     *          description="Task page",
     *          required=false,
     *          in="query",
     *          example=1,
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Parameter(
     *          name="perPage",
     *          description="Number of items to be retrieved per page",
     *          required=false,
     *          in="query",
     *          example=10,
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Parameter(
     *          name="relationships",
     *          description="Relationships that should be inclused in response",
     *          required=false,
     *          in="query",
     *          example="user;lesson",
     * @OA\Schema(
     *              type="string"
     *          )
     *      ),
     * @OA\Parameter(
     *          name="relationPerPage",
     *          description="Number of relationships to be retrieved in response structure",
     *          required=false,
     *          in="query",
     *          example=3,
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Response(
     *          response=200,
     *          description="Successfully received available tasks",
     * @OA\JsonContent(ref="#/components/schemas/Task"),
     *       ),
     * @OA\Response(response=400,                        description="Bad request"),
     * @OA\Response(response=401,                        description="Authorization token must be provided"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function index(Request $request)
    {
        return new TaskCollection(
            Task::paginate(
                (int)$request->query->get(AbstractCollection::PER_PAGE_PARAM_NAME) ??
                AbstractCollection::DEFAULT_ITEMS_NUMBER_PER_PAGE
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param                                           Task $task
     * @return                                          TaskResource
     * @OA\Get(
     *      path="/tasks/{id}",
     *      operationId="getTaskById",
     *      tags={"Task"},
     *      summary="Get a specific task",
     *      description="Returns task details",
     * @OA\Parameter(
     *          name="id",
     *          description="Task id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Response(
     *          response=200,
     *          description="Successfully received specific task",
     * @OA\JsonContent(ref="#/components/schemas/Task")
     *       ),
     * @OA\Response(response=400,                       description="Bad request"),
     * @OA\Response(response=401,                       description="Authorization token must be provided"),
     * @OA\Response(response=404,                       description="Task Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *      path="/tasks",
     *      tags={"Task"},
     *     summary="Create a new task",
     *     operationId="createTask",
     *     description="Create a new task.",
     * @OA\RequestBody(
     *         description="Create task",
     *          required=true,
     * @OA\JsonContent(ref="#/components/schemas/CreateTask")
     *     ),
     * @OA\Response(response=201,                             description="Task created"),
     * @OA\Response(response=400,                             description="Request validation error"),
     * @OA\Response(response=401,                             description="Authorization token must be provided"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                                                 CreateTaskRequest $request
     * @return                                                \Illuminate\Http\JsonResponse
     */
    public function store(CreateTaskRequest $request)
    {
        $aggregateRoot = TaskAggregateRoot::retrieve(ModelUtil::generateUuid());
        $aggregateRoot->createTask(
            $this->processCreateRequestData($request->all())
        )->persist()->snapshot();

        return (new TaskResource(
            $aggregateRoot->restoreStateFromAggregateVersion(Task::class, 1)
        ))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(path="/tasks/{id}",
     *   tags={"Task"},
     *   summary="Delete task",
     *   description="This can only be done by the logged in user.",
     *   operationId="deleteTask",
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of task to be deleted",
     *     required=true,
     * @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     * @OA\Response(response=200,     description="Task deleted"),
     * @OA\Response(response=400,     description="Invalid ID supplied"),
     * @OA\Response(response=401,     description="Authorization token must be provided"),
     * @OA\Response(response=404,     description="Task not found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                         Task $task
     * @return                        \Illuminate\Http\JsonResponse
     */
    public function destroy(Task $task)
    {
        TaskAggregateRoot::retrieve($task->uuid)->deleteTask()->persist();
        return response()->json(['status' => 'Task deleted']);
    }


    /**
     * Update the specified resource in storage.
     *
     * @OA\Patch(
     *     path="/tasks/{id}",
     *     tags={"Task"},
     *     summary="Update a specific task",
     *     operationId="updateTask",
     *     description="Update task.",
     * @OA\Parameter(
     *          name="id",
     *          description="Task id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *         description="Update task",
     *          required=true,
     * @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     * @OA\Response(response=200,                       description="Task updated"),
     * @OA\Response(response=401,                       description="Authorization token must be provided"),
     * @OA\Response(response=400,                       description="Request validation error"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                                           Task              $task
     * @param                                           UpdateTaskRequest $request
     * @return                                          \Illuminate\Http\JsonResponse
     */
    public function update(Task $task, UpdateTaskRequest $request)
    {
        $aggregateRoot = TaskAggregateRoot::retrieve($task->uuid);
        $aggregateRoot->updateTask($task, $request->all())->persist()->snapshot();

        return (new TaskResource(
            $aggregateRoot->restoreStateFromAggregateVersion(Task::class)
        ))->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/tasks/{id}/tags",
     *     tags={"Task"},
     *     summary="Update task's tags",
     *     operationId="updateTaskTags",
     *     description="Update tasks's tags.",
     * @OA\Parameter(
     *          name="id",
     *          description="Task id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *              required=true,
     *              description="Task's tags",
     * @OA\JsonContent(
     *              required={"tags"},
     * @OA\Property(
     *                  property="tags",
     *                  type="array",
     * @OA\Items(type="integer",       example=1),
     *                  description="Task's tags",
     *              ),
     * @OA\Property(property="action", type="string", example="ATTACH")
     *           ),
     *      ),
     * @OA\Response(response=200,      description="Task tags updated"),
     * @OA\Response(response=401,      description="Authorization token must be provided"),
     * @OA\Response(response=400,      description="Request validation error"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                          Task           $task
     * @param                          AddTagsRequest $request
     * @return                         \Illuminate\Http\JsonResponse
     */
    public function processTags(Task $task, AddTagsRequest $request)
    {
        TagsManager::processModelTags($task, $request);
        return response()->json(['status' => 'Task tags updated']);
    }

    /**
     * @param                                           Task $task
     * @param                                           int  $aggregateVersion
     * @return                                          TaskResource
     * @OA\Get(
     *      path="/tasks/{task}/restore/{aggregateVersion}",
     *      operationId="restoreTaskByAggregateVarsion",
     *      tags={"Task"},
     *      summary="Restore task state by aggregate version",
     *      description="Returns restored task state by aggregate version",
     * @OA\Parameter(
     *          name="task",
     *          description="Task id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Parameter(
     *          name="aggregateVersion",
     *          description="Number of aggregate version",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Response(
     *          response=200,
     *          description="Successfully received aggregated task state",
     * @OA\JsonContent(ref="#/components/schemas/Task")
     *       ),
     * @OA\Response(response=400,                       description="Bad request"),
     * @OA\Response(response=401,                       description="Authorization token must be provided"),
     * @OA\Response(response=404,                       description="Task Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function restoreModel(Task $task, int $aggregateVersion)
    {
        return new TaskResource(
            TaskAggregateRoot::retrieve($task->uuid)
                ->restoreStateFromAggregateVersion(Task::class, $aggregateVersion)
        );
    }
}
