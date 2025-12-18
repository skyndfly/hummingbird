<?php

namespace app\services\Code\dto;

class StockStatisticsDto
{
    public function __construct(
        public int $totalCodesCount, // Общее количество кодов на складе
        public int $totalPotentialEarnings, // Потенциальный заработок (общая сумма кодов)
        public int $totalCommission, // Общая комиссия сервиса
        public int $uniqueCodeCount, // Уникальных кодов (после группировки)
        public int $uniqueCompaniesCount, // Уникальных компаний
    )
    {
    }
}