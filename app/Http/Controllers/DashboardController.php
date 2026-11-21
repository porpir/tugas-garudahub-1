<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        abort_if(!in_array($user?->role, ['klien', 'developer'], true), 403, 'Anda tidak berhak mengakses halaman ini.');

        if ($user->role === 'klien') {
            $projects = $user->projects()
                ->withCount('bids')
                ->orderByDesc('id')
                ->get();

            return view('dashboard.index', compact('user', 'projects'));
        }

        $bids = $user->bids()
            ->with('project.client')
            ->orderByDesc('id')
            ->get();

        return view('dashboard.index', compact('user', 'bids'));
    }
}