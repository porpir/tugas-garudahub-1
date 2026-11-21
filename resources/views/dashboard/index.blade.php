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
            <h2>
                <a href="{{ route('projects.show', $project) }}">{{ $project->judul }}</a>
                <span class="badge {{ $project->status === 'terbuka' ? 'open' : 'closed' }}">{{ $project->status }}</span>
            </h2>
            <p class="muted">
                Budget: Rp {{ number_format($project->budget_min, 0, ',', '.') }} - Rp {{ number_format($project->budget_max, 0, ',', '.') }} |
                Batas: {{ $project->batas_lelang->format('Y-m-d') }} |
                {{ $project->bids_count }} penawaran
            </p>
        </div>
    @empty
        <div class="card">
            <p>Anda belum memasang proyek.</p>
        </div>
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