<?php

namespace App\Domain\Profile\Actions;

use App\Domain\Profile\Models\Profile;

class CreateProfileAction
{
    /**
     * @param array<string, mixed> $data
     * @return Profile
     */
    public function execute(array $data): Profile
    {
        return Profile::create($data);
    }
}
