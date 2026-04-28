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
        'created_at',
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
                    // 'renderer' => [
                    //     'soft_break' => "<br>",
                    // ],
                ]);

                $allowedTags = '<p><br><strong><em><a><del>';

                return strip_tags($html, $allowedTags);
            }
        );
    }

    // Thought can be edited 24 hours after create
    public function isEditable(): bool
    {
        return $this->created_at->addHours(24)->isFuture();
    }

    // Check if thought was edited
    public function isEdited(): bool
    {
        return $this->updated_at->notEqualTo($this->created_at);
    }

    // Check if thought is mine
    public function isMine(): bool
    {
        return $this->user_id == auth()->id();
    }

    //
    public function grabbedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    // Check if thought is grabbed by a specific user
    public function isGrabbedBy(?User $user = null): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->id === auth()->id() && $this->getAttribute('is_grabbed') !== null) {
            return (bool) $this->getAttribute('is_grabbed');
        }

        return $this->grabbedBy()->wherePivot('user_id', $user->id)->exists();
    }

    // Check if thought is grabbed by anyone
    public function isGrabbedByAnyone(): bool
    {
        if ($this->getAttribute('grabbed_by_exists') !== null) {
            return (bool) $this->getAttribute('grabbed_by_exists');
        }

        return $this->grabbedBy()->exists();
    }

    public function disownOrDelete(): void
    {
        if ($this->is_private || ! $this->isGrabbedByAnyone()) {
            $this->delete();
        } else {
            $this->update(['user_id' => null]);
        }
    }

    // 
    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
        ];
    }
}
