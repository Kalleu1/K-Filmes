<?php

namespace App\Http\Controllers;

use App\Models\Filme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

class ShareController extends Controller
{
    public function preview($id)
    {
        $filme = Filme::findOrFail($id);

        return view('filmes.share', compact('filme'));
    }

    public function generate(Request $request, $id)
    {
        $filme = Filme::findOrFail($id);

        
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

        // Gera a imagem usando Browsershot
        Browsershot::html($htmlWithCss)
            ->setOption('args', ['--no-sandbox'])
            ->setOption('viewport', ['width' => 1080, 'height' => 1920, 'deviceScaleFactor' => 1])
            ->setOption('clip', [
            'x' => 295,
            'y' => 40,
            'width' => 490,
            'height' => 780 // 
        ])
            ->save($path);

        return response()->json([
            'success' => true,
            'url' => Storage::url("shares/{$filename}")
        ]);
    }

    public function render(Request $request, $id)
    {
        $filme = Filme::findOrFail($id);
        $theme = $request->input('theme', 'deep-blue');

        return view('filmes.share-render', compact('filme', 'theme'));
    }
}
