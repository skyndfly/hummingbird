<?php

namespace app\repositories\Code\dto;


class CodeSearchDto
{
    public function __construct(
        public ?string $code = null,
        public ?string $date = null,
        public ?int $categoryId = null,
    )
    {
    }
}