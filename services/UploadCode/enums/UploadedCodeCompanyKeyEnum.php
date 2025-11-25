<?php

namespace app\services\UploadCode\enums;

enum UploadedCodeCompanyKeyEnum: string
{
    case WB = "wb";
    case OZON = "ozon";

    public function label(): string
    {
        return match ($this) {
            self::WB => 'Wildberries',
            self::OZON => 'Ozon',
        };
    }
}
