<?php

namespace app\services\CommissionConfig\strategy;

use app\repositories\CommissionConfig\CommissionConfigRepository;
use app\services\CommissionConfig\enums\CommissionStrategyTypeEnum;
use app\services\CommissionConfig\enums\CommissionTypeEnum;

class MarketplaceCommissionStrategy implements CommissionStrategyInterface
{

    public function __construct(
        private CommissionConfigRepository $commissionConfigRepository
    )
    {
    }
    public function calculate(int $amount, int $quantity): int
    {

        $strategy = $this->commissionConfigRepository->findForAmount(
            strategy: CommissionStrategyTypeEnum::MARKETPLACE->value,
            amount: $amount
        );

        if ($strategy->type === CommissionTypeEnum::FIXED) {
            return $strategy->value;
        }else if($strategy->type === CommissionTypeEnum::PERCENT) {


            return $amount * ($strategy->value / 100);
        }
        return 0;

    }
}