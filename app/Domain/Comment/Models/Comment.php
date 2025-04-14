<?php

namespace App\Domain\Comment\Models;

use App\Domain\Administrator\Factories\AdministratorFactory;
use App\Domain\Administrator\Models\Administrator;
use App\Domain\Profile\Models\Profile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    /** @use HasFactory<AdministratorFactory> */
    use HasFactory;

    protected $fillable = [
        'content',
        'administrator_id',
        'profile_id',
    ];

    public function administrator(): BelongsTo
    {
        return $this->belongsTo(Administrator::class, 'administrator_id');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
