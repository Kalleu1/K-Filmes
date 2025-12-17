<?php

if (!function_exists('toast')) {
    function toast(
        string $type,
        string $message,
        int $timeout = 4000,
        array $options = []
    ): array {
        return [
            'toast' => array_merge([
                'type' => $type,
                'message' => $message,
                'timeout' => $timeout,
            ], $options),
        ];
    }
}
