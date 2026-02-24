<?php

namespace App\Http\Controllers;

use App\Models\Filme;
use App\Support\Toast\ToastMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

class ShareController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function preview($id)
    {
        $filme = Filme::doUsuario()->findOrFail($id);
        $this->authorize('view', $filme);

        return view('filmes.share', compact('filme'));
    }

    public function generate(Request $request, $id)
    {
        $filme = Filme::doUsuario()->findOrFail($id);
        $this->authorize('view', $filme);
        
        $theme = $request->input('theme','deep-blue');

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
        $html = view('filmes.share-render', compact('filme', 'theme'))->render();

        // Injeta o CSS
        $htmlWithCss = str_replace('</head>', "<style>{$cssContent}</style></head>", $html);

        // Caminho final da imagem
        $filename = "share_{$id}_" . time() . ".png";
        $path = storage_path("app/public/shares/{$filename}");

        try {
            // Gera a imagem usando Browsershot
            Browsershot::html($htmlWithCss)
                ->setChromePath('/usr/bin/google-chrome')
                ->noSandbox()
                ->windowSize(490, 820)
                ->deviceScaleFactor(1)
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
        $filme = Filme::doUsuario()->findOrFail($id);
        $this->authorize('view', $filme);
        $theme = $request->input('theme', 'deep-blue');

        return view('filmes.share-render', compact('filme', 'theme'));
    }
}
