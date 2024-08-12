<?php

namespace Tests\Traits;

trait BankAccountStructureTrait {
    /**
     * @return array
     */
    protected function bankAccountStructure(): array
    {
        return [
            'id',
            'balance',
            'owner',
            'company',
        ];
    }
}



