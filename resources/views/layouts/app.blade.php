<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') - Garuda-Hub</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
<header>
    <div class="in">
        <a class="brand" href="{{ route('home') }}">Garuda-Hub</a>
        <a href="{{ route('home') }}">Proyek</a>
        @auth
            @if(auth()->user()->role === 'klien')
                <a href="{{ route('projects.create') }}">Pasang Proyek</a>
            @endif
            @if(auth()->user()->role !== 'admin')
                <a href="{{ route('dashboard') }}">Dasbor</a>
            @endif
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.index') }}">Admin</a>
            @endif
            <span>{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
            <form class="inline" method="post" action="{{ route('logout') }}">
                @csrf
                <button class="sm sec" type="submit">Keluar</button>
            </form>
        @else
            <a href="{{ route('login') }}">Masuk</a>
            <a href="{{ route('register') }}">Daftar</a>
        @endauth
    </div>
</header>

<main>
    @if(session('ok'))
        <div class="flash ok">{{ session('ok') }}</div>
    @endif
    @if(session('err'))
        <div class="flash err">{{ session('err') }}</div>
    @endif
    @if($errors->any())
        <div class="flash err">
            <ul class="err-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<footer>Garuda-Hub - Refactored to Laravel MVC. Hanya data dummy untuk pembelajaran.</footer>
</body>
</html>