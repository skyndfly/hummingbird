<?php

namespace app\services\CommissionConfig\strategy;

use app\repositories\CommissionConfig\CommissionConfigRepository;
use app\services\CommissionConfig\enums\CommissionStrategyTypeEnum;
use app\services\CommissionConfig\enums\CommissionTypeEnum;

class DefaultCommissionStrategy implements CommissionStrategyInterface
{

    public function calculate(int $amount, int $quantity): int
    {

        return $amount;

    }
}