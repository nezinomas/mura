<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use InvalidArgumentException;


class Quote extends Model
{
    /** @use HasFactory<\Database\Factories\QuoteFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'is_private',
    ];

    protected $attributes = [
        'is_private' => false,
    ];

    //The Relationship Law
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //The "Lost in Time" Display Law
    protected function authorDisplay(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->user ? $this->user->display_name : '(user lost in time)'
        );
    }

    // DB Dimension Law (The Mutator)
    protected function content(): Attribute
    {
        return Attribute::make(
            set: function (?string $value) {
                if ($value === null) return $value;

                $trimmed = trim($value);
                $length = mb_strlen($trimmed);

                if ($length < 3 || $length > 1000) {
                    throw new InvalidArgumentException('A thought must be between 3 and 1000 characters.');
                }

                return $trimmed;
            }
        );
    }

    // "Quiet Typography" Law (The Accessor)
    protected function contentHtml(): Attribute
    {
        return Attribute::make(
            get: function () {
                $html = Str::markdown($this->content ?? '', [
                    'html_input' => 'escape',
                ]);

                $allowedTags = '<p><strong><em><a><del>';

                return strip_tags($html, $allowedTags);
            }
        );
    }

    // Time Laws
    public function isEditable(): bool
    {
        // Is the 24-hour expiration deadline still in the future?
        return $this->created_at->addHours(24)->isFuture();
    }

    public function isEdited(): bool
    {
        // If timestamps no longer match exactly, the stone has been altered.
        return $this->updated_at->notEqualTo($this->created_at);
    }

    public function isMine(): bool
    {
        return $this->user_id == auth()->id();
    }

    public function grabbedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'quote_user')->withTimestamps();
    }

    public function isGrabbedByMe(): bool
    {
        return auth()->check() && $this->grabbedBy->contains(auth()->id());
    }

    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
        ];
    }
}
