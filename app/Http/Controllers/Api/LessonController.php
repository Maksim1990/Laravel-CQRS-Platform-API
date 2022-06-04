<?php

namespace App\Http\Controllers\Api;

use App\Domain\Lesson\LessonAggregateRoot;
use App\Domain\Lesson\Requests\CreateLessonRequest;
use App\Domain\Lesson\Requests\UpdateLessonRequest;
use App\Domain\Tag\Requests\AddTagsRequest;
use App\Http\Resources\AbstractCollection;
use App\Http\Resources\Lessons\LessonCollection;
use App\Http\Resources\Lessons\LessonResource;
use App\Models\Lesson;
use App\Services\TagsManager;
use App\Utils\ModelUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LessonController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @OA\Get(
     *      path="/lessons",
     *      operationId="getLessons",
     *      tags={"Lesson"},
     *      summary="Get all lessons",
     *      description="Returns all lessons details",
     * @OA\Parameter(
     *          name="page",
     *          description="Lesson page",
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
     *          example="videos;tasks;comments;user",
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
     *          description="Successfully received available lessons",
     * @OA\JsonContent(ref="#/components/schemas/Lesson"),
     *       ),
     * @OA\Response(response=400,                          description="Bad request"),
     * @OA\Response(response=401,                          description="Authorization token must be provided"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function index(Request $request)
    {
        return new LessonCollection(
            Lesson::search($this->getSearchQueryValue($request) ?? '')
            ->paginate(
                (int)$request->query->get(AbstractCollection::PER_PAGE_PARAM_NAME) ??
                AbstractCollection::DEFAULT_ITEMS_NUMBER_PER_PAGE
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param                                             Lesson $lesson
     * @return                                            LessonResource
     * @OA\Get(
     *      path="/lessons/{id}",
     *      operationId="getLessonById",
     *      tags={"Lesson"},
     *      summary="Get a specific lesson",
     *      description="Returns lesson details",
     * @OA\Parameter(
     *          name="id",
     *          description="Lesson id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Response(
     *          response=200,
     *          description="Successfully received specific lesson",
     * @OA\JsonContent(ref="#/components/schemas/Lesson")
     *       ),
     * @OA\Response(response=400,                         description="Bad request"),
     * @OA\Response(response=401,                         description="Authorization token must be provided"),
     * @OA\Response(response=404,                         description="Lesson Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function show(Lesson $lesson)
    {
        return new LessonResource($lesson);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *      path="/lessons",
     *      tags={"Lesson"},
     *     summary="Create a new lesson",
     *     operationId="createLesson",
     *     description="Create a new lesson.",
     * @OA\RequestBody(
     *         description="Create lesson",
     *          required=true,
     * @OA\JsonContent(ref="#/components/schemas/CreateLesson")
     *     ),
     * @OA\Response(response=201,                               description="Lesson created"),
     * @OA\Response(response=400,                               description="Request validation error"),
     * @OA\Response(response=401,                               description="Authorization token must be provided"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                                                   CreateLessonRequest $request
     * @return                                                  \Illuminate\Http\JsonResponse
     */
    public function store(CreateLessonRequest $request)
    {
        $aggregateRoot = LessonAggregateRoot::retrieve(ModelUtil::generateUuid());
        $aggregateRoot->createLesson(
            $this->processCreateRequestData($request->all())
        )->persist()->snapshot();

        return (new LessonResource(
            $aggregateRoot->restoreStateFromAggregateVersion(Lesson::class, 1)
        ))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(path="/lessons/{id}",
     *   tags={"Lesson"},
     *   summary="Delete lesson",
     *   description="This can only be done by the logged in user.",
     *   operationId="deleteLesson",
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of lesson to be deleted",
     *     required=true,
     * @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     * @OA\Response(response=200,       description="Lesson deleted"),
     * @OA\Response(response=400,       description="Invalid ID supplied"),
     * @OA\Response(response=401,       description="Authorization token must be provided"),
     * @OA\Response(response=404,       description="Lesson not found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                           Lesson $lesson
     * @return                          \Illuminate\Http\JsonResponse
     */
    public function destroy(Lesson $lesson)
    {
        LessonAggregateRoot::retrieve($lesson->uuid)->deleteLesson()->persist();
        return response()->json(['status' => 'Lesson deleted']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Patch(
     *     path="/lessons/{id}",
     *     tags={"Lesson"},
     *     summary="Update a specific lesson",
     *     operationId="updateLesson",
     *     description="Update lesson.",
     * @OA\Parameter(
     *          name="id",
     *          description="Lesson id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *         description="Update lesson",
     *          required=true,
     * @OA\JsonContent(ref="#/components/schemas/Lesson")
     *     ),
     * @OA\Response(response=200,                         description="Lesson updated"),
     * @OA\Response(response=401,                         description="Authorization token must be provided"),
     * @OA\Response(response=400,                         description="Request validation error"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                                             Lesson              $lesson
     * @param                                             UpdateLessonRequest $request
     * @return                                            \Illuminate\Http\JsonResponse
     */
    public function update(Lesson $lesson, UpdateLessonRequest $request)
    {
        $aggregateRoot = LessonAggregateRoot::retrieve($lesson->uuid);
        $aggregateRoot->updateLesson($lesson, $request->all())->persist()->snapshot();

        return (new LessonResource(
            $aggregateRoot->restoreStateFromAggregateVersion(Lesson::class)
        ))->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/lessons/{id}/tags",
     *     tags={"Lesson"},
     *     summary="Update lesson's tags",
     *     operationId="updateLessonTags",
     *     description="Update lesson's tags.",
     * @OA\Parameter(
     *          name="id",
     *          description="Lesson id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *              required=true,
     *              description="Lesson's tags",
     * @OA\JsonContent(
     *              required={"tags"},
     * @OA\Property(
     *                  property="tags",
     *                  type="array",
     * @OA\Items(type="integer",       example=1),
     *                  description="Lesson's tags",
     *              ),
     * @OA\Property(property="action", type="string", example="ATTACH")
     *           ),
     *      ),
     * @OA\Response(response=200,      description="Lesson tags updated"),
     * @OA\Response(response=401,      description="Authorization token must be provided"),
     * @OA\Response(response=400,      description="Request validation error"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                          Lesson         $lesson
     * @param                          AddTagsRequest $request
     * @return                         \Illuminate\Http\JsonResponse
     */
    public function processTags(Lesson $lesson, AddTagsRequest $request)
    {
        TagsManager::processModelTags($lesson, $request);
        return response()->json(['status' => 'Lesson tags updated']);
    }

    /**
     * @param                                             Lesson $lesson
     * @param                                             int    $aggregateVersion
     * @return                                            LessonResource
     * @OA\Get(
     *      path="/lessons/{lesson}/restore/{aggregateVersion}",
     *      operationId="restoreLessonByAggregateVarsion",
     *      tags={"Lesson"},
     *      summary="Restore lesson state by aggregate version",
     *      description="Returns restored lesson state by aggregate version",
     * @OA\Parameter(
     *          name="lesson",
     *          description="Lesson id",
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
     *          description="Successfully received aggregated lesson state",
     * @OA\JsonContent(ref="#/components/schemas/Lesson")
     *       ),
     * @OA\Response(response=400,                         description="Bad request"),
     * @OA\Response(response=401,                         description="Authorization token must be provided"),
     * @OA\Response(response=404,                         description="Lesson Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function restoreModel(Lesson $lesson, int $aggregateVersion)
    {
        return new LessonResource(
            LessonAggregateRoot::retrieve($lesson->uuid)
                ->restoreStateFromAggregateVersion(Lesson::class, $aggregateVersion)
        );
    }
}
