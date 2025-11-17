<?php

namespace app\services\Code;

use app\filters\Code\CodeFilter;
use app\repositories\Code\CodeRepository;
use app\repositories\Code\dto\CodeSearchDto;
use app\repositories\Code\dto\GroupedCodeList;
use app\services\CommissionConfig\strategy\CommissionStrategyFactory;

class FindCodeService
{
    public function __construct(
        private CodeRepository $codeRepository,
        private CommissionStrategyFactory $commissionStrategyFactory
    ) {
    }

    /**
     * @return GroupedCodeList[]
     */
    public function execute(CodeFilter $filter): array
    {
        $groupedCodeDtos = $this->codeRepository->findCodes(new CodeSearchDto(
            code: $filter->code,
            date: $filter->date,
            categoryId: $filter->categoryId,
        ));


        $grouped = [];
        foreach ($groupedCodeDtos as $row) {

            $key = $row->code . '_' . $row->company->id;
            if (!isset($grouped[$key])) {
                $grouped[$key] = new GroupedCodeList($row->code);
            }
            $grouped[$key]->addRows($row);


        }
        return $this->calculateCommission($grouped);
    }

    /**
     * Считаем комиссию после группировки - от общей суммы
     */
    private function calculateCommission(array $grouped): array
    {
        foreach ($grouped as $group) {
            if (!empty($group->getRows())) {

                $firstRow = $group->getRows()[0];
                $totalAmount = $group->getUnpaidTotal();

                $strategy = $this->commissionStrategyFactory->create($firstRow->company->commissionStrategy);
                $commission = $strategy->calculate($totalAmount, $group->getTotalQuantity());


                $group->setCommission($commission);
            }

        }
        return $grouped;
    }
}