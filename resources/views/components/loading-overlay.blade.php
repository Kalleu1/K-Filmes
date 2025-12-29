<div 
    id="{{ $id ?? 'loading-overlay' }}"
    class="loading-overlay hidden"
    aria-hidden="true"
>
    <div class="loader"></div>
    <p>{{ $text ?? 'Carregando…' }}</p>
</div>
