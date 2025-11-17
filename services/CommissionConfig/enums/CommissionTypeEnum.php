<?php

namespace app\services\CommissionConfig\enums;

enum CommissionTypeEnum: string
{
    case FIXED = 'fixed';
    case PERCENT = 'percent';
    case PER_UNIT = 'per_unit';
}
