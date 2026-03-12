<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;

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
        ]);

        Quote::create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        return redirect('/home');
    }
}
