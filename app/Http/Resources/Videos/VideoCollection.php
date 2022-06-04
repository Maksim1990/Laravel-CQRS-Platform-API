<?php

namespace App\Http\Resources\Videos;

use App\Http\Resources\AbstractCollection;

class VideoCollection extends AbstractCollection
{
    private const RESOURCE_COLLECTION_NAME = 'videos';

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $this->setCollectionName(self::RESOURCE_COLLECTION_NAME);

        return [
            'data' => $this->buildDataStructure($this->collection, $request),
        ];
    }
}
