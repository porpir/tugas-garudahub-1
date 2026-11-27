@extends('layouts.app')

@section('content')
@if ($user->role === 'klien')
    <div class="card">
        <h2>Proyek Saya</h2>
        <p><a href="{{ route('projects.create') }}">Pasang proyek baru</a></p>
    </div>

    @forelse ($projects as $project)
        <div class="card">
            <h3>
                <a href="{{ route('projects.show', $project) }}">{{ $project->judul }}</a>
                <span class="badge {{ $project->isOpen() ? 'open' : 'closed' }}">
                    {{ $project->isOpen() ? 'terbuka' : 'ditutup' }}
                </span>
            </h3>
            <p class="muted">
                Kategori: {{ $project->kategori }} |
                Budget: Rp {{ number_format($project->budget_min, 0, ',', '.') }} - Rp {{ number_format($project->budget_max, 0, ',', '.') }} |
                Penawaran: {{ $project->bids_count }} |
                Batas: {{ $project->batas_lelang->format('Y-m-d') }}
            </p>

            @if ($project->isOpen())
                <div class="action-row">
                    <a href="{{ route('projects.edit', $project) }}" class="btn">Edit</a>
                    <form method="POST" action="{{ route('projects.destroy', $project) }}" data-confirm="Yakin ingin menghapus proyek ini?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-secondary">Hapus</button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div class="card">
            <p class="muted">Anda belum memasang proyek apa pun.</p>
        </div>
    @endforelse
@else
    <div class="card">
        <h2>Penawaran Saya</h2>
    </div>

    @forelse ($bids as $bid)
        <div class="card">
            <h3>
                <a href="{{ route('projects.show', $bid->project) }}">{{ $bid->project->judul }}</a>
                <span class="badge {{ $bid->project->isOpen() ? 'open' : 'closed' }}">
                    {{ $bid->project->isOpen() ? 'terbuka' : 'ditutup' }}
                </span>
                @if ($bid->is_winner)
                    <span class="badge open">Menang</span>
                @elseif (!$bid->project->isOpen())
                    <span class="badge closed">Kalah</span>
                @else
                    <span class="badge wait">Menunggu</span>
                @endif
            </h3>
            <p class="muted">
                Klien: {{ $bid->project->client->name }} |
                Harga: Rp {{ number_format($bid->harga, 0, ',', '.') }} |
                Durasi: {{ $bid->durasi }} hari |
                Batas: {{ $bid->project->batas_lelang->format('Y-m-d') }}
            </p>

            @if ($bid->project->isOpen() && !$bid->is_winner)
                <div class="action-row">
                    <a href="{{ route('bids.edit', $bid) }}" class="btn">Edit</a>
                    <form method="POST" action="{{ route('bids.destroy', $bid) }}" data-confirm="Yakin ingin membatalkan dan menghapus penawaran ini?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-secondary">Hapus</button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div class="card">
            <p class="muted">Anda belum mengajukan penawaran pada proyek mana pun.</p>
        </div>
    @endforelse
@endif
@endsection