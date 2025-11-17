<?php

namespace app\services\CommissionConfig\strategy;

use app\repositories\CommissionConfig\CommissionConfigRepository;
use app\services\CommissionConfig\enums\CommissionStrategyTypeEnum;
use app\services\CommissionConfig\enums\CommissionTypeEnum;

readonly class SdekCommissionStrategy implements CommissionStrategyInterface
{
    public function __construct(
        private CommissionConfigRepository $commissionConfigRepository
    )
    {
    }
    public function calculate(int $amount, int $quantity): int
    {
        $commission = $this->commissionConfigRepository->findForFixed(CommissionStrategyTypeEnum::FIXED_SDEK->value);
        if ($commission === null) {
            return 0;
        }
        if ($commission->type === CommissionTypeEnum::PER_UNIT){
            return $quantity * $commission->value;
        }
        return 0;
    }
}