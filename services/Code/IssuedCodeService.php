<?php

namespace app\services\Code;

use app\repositories\Code\CodeRepository;
use app\repositories\Code\enums\CodeStatusEnum;
use app\services\Code\dto\IssuedCodeDto;

readonly class IssuedCodeService
{
    public function __construct(
        private CodeRepository $repository
    )
    {
    }

    public function execute(IssuedCodeDto $dto): void
    {
        $this->repository->issuedCode(
            status: CodeStatusEnum::from($dto->status->value),
            id: $dto->ids,
        );
    }
}