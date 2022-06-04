<?php

namespace App\Http\Controllers\Api;

use App\Domain\Tag\Requests\CreateTagRequest;
use App\Domain\Tag\Requests\UpdateTagRequest;
use App\Domain\Tag\TagAggregateRoot;
use App\Http\Resources\AbstractCollection;
use App\Http\Resources\Tags\TagCollection;
use App\Http\Resources\Tags\TagResource;
use App\Models\Tag;
use App\Utils\ModelUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TagController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @OA\Get(
     *      path="/tags",
     *      operationId="getTags",
     *      tags={"Tag"},
     *      summary="Get all tags",
     *      description="Returns all tags details",
     * @OA\Parameter(
     *          name="page",
     *          description="Tag page",
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
     *          example="user",
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
     *          description="Successfully received available tags",
     * @OA\JsonContent(ref="#/components/schemas/Tag"),
     *       ),
     * @OA\Response(response=400,                       description="Bad request"),
     * @OA\Response(response=401,                       description="Authorization token must be provided"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function index(Request $request)
    {
        //DB::table('taggables')->where('tag_id', 1)->delete();
        return new TagCollection(
            Tag::paginate(
                (int)$request->query->get(AbstractCollection::PER_PAGE_PARAM_NAME) ??
                AbstractCollection::DEFAULT_ITEMS_NUMBER_PER_PAGE
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param                                          Tag $tag
     * @return                                         TagResource
     * @OA\Get(
     *      path="/tags/{id}",
     *      operationId="getTagById",
     *      tags={"Tag"},
     *      summary="Get a specific tag",
     *      description="Returns tag details",
     * @OA\Parameter(
     *          name="id",
     *          description="Tag id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Response(
     *          response=200,
     *          description="Successfully received specific tag",
     * @OA\JsonContent(ref="#/components/schemas/Tag")
     *       ),
     * @OA\Response(response=400,                      description="Bad request"),
     * @OA\Response(response=401,                      description="Authorization token must be provided"),
     * @OA\Response(response=404,                      description="Tag Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function show(Tag $tag)
    {
        return new TagResource($tag);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *      path="/tags",
     *      tags={"Tag"},
     *     summary="Create a new tag",
     *     operationId="createTag",
     *     description="Create a new tag.",
     * @OA\RequestBody(
     *         description="Create tag",
     *          required=true,
     * @OA\JsonContent(ref="#/components/schemas/CreateTag")
     *     ),
     * @OA\Response(response=201,                            description="Tag created"),
     * @OA\Response(response=400,                            description="Request validation error"),
     * @OA\Response(response=401,                            description="Authorization token must be provided"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                                                CreateTagRequest $request
     * @return                                               \Illuminate\Http\JsonResponse
     */
    public function store(CreateTagRequest $request)
    {
        $requestData = $this->processPolymorphManyRequestData($request->all(), Tag::class);
        $aggregateRoot = TagAggregateRoot::retrieve($requestData['uuid'] ?? ModelUtil::generateUuid());
        $aggregateRoot->createTag($requestData, false)->persist()->snapshot();

        return (new TagResource(
            $aggregateRoot->restoreStateFromAggregateVersion(Tag::class, 1)
        ))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(path="/tags/{id}",
     *   tags={"Tag"},
     *   summary="Delete tag",
     *   description="This can only be done by the logged in user.",
     *   operationId="deleteTag",
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of tag to be deleted",
     *     required=true,
     * @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     * @OA\Response(response=200,    description="Tag deleted"),
     * @OA\Response(response=400,    description="Invalid ID supplied"),
     * @OA\Response(response=401,    description="Authorization token must be provided"),
     * @OA\Response(response=404,    description="Tag not found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                        Tag $tag
     * @return                       \Illuminate\Http\JsonResponse
     */
    public function destroy(Tag $tag)
    {
        TagAggregateRoot::retrieve($tag->uuid)->deleteTag($tag)->persist();
        return response()->json(['status' => 'Tag deleted']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Patch(
     *     path="/tags/{id}",
     *     tags={"Tag"},
     *     summary="Update a specific tag",
     *     operationId="updateTag",
     *     description="Update tag.",
     * @OA\Parameter(
     *          name="id",
     *          description="Tag id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *         description="Update tag",
     *          required=true,
     * @OA\JsonContent(ref="#/components/schemas/Tag")
     *     ),
     * @OA\Response(response=200,                      description="Tag updated"),
     * @OA\Response(response=401,                      description="Authorization token must be provided"),
     * @OA\Response(response=400,                      description="Request validation error"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                                          Tag              $tag
     * @param                                          UpdateTagRequest $request
     * @return                                         \Illuminate\Http\JsonResponse
     */
    public function update(Tag $tag, UpdateTagRequest $request)
    {
        $aggregateRoot = TagAggregateRoot::retrieve($tag->uuid);
        $aggregateRoot->updateTag($tag, $this->deleteMorphFields($request->all()))->persist()->snapshot();

        return (new TagResource(
            $aggregateRoot->restoreStateFromAggregateVersion(Tag::class)
        ))->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @param                                          Tag $tag
     * @param                                          int $aggregateVersion
     * @return                                         TagResource
     * @OA\Get(
     *      path="/tags/{tag}/restore/{aggregateVersion}",
     *      operationId="restoreTagByAggregateVarsion",
     *      tags={"Tag"},
     *      summary="Restore tag state by aggregate version",
     *      description="Returns restored tag state by aggregate version",
     * @OA\Parameter(
     *          name="tag",
     *          description="Tag id",
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
     *          description="Successfully received aggregated tag state",
     * @OA\JsonContent(ref="#/components/schemas/Tag")
     *       ),
     * @OA\Response(response=400,                      description="Bad request"),
     * @OA\Response(response=401,                      description="Authorization token must be provided"),
     * @OA\Response(response=404,                      description="Tag Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function restoreModel(Tag $tag, int $aggregateVersion)
    {
        return new TagResource(
            TagAggregateRoot::retrieve($tag->uuid)
                ->restoreStateFromAggregateVersion(Tag::class, $aggregateVersion)
        );
    }
}
