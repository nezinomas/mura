<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $quotes = $request->user()->quotes()
            ->latest()
            ->get();
        $grabs = $request->user()->grabs()
            ->with('user')
            ->latest()
            ->get();

        $feed = $quotes->concat($grabs)->sortByDesc('created_at');

        return view('dashboard', ['feed' => $feed]);
    }
}
