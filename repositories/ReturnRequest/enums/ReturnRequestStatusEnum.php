<?php

namespace app\repositories\ReturnRequest\enums;

enum ReturnRequestStatusEnum: string
{
    case CREATED = 'created';
    case ACCEPTED = 'accepted';
    case ROAD = 'road';
    case DELIVERED = 'delivered';
    case QR_UPLOADED = 'qr_uploaded';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
    case RETURNING = 'returning';
    case ACCEPTED_RETURN = 'accepted_return';
    case RETURN_CLIENT = 'return_client';

    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'создана',
            self::ACCEPTED => 'принято в 108к',
            self::ROAD => 'В пути',
            self::DELIVERED => 'Доставлен на пункт',
            self::QR_UPLOADED => 'QR код загружен',
            self::COMPLETED => 'выполнена',
            self::CANCELED => 'отмена',
            self::RETURNING => 'товар едет обратно',
            self::ACCEPTED_RETURN => 'принято в 108к для возврата клиенту',
            self::RETURN_CLIENT => 'вернули клиенту',
        };
    }
}
