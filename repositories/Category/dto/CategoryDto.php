<?php

namespace app\repositories\Category\dto;

class CategoryDto
{
    public function __construct(
        public int $id,
        public string $name,
    )
    {
    }

    /**
     * @param array{
     *     id: int,
     *     name: string
     * } $record
     */
    public static function fromDbRecord(array $record): CategoryDto
    {
        return new self(
            id: $record['id'],
            name: $record['name'],
        );
    }
}