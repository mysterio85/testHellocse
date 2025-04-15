<?php

namespace App\Domain\Profile\Actions;

use App\Domain\Profile\Models\Profile;
use Illuminate\Support\Facades\Storage;

class DeleteProfileAction
{
    public function execute(Profile $profile): ?bool
    {
        if ($profile->image_path) {
            Storage::disk('public')->delete($profile->image_path);
        }

        return $profile->delete();
    }
}
