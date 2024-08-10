<?php

namespace Tests\Feature\Company;

use Tests\Feature\Auth\UserStructureTrait;

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
