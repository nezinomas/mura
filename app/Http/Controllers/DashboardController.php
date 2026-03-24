<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $filter = $request->query('filter');

        $query = Quote::with('user')
            ->withExists([
                'grabbedBy as is_grabbed' => function ($q) use ($userId) {
                    $q->where('quote_user.user_id', $userId);
                },
                'grabbedBy' // Automatically creates a 'grabbed_by_exists' attribute
            ]);

        match ($filter) {
            'public' => $query->where('user_id', $userId)->where('is_private', false),
            'private' => $query->where('user_id', $userId)->where('is_private', true),
            'grabbed' => $query->whereHas('grabbedBy', fn ($q) => $q->where('users.id', $userId)),
            default => $query->where(fn ($q) => $q
                ->where('user_id', $userId)
                ->orWhereHas('grabbedBy', fn ($sq) => $sq->where('users.id', $userId))),
        };

        $feed = $query->orderByDesc('created_at')
            ->paginate(20)
            ->onEachSide(1);

        return view('dashboard', ['quotes' => $feed]);
    }
}
