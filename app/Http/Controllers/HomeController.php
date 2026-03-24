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

        $quotes = Quote::with('user')
            ->where('is_private', false)
            ->inRandomOrder()
            ->limit(20)
            ->get();

        return view('home', [
            'quotes' => $quotes,
            'dailyQuote' => $dailyQuote,
        ]);
    }
}