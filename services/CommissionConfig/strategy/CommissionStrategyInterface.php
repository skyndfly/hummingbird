<?php

namespace app\services\CommissionConfig\strategy;

interface CommissionStrategyInterface
{
    public function calculate(int $amount, int $quantity): int;
}