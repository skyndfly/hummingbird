<?php

namespace app\services\CommissionConfig\strategy;

use app\repositories\CommissionConfig\CommissionConfigRepository;
use app\services\CommissionConfig\enums\CommissionStrategyTypeEnum;

readonly class CommissionStrategyFactory
{
    public function __construct(
        private CommissionConfigRepository $commissionConfigRepository
    ) {
    }

    public function create(string $strategy): CommissionStrategyInterface
    {

        return match ($strategy) {
            CommissionStrategyTypeEnum::MARKETPLACE->value => new MarketplaceCommissionStrategy($this->commissionConfigRepository),
            CommissionStrategyTypeEnum::FIXED_FIVEPOST->value, CommissionStrategyTypeEnum::FIXED_POCHTA->value => new FivepostCommissionStrategy($this->commissionConfigRepository),
            CommissionStrategyTypeEnum::FIXED_SDEK->value => new SdekCommissionStrategy($this->commissionConfigRepository),
            default => new DefaultCommissionStrategy()
        };

    }

}