@extends('layouts.app')

@section('title', 'Admin Panel')

@section('content')
<div class="card">
    <h1>Admin</h1>
</div>

<div class="card">
    <h2>Kelola User</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Peran</th>
            <th>Aksi</th>
        </tr>
        @foreach($users as $u)
            <tr>
                <td>{{ $u->id }}</td>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->role }}</td>
                <td>
                    @if($u->id === auth()->id())
                        <span class="muted">(akun Anda)</span>
                    @else
                        <form class="inline" method="post" action="{{ route('admin.users.role', $u) }}">
                            @csrf
                            @method('PATCH')
                            <select name="role">
                                @foreach(['klien', 'developer', 'admin'] as $role)
                                    <option value="{{ $role }}" {{ $u->role === $role ? 'selected' : '' }}>{{ $role }}</option>
                                @endforeach
                            </select>
                            <button class="sm" type="submit">Ubah</button>
                        </form>

                        <form class="inline" method="post" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus user beserta proyek dan penawarannya?')">
                            @csrf
                            @method('DELETE')
                            <button class="sm sec" type="submit">Hapus</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</div>

<div class="card">
    <h2>Kelola Proyek</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Judul</th>
            <th>Klien</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        @foreach($projects as $project)
            <tr>
                <td>{{ $project->id }}</td>
                <td><a href="{{ route('projects.show', $project) }}">{{ $project->judul }}</a></td>
                <td>{{ $project->client->name }}</td>
                <td><span class="badge {{ $project->status === 'terbuka' ? 'open' : 'closed' }}">{{ $project->status }}</span></td>
                <td>
                    <form class="inline" method="post" action="{{ route('admin.projects.destroy', $project) }}" onsubmit="return confirm('Hapus proyek beserta penawarannya?')">
                        @csrf
                        @method('DELETE')
                        <button class="sm sec" type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
</div>
@endsection