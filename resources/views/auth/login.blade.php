@extends('layouts.base')

@section('content')
<main class="login-wrapper">
    <section class="login-container">

        <div class="page-header">
            <h1 class="page-title">K-Filmes</h1>
            <p class="page-subtitle">
                Sua biblioteca pessoal de filmes
            </p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf

            <div class="form-group">
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
            </div>

            <div class="form-group">
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
            </div>

            <button type="submit" class="button-submit">
                Entrar
            </button>
        </form>

        <footer class="login-footer">
            © {{ date('Y') }} K-Filmes
        </footer>

    </section>
</main>
@endsection
