<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Cache a random public post ID for the rest of the day
        $dailyQuoteId = Cache::remember('daily_quote_id', now()->endOfDay(), function () {
            return Quote::where('is_private', false)->inRandomOrder()->value('id');
        });
        
        $dailyQuote = $dailyQuoteId ? Quote::find($dailyQuoteId) : null;

        $query = Quote::with('user')->where('is_private', false);

        if ($user = auth()->user()) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', '!=', $user->id)
                  ->orWhereNull('user_id');
            })->withExists([
                'grabbedBy as is_grabbed' => fn ($q) => $q->where('quote_user.user_id', $user->id)
            ]);

            if ($dailyQuote && $dailyQuote->user_id === $user->id) {
                $dailyQuote = null; // Hide the daily quote if it happens to belong to the active user
            } elseif ($dailyQuote) {
                $dailyQuote->loadExists([
                    'grabbedBy as is_grabbed' => fn ($q) => $q->where('quote_user.user_id', $user->id)
                ]);
            }
        }

        $quotes = $query->inRandomOrder()->limit(20)->get();

        return view('home', [
            'quotes' => $quotes,
            'dailyQuote' => $dailyQuote,
        ]);
    }
}