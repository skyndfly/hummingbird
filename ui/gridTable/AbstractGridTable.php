<?php

namespace app\ui\gridTable;

use ReflectionClass;
use ReflectionProperty;

abstract class AbstractGridTable
{
    /** Возвращает конфигурацию колонок для GridView */
    public static function getColumns(): array
    {
        $ref = new ReflectionClass(static::class);

        $properties = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
        $columns = [];
        foreach ($properties as $prop) {
            $attrs = $prop->getAttributes(GridColumn::class);
            /** @var GridColumn $meta */
            $meta = $attrs[0]->newInstance();
            $attributeName = $prop->getName();
            $callback = $meta->formatter !== null
                ? [static::class, $meta->formatter] // вызов метода
                : fn($model) => $model->{$attributeName};// просто свойство DTO

            $columns[] = [
                'attribute' => $attributeName,
                'label' => $meta->label,
                'value' => $callback,
                'format' => 'raw',
                //                'enableSorting' => $meta->sortable,
                //                'sortAttribute' => $attributeName,
                //                'sortAttribute' => $meta->sortAttribute ?? $attributeName,
            ];
        }
        return $columns;
    }

    /** Возвращает массив атрибутов, которые можно сортировать */
    public static function getSortAttributes(): array
    {
        $ref = new ReflectionClass(static::class);
        $properties = $ref->getProperties(ReflectionProperty::IS_PUBLIC);

        $sortAttributes = [];
        foreach ($properties as $prop) {
            $attrs = $prop->getAttributes(GridColumn::class);

            /** @var GridColumn $meta */
            $meta = $attrs[0]->newInstance();
            if ($meta->sortable) {
                $sortAttributes[] = $prop->getName();
            }
        }

        return $sortAttributes;
    }
}