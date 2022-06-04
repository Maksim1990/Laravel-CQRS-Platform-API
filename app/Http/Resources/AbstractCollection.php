<?php

namespace App\Http\Resources;

use App\Mappings\MorphableMapping;
use App\Models\AbstractModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

abstract class AbstractCollection extends ResourceCollection
{
    use ApiResourceTrait;

    public const DEFAULT_ITEMS_NUMBER_PER_PAGE = 10;
    public const PER_PAGE_PARAM_NAME = 'perPage';
    public const RELATIONS_PARAM_NAME = 'relationships';
    public const RELATIONS_PER_PAGE_PARAM_NAME = 'relationPerPage';

    protected string $collectionName;

    protected function setCollectionName(string $collectionName)
    {
        $this->collectionName = $collectionName;
    }

    // http://localhost:7000/api/courses?page=1&perPage=1&relationships=videos;lessons&relationPerPage=1
    protected function buildDataStructure(Collection $collection, Request $request): Collection
    {
        return $collection->map(
            function (AbstractModel $model) use ($request) {

                $resp = [
                'type' => $this->collectionName,
                'id' => $model->id,
                ];

                if (isset($model->slug)) {
                    $resp['slug'] = $model->slug;
                }

                $resp['attributes'] = $this->filterAttributesStructure(
                    $model->makeHidden(['id', 'slug'])->toArray()
                );

                return $this->processRequestData($resp, $request, $model);
            }
        );
    }

    private function filterAttributesStructure(array $attributes): array
    {
        return collect($attributes)->keyBy(
            function ($value, $key) {
                if(in_array($key, MorphableMapping::MORHABLE_FILEDS)) {
                    return MorphableMapping::MORHABLE_MAPPING_LIST[$key];
                }
                return  $key;
            }
        )->toArray();
    }
}
