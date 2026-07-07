@props([
    'title',
    'subtitle' => null,
    'viewAllUrl' => null,
    'icon' => null
])

<div class="collection-header">
    <div class="collection-header-info">
        <h2 class="collection-header-title">
            @if($icon)
                <span class="collection-header-icon">{{ $icon }}</span>
            @endif
            {{ $title }}
        </h2>
        @if($subtitle)
            <p class="collection-header-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if($viewAllUrl)
        <a href="{{ $viewAllUrl }}" class="collection-view-all">
            Ver Todos <i class="fa-solid fa-chevron-right"></i>
        </a>
    @endif
</div>
