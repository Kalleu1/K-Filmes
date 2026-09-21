<?php

namespace App\Http\Controllers;

use App\Models\Filme;
use App\Services\TMDBService;
use App\Support\Toast\ToastMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

class ShareController extends Controller
{
    protected $tmdb;

    public function __construct(TMDBService $tmdb)
    {
        $this->middleware('auth');
        $this->tmdb = $tmdb;
    }

    private function resolveBackdropUrl(Filme $filme, ?string $customUrl = null): string
    {
        if (!empty($customUrl)) {
            return $customUrl;
        }

        if (!empty($filme->poster_banner_url)) {
            return $filme->poster_banner_url;
        }

        if ($filme->tmdb_id) {
            try {
                $tmdbData = $this->tmdb->getMovie((int) $filme->tmdb_id);
                if (!empty($tmdbData['backdrop_path'])) {
                    return $this->tmdb->getImageUrl($tmdbData['backdrop_path'], 'w1280');
                }
            } catch (\Throwable $e) {
                // se falhar a chamada externa, continua pro fallback
            }
        }

        return $filme->poster_url ?? asset('imgs/no-poster.jpg');
    }

    private function resolvePosterUrl(Filme $filme, ?string $customUrl = null): string
    {
        if (!empty($customUrl)) {
            return $customUrl;
        }

        return $filme->poster_url ?? asset('imgs/no-poster.jpg');
    }

    public function preview($id)
    {
        $filme = Filme::doUsuario()->with('user')->findOrFail($id);
        $this->authorize('view', $filme);

        $backdropUrl = $this->resolveBackdropUrl($filme);
        $posterUrl = $this->resolvePosterUrl($filme);

        return view('filmes.share', compact('filme', 'backdropUrl', 'posterUrl'));
    }

    public function generate(Request $request, $id)
    {
        $filme = Filme::doUsuario()->with('user')->findOrFail($id);
        $this->authorize('view', $filme);
        
        $theme = $request->input('theme', 'movie-backdrop');
        $backdropUrl = $this->resolveBackdropUrl($filme, $request->input('custom_backdrop_url'));
        $posterUrl = $this->resolvePosterUrl($filme, $request->input('custom_poster_url'));

        // LISTA DE CSS FIXOS + TEMA DINAMICO
        $cssFiles = [
            resource_path('css/pages/share.css'),
            resource_path('css/pages/shareThemes/share-base.css'),
            resource_path('css/pages/shareThemes/share-layout.css'),
            resource_path('css/pages/shareThemes/share-variables.css'),
            resource_path("css/pages/shareThemes/themes/{$theme}.css"),
        ];

        // Concatena os CSS existentes
        $cssContent = '';
        foreach ($cssFiles as $file) {
            if (file_exists($file)) {
                $cssContent .= file_get_contents($file) . "\n";
            }
        }

        // Renderiza a view Blade
        $html = view('filmes.share-render', compact('filme', 'theme', 'backdropUrl', 'posterUrl'))->render();

        // Injeta o CSS
        $htmlWithCss = str_replace('</head>', "<style>{$cssContent}</style></head>", $html);

        // Caminho final da imagem
        $filename = "share_{$id}_" . time() . ".png";
        $path = storage_path("app/public/shares/{$filename}");

        try {
            // Gera a imagem usando Browsershot (540x960 @ 2x = 1080x1920 Full HD Story)
            Browsershot::html($htmlWithCss)
                ->setChromePath('/usr/bin/google-chrome')
                ->noSandbox()
                ->windowSize(540, 960)
                ->deviceScaleFactor(2)
                ->save($path);

            return response()->json([
                'success' => true,
                'url' => Storage::url("shares/{$filename}"),
                'toast' => ToastMessages::shareImageGeneratedPayload(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao gerar imagem de compartilhamento.',
                'toast' => ToastMessages::shareImageGenerationFailedPayload(),
            ], 500);
        }
    }

    public function render(Request $request, $id)
    {
        $filme = Filme::doUsuario()->with('user')->findOrFail($id);
        $this->authorize('view', $filme);
        $theme = $request->input('theme', 'movie-backdrop');
        $backdropUrl = $this->resolveBackdropUrl($filme, $request->input('custom_backdrop_url'));
        $posterUrl = $this->resolvePosterUrl($filme, $request->input('custom_poster_url'));

        return view('filmes.share-render', compact('filme', 'theme', 'backdropUrl', 'posterUrl'));
    }
}
