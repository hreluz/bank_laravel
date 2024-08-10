<?php

namespace Tests\Feature\Auth;

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
