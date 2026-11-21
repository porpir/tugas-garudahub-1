@extends('layouts.app')

@section('title', 'Dasbor')

@section('content')
@if($user->role === 'klien')
    <div class="card">
        <h1>Proyek Saya</h1>
        <p><a href="{{ route('projects.create') }}">Pasang proyek baru</a></p>
    </div>

    @forelse($projects as $project)
    <div class="card">
        {{-- ... judul dan detail proyek yang sudah ada ... --}}

        @if ($project->status === 'terbuka')
            <div style="display: flex; gap: 8px; margin-top: 10px;">
                <a href="{{ route('projects.edit', $project) }}" class="btn">Edit</a>
                <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Yakin ingin menghapus proyek ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary">Hapus</button>
                </form>
            </div>
        @endif
    </div>
@empty
    {{-- ... pesan jika proyek kosong ... --}}
@endforelse
@else
    <div class="card">
        <h1>Penawaran Saya</h1>
    </div>

    @forelse($bids as $bid)
        <div class="card">
            <h2>
                <a href="{{ route('projects.show', $bid->project) }}">{{ $bid->project->judul }}</a>
                <span class="badge {{ $bid->project->status === 'terbuka' ? 'open' : 'closed' }}">{{ $bid->project->status }}</span>
                @if($bid->is_winner)
                    <span class="badge open">Pemenang</span>
                @endif
            </h2>
            <p class="muted">
                Klien: {{ $bid->project->client->name }} |
                Harga: Rp {{ number_format($bid->harga, 0, ',', '.') }} |
                Durasi: {{ $bid->durasi }} hari |
                Batas: {{ $bid->project->batas_lelang->format('Y-m-d') }}
            </p>
        </div>
    @empty
        <div class="card">
            <p>Anda belum mengajukan penawaran.</p>
        </div>
    @endforelse
@endif
@endsection