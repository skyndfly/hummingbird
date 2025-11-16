<?php

namespace app\services\Code\dto;

use app\repositories\Code\enums\CodeStatusEnum;

class IssuedCodeDto
{
    /** @param int[] $ids */
    public function __construct(
        public array $ids,
        public CodeStatusEnum $status,
    )
    {
    }
}