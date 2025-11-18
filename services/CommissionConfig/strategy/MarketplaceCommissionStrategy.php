<?php

namespace app\services\CommissionConfig\strategy;

use app\repositories\CommissionConfig\CommissionConfigRepository;
use app\services\CommissionConfig\enums\CommissionStrategyTypeEnum;
use app\services\CommissionConfig\enums\CommissionTypeEnum;

readonly class MarketplaceCommissionStrategy implements CommissionStrategyInterface
{

    public function __construct(
        private CommissionConfigRepository $commissionConfigRepository
    )
    {
    }
    public function calculate(int $amount, int $quantity): int
    {
        if ($amount === 0){
            return 0;
        }
        $commission = $this->commissionConfigRepository->findForAmount(
            strategy: CommissionStrategyTypeEnum::MARKETPLACE->value,
            amount: $amount
        );
        if ($commission === null) {
            return 0;
        }
        if ($commission->type === CommissionTypeEnum::FIXED) {
            return $commission->value;
        }else if($commission->type === CommissionTypeEnum::PERCENT) {


            return $amount * ($commission->value / 100);
        }
        return 0;

    }
}