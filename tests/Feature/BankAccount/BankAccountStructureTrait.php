<?php

namespace Tests\Feature\BankAccount;

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



