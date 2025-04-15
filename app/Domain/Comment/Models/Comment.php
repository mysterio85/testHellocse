<?php

namespace App\Domain\Comment\Models;

use App\Domain\Administrator\Models\Administrator;
use App\Domain\Comment\Factories\CommentFactory;
use App\Domain\Profile\Models\Profile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    protected $fillable = [
        'content',
        'administrator_id',
        'profile_id',
    ];

    /**
     * @return BelongsTo<Administrator, Comment>
     */
    public function administrator(): BelongsTo
    {
        /** @var BelongsTo<Administrator, Comment> $administrator */
        $administrator = $this->belongsTo(Administrator::class, 'administrator_id');

        return $administrator;
    }

    /**
     * @return BelongsTo<Profile, Comment>
     */
    public function profile(): BelongsTo
    {
        /** @var BelongsTo<Profile, Comment> $profile */
        $profile = $this->belongsTo(Profile::class);

        return $profile;
    }

    protected static function newFactory(): CommentFactory
    {
        return CommentFactory::new();
    }
}
