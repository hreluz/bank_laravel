<?php

namespace Tests\Traits;

trait CompanyStructureTrait {

    use UserStructureTrait;

    /**
     * @return array
     */
    protected function companyStructure(): array
    {
        return [
            'id',
            'name',
            'owner' =>  $this->userStructure()
        ];
    }
}
