<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $feed = Quote::with('user')
            ->withExists([
                'grabbedBy as is_grabbed' => function ($query) use ($userId) {
                    $query->where('quote_user.user_id', $userId);
                },
                'grabbedBy' // Automatically creates a 'grabbed_by_exists' attribute
            ])
            ->where('user_id', $userId)
            ->orWhereIn('id', function ($query) use ($userId) {
                $query->select('quote_id')
                    ->from('quote_user')
                    ->where('user_id', $userId);
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->onEachSide(1);

        return view('dashboard', ['quotes' => $feed]);
    }
}
