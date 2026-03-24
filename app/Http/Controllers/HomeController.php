<?php

namespace App\Http\Controllers;

use App\Models\Quote;

class HomeController extends Controller
{
    public function index()
    {
        $quotes = Quote::with('user')
            ->where('is_private', false)
            ->inRandomOrder()
            ->limit(20)
            ->get();

        return view('home', ['quotes' => $quotes]);
    }
}