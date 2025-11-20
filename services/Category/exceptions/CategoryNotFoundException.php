<?php

namespace app\services\Category\exceptions;

use LogicException;

class CategoryNotFoundException extends LogicException
{
    public function __construct(int $categoryId)
    {
        parent::__construct("Категория {$categoryId} не найдена");
    }
}