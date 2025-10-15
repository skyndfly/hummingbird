<?php

namespace app\repositories\Category;

use app\repositories\BaseRepository;
use app\repositories\Category\dto\CategoryDto;

class CategoryRepository extends BaseRepository
{
    public const string TABLE_NAME = 'category';

    public function create(string $name): void
    {
        $this->getCommand()
            ->insert(
                table: self::TABLE_NAME,
                columns: ['name' => $name]
            )
            ->execute();
    }

    /**
     * @return CategoryDto[]
     */
    public function getAll(): array
    {
        $all = $this->getQuery()
            ->from(self::TABLE_NAME)
            ->all();
        return array_map(
            callback: fn($item) => CategoryDto::fromDbRecord($item),
            array: $all
        );
    }

    /**
     * @return array<int, string>
     */
    public function getAllAsMap(): array
    {
        $categories = $this->getAll();
        $result = [];
        foreach ($categories as $category) {
            $result[$category->id] = $category->name;
        }
        return $result;
    }
}