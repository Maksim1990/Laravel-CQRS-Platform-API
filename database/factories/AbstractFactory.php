<?php

namespace Database\Factories;

use App\Utils\ModelUtil;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

abstract class AbstractFactory extends Factory
{
    protected function getPolymorphicModelClass(array $modelList): ?string
    {
        return ModelUtil::getRandomModeClassFromList($modelList);
    }

    protected function getPolymorphicModelIdAndType(array $modelList): array
    {
        do {
            $commentableId = ModelUtil::getRandomModelId(
                $commentableType = $this->getPolymorphicModelClass($modelList)
            );
        } while ($commentableId === null);

        return [$commentableId, $commentableType];
    }

    protected function generateModelEvents(string $modelName)
    {
        return $this->afterMaking(function ($model) use ($modelName) {
            /** @var AggregateRoot $aggregateRoot */
            $aggregateRoot = 'App\Domain\\' . ucfirst($modelName) . '\\' . ucfirst($modelName) . 'AggregateRoot';
            $aggregateRoot::retrieve($model->uuid)->{'create' . ucfirst($modelName)}(
                $model->toArray(),
                false
            )->persist();
        });
    }
}
