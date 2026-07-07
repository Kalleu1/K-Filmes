@extends('layouts.base')
@include('components.header')

@push('styles')
    @vite('resources/css/pages/discover.css')
@endpush

@push('scripts')
    @vite('resources/js/discover.js')
@endpush

@section('content')
<div class="discover-page">
    <div class="discover-container">
        <div class="discover-header">
            <x-back-button context="icon" href="{{ route('dashboard') }}" />
            <h1 class="discover-title">✨ Descobrir</h1>
            <p class="discover-subtitle">Explore novas coleções e recomendações cinematográficas</p>
        </div>

        <div class="discover-content">
            <p class="discover-placeholder-text">A funcionalidade de descoberta está sendo preparada. Em breve você poderá ver coleções e recomendações personalizadas.</p>
        </div>
    </div>
</div>
@endsection
