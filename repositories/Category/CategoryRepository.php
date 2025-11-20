<?php

namespace app\repositories\Category;

use app\repositories\BaseRepository;
use app\repositories\Category\dto\CategoryDto;
use app\services\Category\exceptions\CategoryNotFoundException;

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

    /**
     * @throws CategoryNotFoundException
     */
    public function getById(int $categoryId): CategoryDto
    {

        $record = $this->getQuery()
            ->from(self::TABLE_NAME)
            ->where(['id' => $categoryId])
            ->one();
        if ($record === false) {
            throw new CategoryNotFoundException($categoryId);
        }
        return CategoryDto::fromDbRecord($record);
    }

    public function isNameExist(string $name, ?int $id = null): bool
    {
        $query = $this->getQuery()
            ->from(self::TABLE_NAME)
            ->where(['LOWER(name)' => trim(mb_strtolower($name))]);
        if ($id !== null) {
            $query->andWhere(['!=', 'id', $id]);
        }
        return $query->exists();
    }

    public function updateName(string $name, int $id): void
    {
        $this->getCommand()
            ->update(
                table: self::TABLE_NAME,
                columns: ['name' => $name],
                condition: ['id' => $id]

            )
            ->execute();
    }
}