<?php

namespace App\Http\Controllers\Api;

use App\Domain\Section\Requests\CreateSectionRequest;
use App\Domain\Section\Requests\UpdateSectionRequest;
use App\Domain\Section\SectionAggregateRoot;
use App\Http\Resources\AbstractCollection;
use App\Http\Resources\Sections\SectionCollection;
use App\Http\Resources\Sections\SectionResource;
use App\Models\Section;
use App\Utils\ModelUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SectionController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @OA\Get(
     *      path="/sections",
     *      operationId="getSections",
     *      tags={"Section"},
     *      summary="Get all sections",
     *      description="Returns all sections details",
     * @OA\Parameter(
     *          name="page",
     *          description="Section page",
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
     *          example="course",
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
     *          description="Successfully received available sections",
     * @OA\JsonContent(ref="#/components/schemas/Section"),
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
        return new SectionCollection(
            Section::paginate(
                (int)$request->query->get(AbstractCollection::PER_PAGE_PARAM_NAME) ??
                AbstractCollection::DEFAULT_ITEMS_NUMBER_PER_PAGE
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param                                              Section $section
     * @return                                             SectionResource
     * @OA\Get(
     *      path="/sections/{id}",
     *      operationId="getSectionById",
     *      tags={"Section"},
     *      summary="Get a specific section",
     *      description="Returns section details",
     * @OA\Parameter(
     *          name="id",
     *          description="Section id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Response(
     *          response=200,
     *          description="Successfully received specific section",
     * @OA\JsonContent(ref="#/components/schemas/Section")
     *       ),
     * @OA\Response(response=400,                          description="Bad request"),
     * @OA\Response(response=401,                          description="Authorization token must be provided"),
     * @OA\Response(response=404,                          description="Section Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function show(Section $section)
    {
        return new SectionResource($section);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *      path="/sections",
     *      tags={"Section"},
     *     summary="Create a new section",
     *     operationId="createSection",
     *     description="Create a new section.",
     * @OA\RequestBody(
     *         description="Create section",
     *          required=true,
     * @OA\JsonContent(ref="#/components/schemas/CreateSection")
     *     ),
     * @OA\Response(response=201,                                description="Section created"),
     * @OA\Response(response=400,                                description="Request validation error"),
     * @OA\Response(response=401,                                description="Authorization token must be provided"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                                                    CreateSectionRequest $request
     * @return                                                   \Illuminate\Http\JsonResponse
     */
    public function store(CreateSectionRequest $request)
    {
        $aggregateRoot = SectionAggregateRoot::retrieve(ModelUtil::generateUuid());
        $aggregateRoot->createSection(
            $this->processCreateRequestData($request->all())
        )->persist()->snapshot();

        return (new SectionResource(
            $aggregateRoot->restoreStateFromAggregateVersion(Section::class, 1)
        ))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(path="/sections/{id}",
     *   tags={"Section"},
     *   summary="Delete section",
     *   description="This can only be done by the logged in user.",
     *   operationId="deleteSection",
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of sections to be deleted",
     *     required=true,
     * @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     * @OA\Response(response=200,        description="Section deleted"),
     * @OA\Response(response=400,        description="Invalid ID supplied"),
     * @OA\Response(response=401,        description="Authorization token must be provided"),
     * @OA\Response(response=404,        description="Section not found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                            Section $section
     * @return                           \Illuminate\Http\JsonResponse
     */
    public function destroy(Section $section)
    {
        SectionAggregateRoot::retrieve($section->uuid)->deleteSection()->persist();
        return response()->json(['status' => 'Section deleted']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Patch(
     *     path="/sections/{id}",
     *     tags={"Section"},
     *     summary="Update a specific section",
     *     operationId="updateSection",
     *     description="Update section.",
     * @OA\Parameter(
     *          name="id",
     *          description="Section id",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *         description="Update section",
     *          required=true,
     * @OA\JsonContent(ref="#/components/schemas/Section")
     *     ),
     * @OA\Response(response=200,                          description="Section updated"),
     * @OA\Response(response=401,                          description="Authorization token must be provided"),
     * @OA\Response(response=400,                          description="Request validation error"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     * @param                                              Section              $section
     * @param                                              UpdateSectionRequest $request
     * @return                                             \Illuminate\Http\JsonResponse
     */
    public function update(Section $section, UpdateSectionRequest $request)
    {
        $aggregateRoot = SectionAggregateRoot::retrieve($section->uuid);
        $aggregateRoot->updateSection($section, $request->all())->persist()->snapshot();

        return (new SectionResource(
            $aggregateRoot->restoreStateFromAggregateVersion(Section::class)
        ))->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @param                                              Section $section
     * @param                                              int     $aggregateVersion
     * @return                                             SectionResource
     * @OA\Get(
     *      path="/sections/{section}/restore/{aggregateVersion}",
     *      operationId="restoreSectionByAggregateVarsion",
     *      tags={"Section"},
     *      summary="Restore section state by aggregate version",
     *      description="Returns restored section state by aggregate version",
     * @OA\Parameter(
     *          name="section",
     *          description="Section id",
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
     *          description="Successfully received aggregated section state",
     * @OA\JsonContent(ref="#/components/schemas/Section")
     *       ),
     * @OA\Response(response=400,                          description="Bad request"),
     * @OA\Response(response=401,                          description="Authorization token must be provided"),
     * @OA\Response(response=404,                          description="Section Not Found"),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function restoreModel(Section $section, int $aggregateVersion)
    {
        return new SectionResource(
            SectionAggregateRoot::retrieve($section->uuid)
                ->restoreStateFromAggregateVersion(Section::class, $aggregateVersion)
        );
    }
}
