<?php

namespace app\services\CommissionConfig\strategy;

use app\repositories\CommissionConfig\CommissionConfigRepository;

class CommissionStrategyFactory
{
    public function __construct(
        private CommissionConfigRepository $commissionConfigRepository
    )
    {
    }

    public function create(string $strategy): CommissionStrategyInterface
    {

        return match ($strategy) {
            'marketplace' => new MarketplaceCommissionStrategy($this->commissionConfigRepository),
        };

    }

}