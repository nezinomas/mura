<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function show(User $user)
    {
        $quotes = $user->quotes()
            ->where('is_private', false)
            ->orderByDesc('created_at')
            ->paginate(20)
            ->onEachSide(1);

        // Manually set the relationship to prevent an extra eager-loading query
        $quotes->getCollection()->each(function ($quote) use ($user) {
            $quote->setRelation('user', $user);
        });

        return view('users.show', [
            'user' => $user,
            'quotes' => $quotes,
        ]);
    }
}