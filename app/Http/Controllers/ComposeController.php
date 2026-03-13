<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Policies\QuotePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ComposeController extends Controller
{
    public function index()
    {
        return view('compose');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'min:3', 'max:1000'],
            'is_private' => ['nullable', 'boolean'],
        ]);

        Quote::create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'is_private' => $validated['is_private'] ?? false,
        ]);

        return redirect('/dashboard');
    }

    public function destroy(Quote $quote)
    {
        Gate::authorize('delete', $quote);

        $quote->delete();

        return redirect('/dashboard');
    }
}
