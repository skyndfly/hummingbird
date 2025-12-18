<?php

namespace app\services\Code;

use app\filters\Code\CodeFilter;
use app\repositories\Code\CodeRepository;
use app\repositories\Code\dto\CodeSearchDto;
use app\repositories\Code\dto\GroupedCodeDto;
use app\services\Code\dto\StockStatisticsDto;
use app\services\CommissionConfig\strategy\CommissionStrategyFactory;

class StockStatisticsService
{
    public function __construct(
        private CodeRepository $codeRepository,
        private CommissionStrategyFactory $commissionStrategyFactory
    ) {
    }

    public function getStatistics(?CodeFilter $filter = null): StockStatisticsDto
    {
        // Получаем коды на складе (не выданные)
        $searchDto = $filter ? new CodeSearchDto(
            code: $filter->code,
            date: $filter->date,
            categoryId: $filter->categoryId,
        ) : null;

        $codes = $this->codeRepository->findCodes($searchDto);

        // Фильтруем только невыданные коды
        $stockCodes = array_filter($codes, function (GroupedCodeDto $code) {
            return !in_array($code->status->value, [
                'Выдан/Наличные',
                'Выдан/Бесплатно',
                'Выдан/Оплата картой'
            ]);
        });

        if (empty($stockCodes)) {
            return new StockStatisticsDto(
                totalCodesCount: 0,
                totalPotentialEarnings: 0,
                totalCommission: 0,
                uniqueCodeCount: 0,
                uniqueCompaniesCount: 0
            );
        }

        // Группируем коды так же, как в FindCodeService
        $grouped = [];
        foreach ($stockCodes as $row) {
            $key = $row->code . '_' . $row->company->id;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'code' => $row->code,
                    'company' => $row->company,
                    'total_amount' => 0,
                    'total_quantity' => 0,
                    'rows' => []
                ];
            }
            $grouped[$key]['total_amount'] += $row->unpaidTotal;
            $grouped[$key]['total_quantity'] += $row->quantity;
            $grouped[$key]['rows'][] = $row;
        }

        // Рассчитываем общую комиссию
        $totalCommission = 0;
        foreach ($grouped as $group) {
            if ($group['total_amount'] > 0) {
                $strategy = $this->commissionStrategyFactory->create(
                    $group['company']->commissionStrategy
                );
                $commission = $strategy->calculate(
                    $group['total_amount'],
                    $group['total_quantity']
                );
                $totalCommission += $commission;
            }
        }

        return new StockStatisticsDto(
            totalCodesCount: array_sum(array_column($stockCodes, 'quantity')),
            totalPotentialEarnings: array_sum(array_column($stockCodes, 'unpaidTotal')),
            totalCommission: $totalCommission,
            uniqueCodeCount: count($grouped),
            uniqueCompaniesCount: count(array_unique(
                array_map(fn($item) => $item['company']->id, $grouped)
            ))
        );
    }
}