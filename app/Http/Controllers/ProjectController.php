<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $search = mb_substr(trim((string)$request->query('q', '')), 0, 100);

        $projects = Project::with('client')
            ->withCount('bids')
            ->when($search !== '', function ($query) use ($search) {$escaped = addcslashes($search, '\\%_');$query->where(function ($q) use ($escaped) {
                    $q->where('judul', 'like', "\%{$escaped}%")
                      ->orWhere('kategori', 'like', "%{$escaped}%");
                });
            })
            ->orderByDesc('id')
            ->get();

        return view('projects.index', compact('projects', 'search'));
    }

    public function show(Project $project): View
    {
        $project->load([
            'client',
            'bids' => fn ($query) =>$query->with('developer')
                ->orderByDesc('is_winner')
                ->orderBy('harga')
                ->orderBy('id'),
        ]);

        return view('projects.show', compact('project'));
    }

    public function create(): View
    {
        abort_if(Auth::user()?->role !== 'klien', 403, 'Hanya klien yang dapat memasang proyek.');

        return view('projects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_if($user?->role !== 'klien', 403, 'Hanya klien yang dapat memasang proyek.');

        $validated =$request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'deskripsi' => ['required', 'string', 'max:5000'],
            'kategori' => ['required', 'string', 'max:50'],
            'budget_min' => ['required', 'integer', 'min:0', 'max:999999999999'],
            'budget_max' => ['required', 'integer', 'gte:budget_min', 'max:999999999999'],
            'batas_lelang' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $user->projects()->create([
            'judul' => trim($validated['judul']),
            'deskripsi' => trim($validated['deskripsi']),
            'kategori' => trim($validated['kategori']),
            'budget_min' => $validated['budget_min'],
            'budget_max' => $validated['budget_max'],
            'batas_lelang' => $validated['batas_lelang'],
            'status' => 'terbuka',
        ]);

        return redirect()->route('dashboard')->with('ok', 'Proyek berhasil dipasang.');
    }

    public function edit(Project $project): View|RedirectResponse
    {
        $user = Auth::user();
        abort_if($user?->role !== 'klien' || $project->client_id !==$user->id, 403, 'Anda tidak berhak mengedit proyek ini.');

        if ($project->status !== 'terbuka') {
            return redirect()->route('dashboard')->with('err', 'Proyek yang sudah ditutup tidak dapat diedit.');
        }

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project$project): RedirectResponse
    {
        $user = Auth::user();
        abort_if($user?->role !== 'klien' || $project->client_id !==$user->id, 403, 'Anda tidak berhak memperbarui proyek ini.');

        if ($project->status !== 'terbuka') {
            return redirect()->route('dashboard')->with('err', 'Proyek yang sudah ditutup tidak dapat diperbarui.');
        }

        $validated =$request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'deskripsi' => ['required', 'string', 'max:5000'],
            'kategori' => ['required', 'string', 'max:50'],
            'budget_min' => ['required', 'integer', 'min:0', 'max:999999999999'],
            'budget_max' => ['required', 'integer', 'gte:budget_min', 'max:999999999999'],
            'batas_lelang' => ['required', 'date'],
        ]);

        $project->update([
            'judul' => trim($validated['judul']),
            'deskripsi' => trim($validated['deskripsi']),
            'kategori' => trim($validated['kategori']),
            'budget_min' => $validated['budget_min'],
            'budget_max' => $validated['budget_max'],
            'batas_lelang' => $validated['batas_lelang'],
        ]);

        return redirect()->route('dashboard')->with('ok', 'Proyek berhasil diperbarui.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $user = Auth::user();
        abort_if($user?->role !== 'klien' || $project->client_id !==$user->id, 403, 'Anda tidak berhak menghapus proyek ini.');

        if ($project->status !== 'terbuka') {
            return redirect()->route('dashboard')->with('err', 'Proyek yang sudah ditutup tidak dapat dihapus.');
        }

        $project->delete();

        return redirect()->route('dashboard')->with('ok', 'Proyek berhasil dihapus.');
    }
}