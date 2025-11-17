<?php

namespace app\repositories\Code\enums;

enum CodeStatusEnum: string
{
    case NEW = 'Новый';
    case ISSUED = 'Выдан/Наличные';
    case ISSUED_FREE = 'Выдан/Бесплатно';
    case ISSUED_CARD = 'Выдан/Оплата картой';
    case LOST = 'Не найден';
}
