<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        abort_if(Auth::user()?->role !== 'admin', 403, 'Anda tidak berhak mengakses halaman ini.');

        $users = User::orderBy('id')->get();
        $projects = Project::with('client')->orderByDesc('id')->get();

        return view('admin.index', compact('users', 'projects'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $actor = Auth::user();
        abort_if($actor?->role !== 'admin', 403);

        if ($user->id === $actor->id) {
            return redirect()->route('admin.index')->with('err', 'Anda tidak dapat mengubah peran akun sendiri.');
        }

        $validated = $request->validate([
            'role' => ['required', 'in:klien,developer,admin'],
        ]);

        $user->update(['role' => $validated['role']]);

        return redirect()->route('admin.index')->with('ok', 'Peran user diubah.');
    }

    public function destroyUser(User $user): RedirectResponse
    {
        $actor = Auth::user();
        abort_if($actor?->role !== 'admin', 403);

        if ($user->id === $actor->id) {
            return redirect()->route('admin.index')->with('err', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.index')->with('ok', 'User dihapus.');
    }

    public function destroyProject(Project $project): RedirectResponse
    {
        abort_if(Auth::user()?->role !== 'admin', 403);

        $project->delete();

        return redirect()->route('admin.index')->with('ok', 'Proyek dihapus.');
    }
}