@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Edit Proyek</h2>

    @if ($errors->any())
        <div class="alert alert-err">
            <ul class="err-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('projects.update', $project) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="judul">Judul Proyek</label>
            <input type="text" id="judul" name="judul" value="{{ old('judul', $project->judul) }}" required maxlength="200">
        </div>

        <div class="form-group">
            <label for="kategori">Kategori</label>
            <input type="text" id="kategori" name="kategori" value="{{ old('kategori', $project->kategori) }}" required maxlength="50">
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi Proyek</label>
            <textarea id="deskripsi" name="deskripsi" rows="5" required maxlength="5000">{{ old('deskripsi', $project->deskripsi) }}</textarea>
        </div>

        <div class="form-group">
            <label for="budget_min">Budget Minimum (Rp)</label>
            <input type="number" id="budget_min" name="budget_min" value="{{ old('budget_min', $project->budget_min) }}" min="0" required>
        </div>

        <div class="form-group">
            <label for="budget_max">Budget Maksimum (Rp)</label>
            <input type="number" id="budget_max" name="budget_max" value="{{ old('budget_max', $project->budget_max) }}" min="0" required>
        </div>

        <div class="form-group">
            <label for="batas_lelang">Batas Waktu Lelang</label>
            <input type="date" id="batas_lelang" name="batas_lelang" value="{{ old('batas_lelang', $project->batas_lelang->format('Y-m-d')) }}" required>
        </div>

        <div class="action-row-lg">
            <button type="submit" class="btn">Simpan Perubahan</button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection