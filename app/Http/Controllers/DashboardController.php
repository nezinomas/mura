<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $quotes = $request->user()->quotes()
            ->with('user')
            ->get();

        $grabs = $request->user()->grabs()
            ->with('user')
            ->get();

        $feed = $quotes->concat($grabs)->sortByDesc('created_at');

        return view('dashboard', ['feed' => $feed]);
    }
}
