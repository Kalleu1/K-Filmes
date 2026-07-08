@props([
    'context' => 'page',
    'href' => url()->previous(),
])
<a href="{{ $href }}"
   onclick="if(window.history.length > 1) { window.history.back(); return false; }"
   @class([
        'back-button',

        'back-button--hero'  => $context === 'hero',
        'back-button--page'  => $context === 'page',
        'back-button--modal' => $context === 'modal',
        'back-button--icon'  => $context === 'icon',
   ])
   aria-label="Voltar"
>
    <i class="fa-solid fa-angle-left"></i>

    @if(!in_array($context, ['modal', 'icon']))
        <span class="back-button__text">Voltar</span>
    @endif
</a>


