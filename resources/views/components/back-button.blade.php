@props([
    'context' => 'page',
    'href' => url()->previous(),
])
<a href="{{ $href }}"
   @class([
        'back-button',

        'back-button--hero'  => $context === 'hero',
        'back-button--page'  => $context === 'page',
        'back-button--modal' => $context === 'modal',
        'back-button--icon'  => $context === 'icon',
   ])
   aria-label="Voltar"
>
    <span class="back-button__icon">←</span>

    @if(!in_array($context, ['modal', 'icon']))
        <span class="back-button__text">Voltar</span>
    @endif
</a>


