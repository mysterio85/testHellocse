<?php

namespace App\Domain\Profile\Actions;

use App\Domain\Profile\Models\Profile;

class UpdateProfileAction
{
    /**
     * @param Profile $profile
     * @param array<string, mixed> $data
     * @return Profile
     */
    public function execute(Profile $profile, array $data): Profile
    {
        if (isset($data['image_path']) && $data['image_path']->isValid()) {
            $data['image_path'] = $data['image_path']->store('profiles', 'public');
        }
        $data['administrator_id'] = auth()->id();

        $profile->update($data);

        return $profile;
    }
}
