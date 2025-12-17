<?php

namespace App\Support\Toast;

class ToastManager
{
    protected static string $sessionKey = 'toasts';

    public static function push(
        string $type,
        string $message,
        int $timeout = 4000,
        bool $dismissible = true
    ): array {
        $toast = compact('type', 'message', 'timeout', 'dismissible');

        $toasts = session()->get(self::$sessionKey, []);
        $toasts[] = $toast;

        session()->flash(self::$sessionKey, $toasts);

        return [self::$sessionKey => $toasts];
    }

    /** Compatibilidade com uso antigo */
    public static function single(array $toast): array
    {
        return self::push(
            $toast['type'] ?? 'info',
            $toast['message'] ?? '',
            $toast['timeout'] ?? 4000,
            $toast['dismissible'] ?? true
        );
    }
}
