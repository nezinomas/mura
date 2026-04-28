<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Handle the incoming search request.
     */
    public function index(Request $request): View
    {
        // 1. Grab the search term from the URL (?q=...)
        $searchTerm = $request->input('q');

        // 2. Build the query
        $quotes = Quote::query()
            ->when($searchTerm, function ($query, $term) {
                // Search the content
                $query->where('content', 'like', "%{$term}%");
            })
            ->where(function ($query) {
                // Privacy Guard: Must be public OR belong to the logged-in user
                $query->where('is_private', false);
                
                if (auth()->check()) {
                    $query->orWhere('user_id', auth()->id());
                }
            })
            ->latest() // Order by newest first
            ->paginate(20) // Limit to 20 per page
            ->withQueryString(); // Keep the ?q= keyword in the pagination links!

        // 3. Return the view with the results
        return view('search', [
            'quotes' => $quotes,
            'searchTerm' => $searchTerm,
        ]);
    }
}