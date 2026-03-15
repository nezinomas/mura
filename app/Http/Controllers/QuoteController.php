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
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quote $quote)
    {
        //
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
}
