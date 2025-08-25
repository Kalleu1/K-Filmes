<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>K-Filmes</title>
    @vite('resources/css/app.css')
</head>
<body>
    <header>
        <h1> Meus filmes</h1>

        <nav>
            <a href="{{route('filmes.index')}}">Home</a>
            <a href="{{route('filmes.create')}}">Cadastrar um novo filme</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; 2025 Meus Filmes</p>
    </footer>
</body>
</html>