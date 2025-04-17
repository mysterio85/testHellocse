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
     * @return BelongsTo<Administrator, Profile>
     */
    public function administrator(): BelongsTo
    {
        /** @var BelongsTo<Administrator, Profile> $administrator */
        $administrator = $this->belongsTo(Administrator::class);

        return $administrator;
    }

    /**
     * @return HasMany<Comment, Profile>
     */
    public function comments(): HasMany
    {
        /** @var HasMany<Comment, Profile> $comments */
        $comments = $this->hasMany(Comment::class);

        return $comments;
    }

    protected static function newFactory(): ProfileFactory
    {
        return ProfileFactory::new();
    }
}
