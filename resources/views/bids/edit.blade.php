@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Edit Penawaran: {{ $bid->project->judul }}</h2>

    @if ($errors->any())
        <div class="alert alert-err">
            <ul class="err-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('bids.update', $bid) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="harga">Harga Penawaran (Rp)</label>
            <input type="number" id="harga" name="harga" value="{{ old('harga', $bid->harga) }}" min="1" required>
        </div>

        <div class="form-group">
            <label for="durasi">Estimasi Durasi Pengerjaan (Hari)</label>
            <input type="number" id="durasi" name="durasi" value="{{ old('durasi', $bid->durasi) }}" min="1" max="3650" required>
        </div>

        <div class="form-group">
            <label for="proposal">Proposal Penawaran</label>
            <textarea id="proposal" name="proposal" rows="5" required maxlength="5000">{{ old('proposal', $bid->proposal) }}</textarea>
        </div>

        <div class="action-row-lg">
            <button type="submit" class="btn">Simpan Perubahan</button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection