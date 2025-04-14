<?php

namespace App\Domain\Profile\Models;

use App\Domain\Administrator\Models\Administrator;
use App\Domain\Comment\Models\Comment;
use App\Domain\Profile\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'administrator_id',
        'last_name',
        'first_name',
        'image_path',
        'status',
    ];


    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Administrator::class, 'administrator_id');
    }


    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    protected static function newFactory()
    {
        return ProfileFactory::new();
    }
}
