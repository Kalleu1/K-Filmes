@props([
    'context' => 'page',
])

<a href="{{ route('dashboard') }}"
   @class([
        'back-button',
        'back-button--dashboard',

        'back-button--hero'  => $context === 'hero',
        'back-button--page'  => $context === 'page',
        'back-button--modal' => $context === 'modal',
        'back-button--icon'  => $context === 'icon',
   ])
   aria-label="Voltar para o Dashboard"
>
    <span class="back-button__icon">←←</span>

    @if(!in_array($context, ['modal', 'icon']))
        <span class="back-button__text">Dashboard</span>
    @endif
</a>
