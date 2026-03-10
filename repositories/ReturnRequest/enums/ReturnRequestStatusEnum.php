<?php

namespace app\repositories\ReturnRequest\enums;

enum ReturnRequestStatusEnum: string
{
    case CREATED = 'created';
    case ACCEPTED = 'accepted';
    case QR_UPLOADED = 'qr_uploaded';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';

    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'создана',
            self::ACCEPTED => 'принято в 108к',
            self::QR_UPLOADED => 'QR код загружен',
            self::COMPLETED => 'выполнена',
            self::CANCELED => 'отмена',
        };
    }
}
