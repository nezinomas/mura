<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuotePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Quote $quote): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quote $quote, bool $wantsToMakePrivate = false): Response
    {
        // 1. Standard Ownership Check (You likely already have this)
        if ($user->id !== $quote->user_id) {
            return Response::deny('You do not own this thought.');
        }

        // 2. The mura Immutability Rule
        if ($wantsToMakePrivate && $quote->grabbedBy()->exists()) {
            return Response::deny(__('This public thought has been grabbed and is now permanently visible.'));
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quote $quote): bool
    {
        return $user->id === $quote->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Quote $quote): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Quote $quote): bool
    {
        return false;
    }


    /**
     * Determine whether user can grab the thought
     */
    public function grab(User $user, Quote $quote): Response
    {
        if ($quote->isMine()) {
            return Response::deny(__('You cannot grab your own thought.'));
        }

        if ($quote->is_private) {
            return Response::deny(__('You cannot grab a private thought.'));
        }

        return Response::allow();
    }
}
