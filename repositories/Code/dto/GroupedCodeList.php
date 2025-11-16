<?php

namespace app\repositories\Code\dto;

use app\repositories\Code\enums\CodeStatusEnum;

class GroupedCodeList
{
    /** @var GroupedCodeDto[] */
    private array $rows = [];
    private int $unpaidTotal = 0;
    /** @var int[] */
    private array $ids = [];
    private int $totalQuantity = 0;
    private string $code;
    /**
     * @var string[]
     */
    private array $storages = [];


    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }


    public function addRows(GroupedCodeDto $dto): void
    {
        $this->rows[] = $dto ;
        $this->unpaidTotal += $dto->unpaidTotal;
        $this->ids[] = $dto->id;
        if($dto->status === CodeStatusEnum::NEW){
            $this->totalQuantity += $dto->quantity;
        }
        $this->storages[] = $dto->categoryName;
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
    public function getTotalQuantity(): int{
        return $this->totalQuantity;
    }

    /**
     * @return string[]
     */
    public function getStorages():array
    {
        return $this->storages;
    }
}