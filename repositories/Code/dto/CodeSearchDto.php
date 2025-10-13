<?php

namespace app\repositories\Code\dto;


class CodeSearchDto
{
    public function __construct(
        public ?string $code = null,
        public ?string $date = null,
        public ?string $place = null,
    )
    {
    }
}