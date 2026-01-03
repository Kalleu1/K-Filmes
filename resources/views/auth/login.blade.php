@extends('layouts.base')

@section('content')
<main class="login-wrapper">
    <section class="login-container">

        <header class="login-header">
            <h2 class="login-title">Acesse sua conta</h2>
        </header>

        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf

            <label for="email">Usuário</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
            >
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror

            <label for="password">Senha</label>
            <input
                type="password"
                id="password"
                name="password"
                required
            >
            @error('password')
                <span class="form-error">{{ $message }}</span>
            @enderror

            <button type="submit" class="button-submit">
                Entrar
            </button>

            <nav class="links">
                <a href="{{ route('password.request') }}">Esqueci minha senha</a>
                
                
            </nav>
        </form>

        <hr class="divisoria">

        <footer class="rodape">
            © {{ date('Y') }} K-Filmes
        </footer>

    </section>
</main>
@endsection
