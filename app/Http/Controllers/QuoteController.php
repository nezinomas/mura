<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class QuoteController extends Controller implements HasMiddleware
{
/**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('throttle:5,1', only: ['store']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('quotes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quote)
    {
        Gate::authorize('update', $quote);

        if (! $quote->isEditable()) {
            return redirect('/dashboard')->with('modal_error', __('This thought is locked and can no longer be modified.'));
        }

        return view('quotes.create', [
            'quote' => $quote,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quote $quote)
    {
        Gate::authorize('update', [$quote, $request->boolean('is_private')]);

        if (! $quote->isEditable()) {
            return back()->with('modal_error', __('This thought is locked and can no longer be modified.'));
        }

        $validated = $request->validate([
            'content' => ['sometimes', 'required', 'string', 'min:3', 'max:1000'],
            'is_private' => ['boolean', 'nullable'],
        ]);

        $quote->update($validated);

        return redirect('/dashboard');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quote $quote)
    {
        Gate::authorize('delete', $quote);

        if ($quote->is_private) {
            $quote->delete();
        } else {
            $quote->update(['user_id' => null]);
        }

        return redirect('/dashboard');
    }

    /**
     * Grab a public thought from another user
     */
    public function grab(Request $request, Quote $quote)
    {
        Gate::authorize('grab', $quote);

        $request->user()->grabs()->syncWithoutDetaching([$quote->id]);

        return back();
    }

    /**
     * Ungrab a thought from another user
     */
    public function ungrab(Request $request, Quote $quote)
    {
        Gate::authorize('ungrab', $quote);

        $request->user()->grabs()->detach($quote->id);

        return back();
    }
}
