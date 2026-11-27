@extends('layouts.app')

@section('title', 'Daftar Proyek')

@section('content')
<div class="card">
    <h1>Daftar Proyek</h1>
    <form class="search" method="get" action="{{ route('home') }}">
        <input name="q" maxlength="100" placeholder="Cari judul atau kategori..." value="{{ $search }}">
        <button type="submit">Cari</button>
    </form>
    @if($search !== '')
        <p class="muted">Hasil pencarian untuk: <b>{{ $search }}</b> ({{ $projects->count() }} proyek)</p>
    @endif
</div>

@forelse($projects as $project)
    <div class="card">
        <h2>
            <a href="{{ route('projects.show', $project) }}">{{ $project->judul }}</a>
            <span class="badge {{ $project->isOpen() ? 'open' : 'closed' }}">{{ $project->isOpen() ? 'terbuka' : 'ditutup' }}</span>
        </h2>
        <p class="muted">
            Kategori: {{ $project->kategori }} |
            Klien: {{ $project->client->name }} |
            Budget: Rp {{ number_format($project->budget_min, 0, ',', '.') }} - Rp {{ number_format($project->budget_max, 0, ',', '.') }} |
            Batas: {{ $project->batas_lelang->format('Y-m-d') }} |
            {{ $project->bids_count }} penawaran
        </p>
        <p>{{ \Illuminate\Support\Str::limit($project->deskripsi, 160) }}</p>
    </div>
@empty
    <div class="card">
        <p>Belum ada proyek yang cocok.</p>
    </div>
@endforelse
@endsection