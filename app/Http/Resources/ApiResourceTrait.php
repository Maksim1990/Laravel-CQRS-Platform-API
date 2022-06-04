<?php

namespace App\Http\Resources;

use App\Models\AbstractModel;
use Illuminate\Http\Request;

trait ApiResourceTrait
{
    private function buildRelationStructure(
        Request $request,
        string $relation,
        $model,
        int $perPage
    ): array {
        return [
            'data' => $model->{$relation}()->paginate($perPage)->map(
                function ($item) {
                    return $item->makeHidden(['pivot'])->toArray();
                }
            )->toArray(),
            'links' => [
                'self' => sprintf(
                    '%s/api/%s/%s/%s/%s',
                    $request->getSchemeAndHttpHost(),
                    $this->collectionName,
                    $model->id,
                    AbstractCollection::RELATIONS_PARAM_NAME,
                    $relation
                ),
                'related' => sprintf(
                    '%s/api/%s/%s/%s',
                    $request->getSchemeAndHttpHost(),
                    $this->collectionName,
                    $model->id,
                    $relation
                ),
            ]
        ];
    }

    private function processRequestData(array $resp, Request $request, AbstractModel $model = null): array
    {
        if (($relations = $request->query->get(AbstractCollection::RELATIONS_PARAM_NAME)) === null) {
            return $resp;
        }

        if($model === null) {
            $model = $this;
        }

        foreach (explode(';', $relations) as $relation) {

            if (!isset($model->$relation)) {
                continue;
            }

            $resp[AbstractCollection::RELATIONS_PARAM_NAME][$relation] = $this->buildRelationStructure(
                $request,
                $relation,
                $model,
                $request->query->get(AbstractCollection::RELATIONS_PER_PAGE_PARAM_NAME)
                ?? AbstractCollection::DEFAULT_ITEMS_NUMBER_PER_PAGE
            );
        }

        return $resp;
    }
}
