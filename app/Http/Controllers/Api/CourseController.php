<?php

namespace App\Http\Controllers\Api;

use App\Domain\Course\CourseAggregateRoot;
use App\Domain\Course\Requests\CreateCourseRequest;
use App\Domain\Course\Requests\UpdateCourseRequest;
use App\Domain\Tag\Requests\AddTagsRequest;
use App\Filters\SlugToSnakeCase;
use App\Http\Resources\AbstractCollection;
use App\Http\Resources\Courses\CourseCollection;
use App\Http\Resources\Courses\CourseResource;
use App\Models\Course;
use App\Services\TagsManager;
use App\Utils\ModelUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Pipeline;

class CourseController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @OA\Get(
     *      path="/courses",
     *      operationId="getCourses",
     *      tags={"Course"},
     *      summary="Get all courses",
     *      description="Returns all courses details",
     * @OA\Parameter(
     *          name="page",
     *          description="Current page",
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
     *          example="videos;lessons;courses;user",
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
     *          description="Successfully received available courses",
     * @OA\JsonContent(ref="#/components/schemas/Course"),
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
        return new CourseCollection(
            Course::search($this->getSearchQueryValue($request) ?? '')
            ->paginate(
                (int)$request->query->get(AbstractCollection::PER_PAGE_PARAM_NAME) ??
                AbstractCollection::DEFAULT_ITEMS_NUMBER_PER_PAGE
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param                                             Course $course
     * @return                                            CourseResource
     * @OA\Get(
     *      path="/courses/{slug}",
     *      operationId="getCourseById",
     *      tags={"Course"},
     *      summary="Get a specific course",
     *      description="Returns course details",
     * @OA\Parameter(
     *          name="slug",
     *          description="Course slug",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="string"
     *          )
     *      ),
     * @OA\Response(
     *          response=200,
     *          description="Successfully received specific course",
     * @OA\JsonContent(ref="#/components/schemas/Course")
     *       ),
     * @OA\Response(response=400,                         description="Bad request"),
     * @OA\Response(response=401,                         description="Authorization token must be provided"),
     * @OA\Response(response=404,                         description="Course Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function show(Course $course)
    {
        return new CourseResource($course);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *      path="/courses",
     *      tags={"Course"},
     *     summary="Create a new course",
     *     operationId="createCourse",
     *     description="Create a new course.",
     * @OA\RequestBody(
     *         description="Create product",
     *          required=true,
     * @OA\JsonContent(ref="#/components/schemas/CreateCourse")
     *     ),
     * @OA\Response(response=201,                               description="Course created"),
     * @OA\Response(response=400,                               description="Request validation error"),
     * @OA\Response(response=401,                               description="Authorization token must be provided"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                                                   CreateCourseRequest $request
     * @return                                                  \Illuminate\Http\JsonResponse
     */
    public function store(CreateCourseRequest $request)
    {
        $filteredData = app(Pipeline::class)
            ->send($request->all())
            ->through(
                [
                SlugToSnakeCase::class
                ]
            )
            ->thenReturn();

        $aggregateRoot = CourseAggregateRoot::retrieve(ModelUtil::generateUuid());
        $aggregateRoot->createCourse($this->processCreateRequestData($filteredData))->persist()->snapshot();

        return (new CourseResource(
            $aggregateRoot->restoreStateFromAggregateVersion(
                Course::class,
                1
            )
        ))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(path="/courses/{id}",
     *   tags={"Course"},
     *   summary="Delete course",
     *   description="This can only be done by the logged in user.",
     *   operationId="deleteCourse",
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of course to be deleted",
     *     required=true,
     * @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     * @OA\Response(response=200,       description="Course deleted"),
     * @OA\Response(response=400,       description="Invalid ID supplied"),
     * @OA\Response(response=401,       description="Authorization token must be provided"),
     * @OA\Response(response=404,       description="Course not found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                           Course $course
     * @return                          \Illuminate\Http\JsonResponse
     */
    public function destroy(Course $course)
    {
        CourseAggregateRoot::retrieve($course->uuid)->deleteCourse()->persist();
        return response()->json(['status' => 'Course deleted']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Patch(
     *     path="/courses/{id}",
     *     tags={"Course"},
     *     summary="Update a specific course",
     *     operationId="updateCourse",
     *     description="Update course.",
     * @OA\Parameter(
     *          name="id",
     *          description="Course id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *         description="Update course",
     *          required=true,
     * @OA\JsonContent(ref="#/components/schemas/Course")
     *     ),
     * @OA\Response(response=200,                         description="Course updated"),
     * @OA\Response(response=401,                         description="Authorization token must be provided"),
     * @OA\Response(response=400,                         description="Request validation error"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function update(Course $course, UpdateCourseRequest $request)
    {
        $requestData = collect($request->all())->filter(
            function ($item, $key) {
                return $key !== 'slug';
            }
        );

        $aggregateRoot = CourseAggregateRoot::retrieve($course->uuid);
        $aggregateRoot->updateCourse($course, $requestData->all())->persist()->snapshot();

        return (new CourseResource(
            $aggregateRoot->restoreStateFromAggregateVersion(Course::class)
        ))->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/courses/{id}/tags",
     *     tags={"Course"},
     *     summary="Update course's tags",
     *     operationId="updateCourseTags",
     *     description="Update course's tags.",
     * @OA\Parameter(
     *          name="id",
     *          description="Course id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *              required=true,
     *              description="Course's tags",
     * @OA\JsonContent(
     *              required={"tags"},
     * @OA\Property(
     *                  property="tags",
     *                  type="array",
     * @OA\Items(type="integer",       example=1),
     *                  description="Course's tags",
     *              ),
     * @OA\Property(property="action", type="string", example="ATTACH")
     *           ),
     *      ),
     * @OA\Response(response=200,      description="Course tags updated"),
     * @OA\Response(response=401,      description="Authorization token must be provided"),
     * @OA\Response(response=400,      description="Request validation error"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                          Course         $course
     * @param                          AddTagsRequest $request
     * @return                         \Illuminate\Http\JsonResponse
     */
    public function processTags(Course $course, AddTagsRequest $request)
    {
        TagsManager::processModelTags($course, $request);
        return response()->json(['status' => 'Course tags updated']);
    }


    /**
     * @param                                             Course $course
     * @param                                             int    $aggregateVersion
     * @return                                            CourseResource
     * @OA\Get(
     *      path="/courses/{course}/restore/{aggregateVersion}",
     *      operationId="restoreCourseByAggregateVarsion",
     *      tags={"Course"},
     *      summary="Restore course state by aggregate version",
     *      description="Returns restored course state by aggregate version",
     * @OA\Parameter(
     *          name="course",
     *          description="Course slug",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="string"
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
     *          description="Successfully received aggregated course state",
     * @OA\JsonContent(ref="#/components/schemas/Course")
     *       ),
     * @OA\Response(response=400,                         description="Bad request"),
     * @OA\Response(response=401,                         description="Authorization token must be provided"),
     * @OA\Response(response=404,                         description="Course Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function restoreModel(Course $course, int $aggregateVersion)
    {
        return new CourseResource(
            CourseAggregateRoot::retrieve($course->uuid)
                ->restoreStateFromAggregateVersion(Course::class, $aggregateVersion)
        );
    }

    public function search(Request $request, string $searchQuery)
    {
        return new CourseCollection(
            Course::search($searchQuery)->paginate(
                (int)$request->query->get(AbstractCollection::PER_PAGE_PARAM_NAME) ??
                AbstractCollection::DEFAULT_ITEMS_NUMBER_PER_PAGE
            )
        );
    }
}
