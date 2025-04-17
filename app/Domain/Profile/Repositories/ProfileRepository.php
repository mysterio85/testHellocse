<?php

namespace App\Domain\Profile\Repositories;

use App\Domain\Profile\Models\Profile;
use Illuminate\Database\Eloquent\Collection;

class ProfileRepository
{
    /**
     * @return Collection<int, Profile>
     */
    public function getAllActive(): Collection
    {
        return Profile::where('status', 'active')->get();
    }

    public function getById(int $id): Profile
    {
        return Profile::findOrFail($id);
    }
}
