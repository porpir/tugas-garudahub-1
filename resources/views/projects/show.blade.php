@extends('layouts.app')

@section('title', $project->judul)

@section('content')
<div class="card">
    <h1>
        {{ $project->judul }}
        <span class="badge {{ $project->status === 'terbuka' ? 'open' : 'closed' }}">{{ $project->status }}</span>
    </h1>
    <p class="muted">
        Kategori: {{ $project->kategori }} |
        Klien: {{ $project->client->name }} |
        Budget: Rp {{ number_format($project->budget_min, 0, ',', '.') }} - Rp {{ number_format($project->budget_max, 0, ',', '.') }} |
        Batas lelang: {{ $project->batas_lelang->format('Y-m-d') }}
    </p>
    <p class="pre-line">{{ $project->deskripsi }}</p>
</div>

<div class="card">
    <h2>Penawaran ({{ $project->bids->count() }})</h2>
    @if($project->bids->isEmpty())
        <p>Belum ada penawaran.</p>
    @else
        <table>
            <tr>
                <th>Developer</th>
                <th>Harga</th>
                <th>Durasi</th>
                <th>Proposal</th>
                <th></th>
            </tr>
            @foreach($project->bids as $bid)
                <tr class="{{ $bid->is_winner ? 'winner' : '' }}">
                    <td>
                        {{ $bid->developer->name }}
                        @if($bid->is_winner)
                            <span class="badge win">Pemenang</span>
                        @endif
                    </td>
                    <td>Rp {{ number_format($bid->harga, 0, ',', '.') }}</td>
                    <td>{{ $bid->durasi }} hari</td>
                    <td class="pre-line">{{ $bid->proposal }}</td>
                    <td>
                        @if(auth()->check() && auth()->user()->role === 'klien' && auth()->id() === $project->client_id && $project->status === 'terbuka')
                            <form method="post" action="{{ route('bids.pick', [$project, $bid]) }}" data-confirm="Pilih penawaran ini sebagai pemenang?">
                                @csrf
                                <button class="sm" type="submit">Pilih pemenang</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
</div>

@auth
    @if(auth()->user()->role === 'developer')
        @if($project->isOpen())
            <div class="card">
                <h2>Ajukan Penawaran</h2>
                <form method="post" action="{{ route('bids.store', $project) }}">
                    @csrf
                    <div class="row">
                        <div>
                            <label>Harga (Rp)</label>
                            <input type="number" name="harga" min="1" required value="{{ old('harga') }}">
                        </div>
                        <div>
                            <label>Durasi (hari)</label>
                            <input type="number" name="durasi" min="1" required value="{{ old('durasi') }}">
                        </div>
                    </div>
                    <label>Proposal</label>
                    <textarea name="proposal" required>{{ old('proposal') }}</textarea>
                    <p><button type="submit">Kirim penawaran</button></p>
                </form>
            </div>
        @else
            <div class="card">
                <p class="muted">Lelang sudah ditutup; penawaran tidak dapat diajukan.</p>
            </div>
        @endif
    @endif
@else
    @if($project->isOpen())
        <div class="card">
            <p><a href="{{ route('login') }}">Masuk</a> sebagai developer untuk mengajukan penawaran.</p>
        </div>
    @endif
@endauth
@endsection