<?php

namespace App\Http\Controllers\Api;

use App\Domain\Comment\CommentAggregateRoot;
use App\Domain\Comment\Requests\CreateCommentRequest;
use App\Domain\Comment\Requests\UpdateCommentRequest;
use App\Http\Resources\AbstractCollection;
use App\Http\Resources\Comments\CommentCollection;
use App\Http\Resources\Comments\CommentResource;
use App\Models\Comment;
use App\Utils\ModelUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @OA\Get(
     *      path="/comments",
     *      operationId="getComments",
     *      tags={"Comment"},
     *      summary="Get all comments",
     *      description="Returns all comments details",
     * @OA\Parameter(
     *          name="page",
     *          description="Comment page",
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
     *          description="Successfully received available comments",
     * @OA\JsonContent(ref="#/components/schemas/Comment"),
     *       ),
     * @OA\Response(response=400,                           description="Bad request"),
     * @OA\Response(response=401,                           description="Authorization token must be provided"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function index(Request $request)
    {
        return new CommentCollection(
            Comment::paginate(
                (int)$request->query->get(AbstractCollection::PER_PAGE_PARAM_NAME) ??
                AbstractCollection::DEFAULT_ITEMS_NUMBER_PER_PAGE
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param                                              Comment $comment
     * @return                                             CommentResource
     * @OA\Get(
     *      path="/comments/{id}",
     *      operationId="getCommentById",
     *      tags={"Comment"},
     *      summary="Get a specific comment",
     *      description="Returns comment details",
     * @OA\Parameter(
     *          name="id",
     *          description="Comment id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Response(
     *          response=200,
     *          description="Successfully received specific comment",
     * @OA\JsonContent(ref="#/components/schemas/Comment")
     *       ),
     * @OA\Response(response=400,                          description="Bad request"),
     * @OA\Response(response=401,                          description="Authorization token must be provided"),
     * @OA\Response(response=404,                          description="Comment Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function show(Comment $comment)
    {
        return new CommentResource($comment);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *      path="/comments",
     *      tags={"Comment"},
     *     summary="Create a new comment",
     *     operationId="createComment",
     *     description="Create a new comment.",
     * @OA\RequestBody(
     *         description="Create comment",
     *          required=true,
     * @OA\JsonContent(ref="#/components/schemas/CreateComment")
     *     ),
     * @OA\Response(response=201,                                description="Comment created"),
     * @OA\Response(response=400,                                description="Request validation error"),
     * @OA\Response(response=401,                                description="Authorization token must be provided"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                                                    CreateCommentRequest $request
     * @return                                                   \Illuminate\Http\JsonResponse
     */
    public function store(CreateCommentRequest $request)
    {
        $aggregateRoot = CommentAggregateRoot::retrieve(ModelUtil::generateUuid());

        $aggregateRoot->createComment(
            $this->preparePolymorphRequestData(
                $this->processCreateRequestData($request->all()),
                'commentable'
            )
        )->persist()->snapshot();

        return (new CommentResource(
            $aggregateRoot->restoreStateFromAggregateVersion(Comment::class, 1)
        ))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(path="/comments/{id}",
     *   tags={"Comment"},
     *   summary="Delete comment",
     *   description="This can only be done by the logged in user.",
     *   operationId="deleteComment",
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of comment to be deleted",
     *     required=true,
     * @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     * @OA\Response(response=200,        description="Comment deleted"),
     * @OA\Response(response=400,        description="Invalid ID supplied"),
     * @OA\Response(response=401,        description="Authorization token must be provided"),
     * @OA\Response(response=404,        description="Comment not found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                            Comment $comment
     * @return                           \Illuminate\Http\JsonResponse
     */
    public function destroy(Comment $comment)
    {
        CommentAggregateRoot::retrieve($comment->uuid)->deleteComment()->persist();
        return response()->json(['status' => 'Comment deleted']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Patch(
     *     path="/comments/{id}",
     *     tags={"Comment"},
     *     summary="Update a specific comment",
     *     operationId="updateComment",
     *     description="Update comment.",
     * @OA\Parameter(
     *          name="id",
     *          description="Comment id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *         description="Update comment",
     *          required=true,
     * @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     * @OA\Response(response=200,                          description="Comment updated"),
     * @OA\Response(response=401,                          description="Authorization token must be provided"),
     * @OA\Response(response=400,                          description="Request validation error"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                                              Comment              $comment
     * @param                                              UpdateCommentRequest $request
     * @return                                             \Illuminate\Http\JsonResponse
     */
    public function update(Comment $comment, UpdateCommentRequest $request)
    {
        $aggregateRoot = CommentAggregateRoot::retrieve($comment->uuid);
        $aggregateRoot
            ->updateComment(
                $comment,  $this->preparePolymorphRequestData(
                    $this->processCreateRequestData($request->all()),
                    'commentable'
                )
            )
            ->persist()
            ->snapshot();

        return (new CommentResource(
            $aggregateRoot->restoreStateFromAggregateVersion(Comment::class)
        ))->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @param                                              Comment $comment
     * @param                                              int     $aggregateVersion
     * @return                                             CommentResource
     * @OA\Get(
     *      path="/comments/{comment}/restore/{aggregateVersion}",
     *      operationId="restoreCommentByAggregateVarsion",
     *      tags={"Comment"},
     *      summary="Restore comment state by aggregate version",
     *      description="Returns restored comment state by aggregate version",
     * @OA\Parameter(
     *          name="comment",
     *          description="Comment id",
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
     *          description="Successfully received aggregated comment state",
     * @OA\JsonContent(ref="#/components/schemas/Comment")
     *       ),
     * @OA\Response(response=400,                          description="Bad request"),
     * @OA\Response(response=401,                          description="Authorization token must be provided"),
     * @OA\Response(response=404,                          description="Comment Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function restoreModel(Comment $comment, int $aggregateVersion)
    {
        return new CommentResource(
            CommentAggregateRoot::retrieve($comment->uuid)
                ->restoreStateFromAggregateVersion(Comment::class, $aggregateVersion)
        );
    }
}
