<?php

namespace App\Http\Controllers\Api;

use App\Domain\Tag\Requests\AddTagsRequest;
use App\Domain\Video\Requests\CreateVideoRequest;
use App\Domain\Video\Requests\UpdateVideoRequest;
use App\Domain\Video\VideoAggregateRoot;
use App\Http\Resources\AbstractCollection;
use App\Http\Resources\Videos\VideoCollection;
use App\Http\Resources\Videos\VideoResource;
use App\Models\Video;
use App\Services\TagsManager;
use App\Utils\ModelUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VideoController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @OA\Get(
     *      path="/videos",
     *      operationId="getVideos",
     *      tags={"Video"},
     *      summary="Get all videos",
     *      description="Returns all videos details",
     * @OA\Parameter(
     *          name="page",
     *          description="Video page",
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
     *          example="lesson;user",
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
     *          description="Successfully received available videos",
     * @OA\JsonContent(ref="#/components/schemas/Video"),
     *       ),
     * @OA\Response(response=400,                         description="Bad request"),
     * @OA\Response(response=401,                         description="Authorization token must be provided"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function index(Request $request)
    {
        return new VideoCollection(
            Video::search($this->getSearchQueryValue($request) ?? '')
            ->paginate(
                (int)$request->query->get(AbstractCollection::PER_PAGE_PARAM_NAME) ??
                AbstractCollection::DEFAULT_ITEMS_NUMBER_PER_PAGE
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param                                            Video $video
     * @return                                           VideoResource
     * @OA\Get(
     *      path="/videos/{id}",
     *      operationId="getVideoById",
     *      tags={"Video"},
     *      summary="Get a specific video",
     *      description="Returns video details",
     * @OA\Parameter(
     *          name="id",
     *          description="Video id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Response(
     *          response=200,
     *          description="Successfully received specific video",
     * @OA\JsonContent(ref="#/components/schemas/Video")
     *       ),
     * @OA\Response(response=400,                        description="Bad request"),
     * @OA\Response(response=401,                        description="Authorization token must be provided"),
     * @OA\Response(response=404,                        description="Video Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function show(Video $video)
    {
        return new VideoResource($video);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *      path="/videos",
     *      tags={"Video"},
     *     summary="Create a new video",
     *     operationId="createLesson",
     *     description="Create a new video.",
     * @OA\RequestBody(
     *         description="Create video",
     *          required=true,
     * @OA\JsonContent(ref="#/components/schemas/CreateVideo")
     *     ),
     * @OA\Response(response=201,                              description="Video created"),
     * @OA\Response(response=400,                              description="Request validation error"),
     * @OA\Response(response=401,                              description="Authorization token must be provided"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                                                  CreateVideoRequest $request
     * @return                                                 \Illuminate\Http\JsonResponse
     */
    public function store(CreateVideoRequest $request)
    {
        $aggregateRoot = VideoAggregateRoot::retrieve(ModelUtil::generateUuid());
        $aggregateRoot->createVideo(
            $this->processCreateRequestData($request->all())
        )->persist()->snapshot();

        return (new VideoResource(
            $aggregateRoot->restoreStateFromAggregateVersion(Video::class, 1)
        ))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(path="/videos/{id}",
     *   tags={"Video"},
     *   summary="Delete video",
     *   description="This can only be done by the logged in user.",
     *   operationId="deleteVideo",
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of video to be deleted",
     *     required=true,
     * @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     * @OA\Response(response=200,      description="Video deleted"),
     * @OA\Response(response=400,      description="Invalid ID supplied"),
     * @OA\Response(response=401,      description="Authorization token must be provided"),
     * @OA\Response(response=404,      description="Video not found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                          Video $video
     * @return                         \Illuminate\Http\JsonResponse
     */
    public function destroy(Video $video)
    {
        VideoAggregateRoot::retrieve($video->uuid)->deleteVideo()->persist();
        return response()->json(['status' => 'Video deleted']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Patch(
     *     path="/videos/{id}",
     *     tags={"Video"},
     *     summary="Update a specific video",
     *     operationId="updateVideo",
     *     description="Update video.",
     * @OA\Parameter(
     *          name="id",
     *          description="Video id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *         description="Update video",
     *          required=true,
     * @OA\JsonContent(ref="#/components/schemas/Video")
     *     ),
     * @OA\Response(response=200,                        description="Video updated"),
     * @OA\Response(response=401,                        description="Authorization token must be provided"),
     * @OA\Response(response=400,                        description="Request validation error"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                                            Video              $video
     * @param                                            UpdateVideoRequest $request
     * @return                                           \Illuminate\Http\JsonResponse
     */
    public function update(Video $video, UpdateVideoRequest $request)
    {
        $aggregateRoot = VideoAggregateRoot::retrieve($video->uuid);
        $aggregateRoot->updateVideo($video, $request->all())->persist()->snapshot();

        return (new VideoResource(
            $aggregateRoot->restoreStateFromAggregateVersion(Video::class)
        ))->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/videos/{id}/tags",
     *     tags={"Video"},
     *     summary="Update video's tags",
     *     operationId="updateVideoTags",
     *     description="Update video's tags.",
     * @OA\Parameter(
     *          name="id",
     *          description="Video id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *              required=true,
     *              description="Video's tags",
     * @OA\JsonContent(
     *              required={"tags"},
     * @OA\Property(
     *                  property="tags",
     *                  type="array",
     * @OA\Items(type="integer",       example=1),
     *                  description="Video's tags",
     *              ),
     * @OA\Property(property="action", type="string", example="ATTACH")
     *           ),
     *      ),
     * @OA\Response(response=200,      description="Video tags updated"),
     * @OA\Response(response=401,      description="Authorization token must be provided"),
     * @OA\Response(response=400,      description="Request validation error"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                          Video          $video
     * @param                          AddTagsRequest $request
     * @return                         \Illuminate\Http\JsonResponse
     */
    public function processTags(Video $video, AddTagsRequest $request)
    {
        TagsManager::processModelTags($video, $request);
        return response()->json(['status' => 'Video tags updated']);
    }

    /**
     * @param                                            Video $video
     * @param                                            int   $aggregateVersion
     * @return                                           VideoResource
     * @OA\Get(
     *      path="/videos/{video}/restore/{aggregateVersion}",
     *      operationId="restoreVideoByAggregateVarsion",
     *      tags={"Video"},
     *      summary="Restore video state by aggregate version",
     *      description="Returns restored video state by aggregate version",
     * @OA\Parameter(
     *          name="video",
     *          description="Video id",
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
     *          description="Successfully received aggregated video state",
     * @OA\JsonContent(ref="#/components/schemas/Video")
     *       ),
     * @OA\Response(response=400,                        description="Bad request"),
     * @OA\Response(response=401,                        description="Authorization token must be provided"),
     * @OA\Response(response=404,                        description="Video Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function restoreModel(Video $video, int $aggregateVersion)
    {
        return new VideoResource(
            VideoAggregateRoot::retrieve($video->uuid)
                ->restoreStateFromAggregateVersion(Video::class, $aggregateVersion)
        );
    }
}
