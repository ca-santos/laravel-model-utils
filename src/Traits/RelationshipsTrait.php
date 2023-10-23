<?php

namespace CaueSantos\LaravelModelUtils\Traits;

use ErrorException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

trait RelationshipsTrait
{

    /**
     * @return array
     * @throws ReflectionException
     */
    public function relationships(): array
    {

        $model = new static;

        $relationships = [];

        foreach ((new ReflectionClass($model))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {

            if ((!empty($method->getParameters()) ||
                $method->getName() == __FUNCTION__)) {
                continue;
            }

            if ($method->hasReturnType() && is_subclass_of($method->getReturnType()->getName(), \Illuminate\Database\Eloquent\Relations\Relation::class)) {

                /** @var BelongsTo|HasOne $return */
                $return = $method->invoke($model);
                $relationType = (new ReflectionClass($return))->getName();

                $foreignKey = $return->getRelated()->getForeignKey();

                if (is_subclass_of($relationType, BelongsTo::class)) {
                    $foreignKey = $return->getForeignKeyName();
                }

                if (is_subclass_of($relationType, HasOne::class)) {
                    $foreignKey = $return->getLocalKeyName();
                }

                try {
                    $pivot = [
                        'table' => $return->getTable() ?? null,
                        'foreign_key' => $return->getForeignPivotKeyName() ?? null,
                        'related_key' => $return->getRelatedPivotKeyName() ?? null
                    ];
                } catch (\Exception $e) {
                    $pivot = [];
                }

                $relationships[$method->getName()] = [
                    'name' => $method->getName(),
                    'type' => (new ReflectionClass($return))->getShortName(),
                    'model' => ($getModel = (new ReflectionClass($return->getRelated())))->getName(),
                    'table' => $return->getRelated()->getTable(),
                    'primary' => $return->getRelated()->getKeyName(),
                    'foreign_key' => $foreignKey,
                    'pivot' => $pivot,
                    'computed' => collect($getModel->getMethods())->flatMap(function (ReflectionMethod $m) {
                        if (
                            Str::startsWith($name = $m->getName(), 'get') &&
                            Str::endsWith($name, 'Attribute') &&
                            $name !== 'getAttribute'
                        ) {
                            return [
                                Str::snake(Str::substrReplace(Str::substrReplace($name, '', 0, 3), '', -9)) => $name
                            ];
                        }
                        return null;
                    })->whereNotNull()->toArray()
                ];

            }

        }

        return $relationships;
    }

    /**
     * @param $relation_name
     * @return array|bool
     * @throws ReflectionException
     */
    public function hasDefinedRelation($relation_name): array|bool
    {

        $relationsToLook = explode('.', $relation_name);
        $result = [];

        $model = $this;
        foreach ($relationsToLook as $relationToLook) {

            if (isset($model->relationships()[$relationToLook])) {
                $result[$relationToLook] = $model->relationships()[$relationToLook];
                $model = new $result[$relationToLook]['model'];
            } else {
                break;
            }

        }

        return count($result) === count($relationsToLook) ? $result : false;

    }

}
