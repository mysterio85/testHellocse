<?php

namespace App\Domain\Profile\Actions;

use App\Domain\Profile\Models\Profile;
use Illuminate\Http\UploadedFile;

class UpdateProfileAction
{
    /**
     * @param Profile $profile
     * @param array<string, mixed> $data
     *
     * @return Profile
     */
    public function execute(Profile $profile, array $data): Profile
    {
        $imagePath = $data['image_path'];

        if (isset($imagePath) && ($imagePath instanceof UploadedFile) && $imagePath->isValid()) {
            $data['image_path'] = $imagePath->store('profiles', 'public');
        }
        $data['administrator_id'] = auth()->id();

        $profile->update($data);

        return $profile;
    }
}
