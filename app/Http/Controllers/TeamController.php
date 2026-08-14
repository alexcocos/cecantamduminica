<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    /**
     * Afișează echipele utilizatorului.
     */
    public function index(Request $request)
    {
        $teams = $request
            ->user()
            ->teams()
            ->with('owner')
            ->withCount([
                'users',
                'setlists',
            ])
            ->orderBy('name')
            ->get();

        return view(
            'teams.index',
            compact('teams')
        );
    }

    /**
     * Creează o echipă nouă.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
    [
        'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique(
                'teams',
                'name'
            ),
        ],
    ],
    [
        'name.unique' =>
            'Există deja o echipă cu acest nume.',
    ]
);

        $team = DB::transaction(
            function () use (
                $request,
                $validated
            ) {
                $team = new Team();

                $team->owner_id =
                    $request->user()->id;

                $team->name =
                    $validated['name'];

                $team->join_code =
                    $this->generateUniqueJoinCode();

                $team->save();

                /*
                 * Creatorul este adăugat automat
                 * ca membru și proprietar.
                 */
                $team->users()->attach(
                    $request->user()->id,
                    [
                        'role' => 'owner',
                    ]
                );

                return $team;
            }
        );

        return redirect()
            ->route('teams.show', $team)
            ->with(
                'success',
                'Echipa a fost creată.'
            );
    }

    /**
     * Înscrie utilizatorul într-o echipă prin cod.
     */
    public function join(Request $request)
    {
        $validated = $request->validate([
            'join_code' => [
                'required',
                'string',
                'max:30',
            ],
        ]);

        $joinCode = Str::upper(
            trim($validated['join_code'])
        );

        $team = Team::query()
            ->where(
                'join_code',
                $joinCode
            )
            ->first();

        if (!$team) {
            return back()
                ->withErrors([
                    'join_code' =>
                        'Codul echipei nu este valid.',
                ])
                ->withInput();
        }

        $alreadyMember = $team
            ->users()
            ->whereKey(
                $request->user()->id
            )
            ->exists();

        if ($alreadyMember) {
            return redirect()
                ->route('teams.show', $team)
                ->with(
                    'success',
                    'Ești deja membru al acestei echipe.'
                );
        }

        $team->users()->attach(
            $request->user()->id,
            [
                'role' => 'member',
            ]
        );

        return redirect()
            ->route('teams.show', $team)
            ->with(
                'success',
                'Te-ai înscris în echipa „'
                    . $team->name
                    . '”.'
            );
    }

    /**
     * Afișează o echipă și membrii săi.
     */
    public function show(
        Request $request,
        Team $team
    ) {
        $this->ensureMember(
            $request,
            $team
        );

        $team->load([
            'owner',
            'users' => function ($query) {
                $query->orderBy('name');
            },
            'setlists' => function ($query) {
                $query
                    ->withCount('songs')
                    ->latest('updated_at');
            },
        ]);

        return view(
            'teams.show',
            compact('team')
        );
    }

    /**
     * Utilizatorul părăsește echipa.
     */
    public function leave(
        Request $request,
        Team $team
    ) {
        $this->ensureMember(
            $request,
            $team
        );

        if ($team->isOwner($request->user())) {
            return back()->withErrors([
                'team' =>
                    'Proprietarul nu poate părăsi echipa.',
            ]);
        }

        $team->users()->detach(
            $request->user()->id
        );

        return redirect()
            ->route('teams.index')
            ->with(
                'success',
                'Ai părăsit echipa „'
                    . $team->name
                    . '”.'
            );
    }

/**
 * Șterge o echipă.
 *
 * Numai proprietarul poate face această acțiune.
 */
public function destroy(
    Request $request,
    Team $team
) {
    abort_unless(
        $team->isOwner(
            $request->user()
        ),
        403,
        'Numai proprietarul poate șterge echipa.'
    );

    DB::transaction(
        function () use ($team) {
            /*
             * Setlisturile nu sunt șterse.
             * Devin personale și nu mai sunt live.
             */
            $team
                ->setlists()
                ->update([
                    'team_id' => null,
                    'is_live' => false,
                ]);

            $team->delete();
        }
    );

    return redirect()
        ->route('teams.index')
        ->with(
            'success',
            'Echipa a fost ștearsă. Setlisturile au rămas în conturile proprietarilor.'
        );
}

    /**
     * Generează un cod unic de înscriere.
     */
    private function generateUniqueJoinCode(): string
    {
        do {
            $code = Str::upper(
                Str::random(8)
            );
        } while (
            Team::query()
                ->where('join_code', $code)
                ->exists()
        );

        return $code;
    }

    /**
     * Permite accesul numai membrilor echipei.
     */
    private function ensureMember(
        Request $request,
        Team $team
    ): void {
        abort_unless(
            $team->hasMember(
                $request->user()
            ),
            403,
            'Nu faci parte din această echipă.'
        );
    }
}