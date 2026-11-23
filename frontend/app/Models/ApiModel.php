<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * A read-only model filled from API JSON (this app has no database).
 * Casts, accessors and relations work as usual in Blade; saving does not.
 */
abstract class ApiModel extends Model
{
    /** relation name => [related class, is-many] */
    protected array $apiRelations = [];

    public static function fromApi(?array $attributes): ?static
    {
        if ($attributes === null) {
            return null;
        }

        $model = new static;
        $relations = [];

        foreach ($model->apiRelations as $name => [$class, $many]) {
            if (array_key_exists($name, $attributes)) {
                $value = $attributes[$name];
                $relations[$name] = $many ? $class::collectionFromApi($value ?? []) : $class::fromApi($value);
                unset($attributes[$name]);
            }
        }

        $model->setRawAttributes($attributes, true);
        $model->exists = true;
        $model->setRelations($relations);

        return $model;
    }

    public static function collectionFromApi(array $items): Collection
    {
        return new Collection(array_map(fn (array $item) => static::fromApi($item), $items));
    }

    public function save(array $options = []): bool
    {
        throw new \LogicException(static::class.' is read-only here: change data through App\Services\Api\ApiClient.');
    }
}
