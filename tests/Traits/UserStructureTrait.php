<?php

namespace Tests\Traits;

trait UserStructureTrait {
    /**
     * @return array
     */
    protected function userStructure(): array
    {
        return [
            'name',
            'email',
        ];
    }
}
