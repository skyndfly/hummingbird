<?php

namespace app\repositories\Code\dto;

class GroupedCodeList
{
    /** @var GroupedCodeDto[] */
    private array $rows = [];
    private int $unpaidTotal = 0;
    /** @var int[] */
    private array $ids = [];

    public function addRows(GroupedCodeDto $dto): void
    {
        $this->rows[] = $dto ;
        $this->unpaidTotal += $dto->unpaidTotal;
        $this->ids[] = $dto->id;
    }

    /**
     * @return GroupedCodeDto[]
     */
    public function getRows(): array{
        return $this->rows;
    }

    public function getUnpaidTotal():int
    {
        return $this->unpaidTotal / 100;
    }

    /**
     * @return int[]
     */
    public function getIds(): array{
        return $this->ids;
    }
}