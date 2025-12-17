<?php

namespace App\Support\Toast;

use App\Support\ToastManager;

class ToastMessages
{
    /* ========= Sucesso ========= */

    public static function movieAdded(): array
    {
        return ToastManager::push(
            'success',
            'Filme adicionado à sua biblioteca.'
        );
    }

    public static function movieUpdated(): array
    {
        return ToastManager::push(
            'success',
            'Informações do filme atualizadas com sucesso.'
        );
    }

    public static function movieDeleted(): array
    {
        return ToastManager::push(
            'success',
            'Filme removido da sua biblioteca.'
        );
    }

    public static function markedAsWatched(): array
    {
        return ToastManager::push(
            'success',
            'Filme marcado como assistido.'
        );
    }

    public static function markedAsUnwatched(): array
    {
        return ToastManager::push(
            'info',
            'Filme marcado como não assistido.'
        );
    }

    /* ========= Avisos ========= */

    public static function alreadyExists(): array
    {
        return ToastManager::push(
            'warning',
            'Este filme já está na sua biblioteca.'
        );
    }

    public static function missingData(): array
    {
        return ToastManager::push(
            'warning',
            'Alguns dados não foram informados.'
        );
    }

    /* ========= Erros ========= */

    public static function tmdbUnavailable(): array
    {
        return ToastManager::push(
            'error',
            'Não foi possível obter dados da TMDB no momento.',
            7000,
            false
        );
    }

    public static function saveFailed(): array
    {
        return ToastManager::push(
            'error',
            'Erro ao salvar o filme. Tente novamente.',
            7000,
            false
        );
    }

    public static function deleteFailed(): array
    {
        return ToastManager::push(
            'error',
            'Erro ao remover o filme.',
            7000,
            false
        );
    }

    /* ========= Genéricos ========= */

    public static function custom(
        string $type,
        string $message,
        int $timeout = 4000,
        bool $dismissible = true
    ): array {
        return ToastManager::push(
            $type,
            $message,
            $timeout,
            $dismissible
        );
    }
}
