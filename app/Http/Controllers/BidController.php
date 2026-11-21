<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BidController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        $user = Auth::user();
        abort_if($user?->role !== 'developer', 403, 'Hanya developer yang dapat mengajukan penawaran.');

        if (!$project->isOpen()) {
            return redirect()->route('projects.show', $project)
                ->with('err', 'Lelang sudah ditutup; penawaran tidak dapat diajukan.');
        }

        $validated = $request->validate([
            'harga' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'durasi' => ['required', 'integer', 'min:1', 'max:3650'],
            'proposal' => ['required', 'string', 'max:5000'],
        ]);

        $alreadyBid = Bid::where('project_id', $project->id)
            ->where('developer_id', $user->id)
            ->exists();

        if ($alreadyBid) {
            return redirect()->route('projects.show', $project)
                ->with('err', 'Anda sudah mengajukan penawaran pada proyek ini.');
        }

        $project->bids()->create([
            'developer_id' => $user->id,
            'harga' => $validated['harga'],
            'durasi' => $validated['durasi'],
            'proposal' => trim($validated['proposal']),
        ]);

        return redirect()->route('projects.show', $project)->with('ok', 'Penawaran berhasil dikirim.');
    }

    public function pickWinner(Project $project, Bid $bid): RedirectResponse
    {
        $user = Auth::user();
        abort_if($user?->role !== 'klien', 403, 'Hanya klien yang dapat memilih pemenang.');

        $error = DB::transaction(function () use ($user, $project, $bid): ?string {
            $lockedProject = Project::where('id', $project->id)
                ->where('client_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedProject) {
                return 'Proyek tidak ditemukan atau bukan milik Anda.';
            }

            if ($lockedProject->status !== 'terbuka') {
                return 'Pemenang sudah dipilih atau lelang sudah ditutup.';
            }

            $validBid = Bid::where('id', $bid->id)
                ->where('project_id', $lockedProject->id)
                ->first();

            if (!$validBid) {
                return 'Penawaran tidak valid untuk proyek ini.';
            }

            $validBid->update(['is_winner' => true]);
            $lockedProject->update(['status' => 'ditutup']);

            return null;
        });

        if ($error !== null) {
            return redirect()->route('projects.show', $project)->with('err', $error);
        }

        return redirect()->route('projects.show', $project)->with('ok', 'Pemenang berhasil dipilih.');
    }
}