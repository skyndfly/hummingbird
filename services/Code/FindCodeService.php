<?php

namespace app\services\Code;

use app\filters\Code\CodeFilter;
use app\repositories\Code\CodeRepository;
use app\repositories\Code\dto\CodeSearchDto;
use app\repositories\Code\dto\GroupedCodeList;

class FindCodeService
{
    private CodeRepository $codeRepository;

    public function __construct(CodeRepository $codeRepository)
    {
        $this->codeRepository = $codeRepository;
    }

    /**
     * @return GroupedCodeList[]
     */
    public function execute(CodeFilter $filter): array
    {
        $groupedCodeDtos =  $this->codeRepository->findCodes(new CodeSearchDto(
            code: $filter->code,
            date: $filter->date,
            categoryId: $filter->categoryId,
        ));
        $grouped = [];
        foreach ($groupedCodeDtos as $row) {
            $code = $row->code;

            if (!isset($grouped[$code])) {
                $grouped[$code] = new GroupedCodeList($code);
            }
            $grouped[$code]->addRows($row);
        }

        return $grouped;
    }
}