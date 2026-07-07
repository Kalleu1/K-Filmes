@extends('layouts.base')
@include('components.header')

@push('styles')
    @vite('resources/css/pages/discover.css')
@endpush

@push('scripts')
    @vite('resources/js/discover.js')
@endpush

@section('content')
<div class="discover-page discover-collection-page">
    <div class="discover-container">
        <div class="discover-header">
            <x-back-button context="icon" href="{{ route('discover.index') }}" />
            <h1 class="discover-title">🎬 Coleção: {{ $id }}</h1>
            <p class="discover-subtitle">Detalhes da coleção selecionada</p>
        </div>

        <div class="discover-content">
            <p class="discover-placeholder-text">Esta coleção está sendo estruturada. Os filmes da coleção {{ $id }} serão exibidos aqui.</p>
        </div>
    </div>
</div>
@endsection
