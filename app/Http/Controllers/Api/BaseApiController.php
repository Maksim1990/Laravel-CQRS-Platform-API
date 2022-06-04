<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mappings\MorphableMapping;
use App\Services\TagsManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="WEBMASTERY SCHOOL API DOCS",
 *      description="Webmastery School Portal API documentation",
 * @OA\Contact(
 *          email="narushevich.maksim@gmail.com"
 *      ),
 * @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API documentation"
 * )
 * @OA\SecurityScheme(
 * @OA\Flow(
 *         flow="clientCredentials",
 *         tokenUrl="oauth/token",
 *         scopes={}
 *     ),
 *     securityScheme="bearerAuth",
 *     in="header",
 *     type="http",
 *     description="Oauth2 security",
 *     name="oauth2",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */
class BaseApiController extends Controller
{
    private const MODEL_NAMESPACE = 'App\Models\\';
    private const SEARCH_QUERY_NAME = 'search';

    protected function preparePolymorphRequestData(array $requestData, string $morphField): array
    {
        if (isset($requestData[MorphableMapping::DEFAULT_MORPHABLE_ID])) {
            $requestData[$morphField . '_id'] = $requestData[MorphableMapping::DEFAULT_MORPHABLE_ID];
        }
        if (isset($requestData[MorphableMapping::DEFAULT_MORPHABLE_TYPE])) {
            $requestData[$morphField . '_type'] =
                self::MODEL_NAMESPACE . ucfirst(strtolower($requestData[MorphableMapping::DEFAULT_MORPHABLE_TYPE]));
        }

        return $this->deleteMorphFields($requestData);
    }

    protected function processCreateRequestData(array $requestData): array
    {
        if (!isset($requestData['user_id'])) {
            $requestData['user_id'] = Auth::id();
        }

        return $requestData;
    }

    protected function processPolymorphManyRequestData(array $requestData, string $morphableModel): array
    {
        $modelId = $requestData[MorphableMapping::DEFAULT_MORPHABLE_ID] ?? null;
        if ($modelId !== null) {
            $model = (self::MODEL_NAMESPACE . ucfirst(
                strtolower(
                    $requestData[MorphableMapping::DEFAULT_MORPHABLE_TYPE]
                )
            )
            )::where('id', $modelId)->first();
            if ($model === null) {
                throw (new ModelNotFoundException())->setModel(
                    (self::MODEL_NAMESPACE . ucfirst(strtolower($requestData[MorphableMapping::DEFAULT_MORPHABLE_TYPE]))),
                    [$modelId]
                );
            }
        }

        $requestData = $this->deleteMorphFields($requestData);
        $requestData['uuid'] = Str::uuid()->toString();
        $requestData = $this->processCreateRequestData($requestData);
        $newModel = $morphableModel::create($requestData);

        if ($modelId !== null) {
            if (strtolower(class_basename($model)) === 'course') {
                TagsManager::attachModelTags($model, [$newModel->id]);
                return $requestData;
            }
            $model->tags()->save($newModel);
        }

        return $requestData;
    }

    protected function deleteMorphFields(array $requestData): array
    {
        unset($requestData[MorphableMapping::DEFAULT_MORPHABLE_ID]);
        unset($requestData[MorphableMapping::DEFAULT_MORPHABLE_TYPE]);

        return $requestData;
    }

    protected function getSearchQueryValue(Request $request): ?string
    {
        return $request->query->get(self::SEARCH_QUERY_NAME);
    }
}
