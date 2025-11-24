<?php

namespace app\services\Company\exceptions;

use LogicException;

class CompanyNotFoundException extends LogicException
{
    public function __construct(int $companyId)
    {
        parent::__construct("Служба доставки {$companyId} не найдена");
    }
}