<?php

namespace app\ui\gridTable;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class GridColumn
{
    public function __construct(
        public string $label,
        public ?string $formatter = null,
        public bool $sortable = false
    ) {
    }
}