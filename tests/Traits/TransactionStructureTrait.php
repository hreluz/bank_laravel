<?php

namespace Tests\Traits;

trait TransactionStructureTrait {

    /**
     * @return array
     */
    protected function transactionStructure(): array
    {
        return [
            'id',
            'type',
            'amount',
            'date',
            'time',
        ];
    }
}
