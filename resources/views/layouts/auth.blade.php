@extends('layouts.base') {{-- ou o nome real desse layout base --}}

@section('content')
    <main class="login-screen">
        <div class="login-card">
            <div class="login-brand">
                <a href="/">
                    <x-application-logo />
                </a>
            </div>

            {{ $slot }}
        </div>
    </main>
@endsection
