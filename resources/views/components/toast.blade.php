@if(session()->has('toasts'))
    <script>
        window.toasts = @json(session('toasts'));
    </script>
@endif
