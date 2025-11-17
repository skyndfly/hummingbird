<?php

namespace app\services\CommissionConfig\enums;

enum CommissionStrategyTypeEnum: string
{
    case MARKETPLACE = 'marketplace';
    case FIXED_POCHTA = 'fixed_pochta';
    case FIXED_SDEK = 'fixed_sdek';
    case FIXED_FIVEPOST = 'fixed_fivepost';

    public function label(): string
    {
        return match ($this) {
            self::MARKETPLACE => 'Маркетплейсы (Ozon, ДНС, и т.д.)',
            self::FIXED_POCHTA => 'Почта России',
            self::FIXED_SDEK => 'СДЭК',
            self::FIXED_FIVEPOST => '5Post',
        };
    }
}
