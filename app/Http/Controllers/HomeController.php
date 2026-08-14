<?php

namespace App\Http\Controllers;

use App\Models\Setlist;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Afișează pagina principală.
     */
    public function index(Request $request)
    {
        $liveSetlists = collect();

        if ($request->user()) {
            $teamIds = $request
                ->user()
                ->teams()
                ->pluck('teams.id');

            if ($teamIds->isNotEmpty()) {
                $liveSetlists = Setlist::query()
                    ->where('is_live', true)
                    ->whereIn('team_id', $teamIds)
                    ->with([
                        'user',
                        'team',
                    ])
                    ->withCount('songs')
                    ->latest('updated_at')
                    ->get();
            }
        }

        return view(
            'home',
            compact('liveSetlists')
        );
    }
}