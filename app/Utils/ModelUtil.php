<?php
namespace App\Utils;

use Illuminate\Support\Str;

class ModelUtil
{

    public static function getRandomModeClassFromList(array $modelList): string
    {
        return $modelList[array_rand($modelList)];
    }

    public static function getRandomModelId(string $model): ?int
    {
        $modelIds = $model::all()->pluck('id')->toArray();

        if(empty($modelIds)) {
            return null;
        }

        return $modelIds[array_rand($modelIds)];
    }

    public static function getClassModelNameFromNamespace(string $namespace): string
    {
        return collect(explode('\\', $namespace))->last();
    }

    public static function generateUuid(): string
    {
        return Str::uuid()->toString();
    }
}
