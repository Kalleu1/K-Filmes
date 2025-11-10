<?php

namespace App\Http\Controllers;

use App\Models\Filme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShareController extends Controller
{
    /**
     * Gera imagem de compartilhamento com tema padrão.
     */
    public function gerarShareImage($id)
    {
        $filme = Filme::findOrFail($id);

        // 🎨 Tema padrão (gradiente azul escuro)
        $defaultColors = [
            [1, 40, 64],    // #012840
            [39, 79, 115],  // #274F73
            [14, 13, 64],   // #0E0D40
        ];

        return $this->gerarShareCustom($filme, $defaultColors);
    }

    /**
     * Gera imagem via AJAX, com tema escolhido pelo usuário.
     */
    public function gerarShareAjax(Request $request, $id)
    {
        $filme = Filme::findOrFail($id);
        $theme = $request->input('theme', 'default');

        // 🎨 Paleta de temas disponíveis
        $themes = [
        'por-do-sol' => [
            [255, 94, 98],   // #FF5E62
            [255, 195, 113], // #FFC371
        ],
        'esmeralda' => [
            [0, 77, 64],     // #004D40
            [0, 150, 136],   // #009688
            [128, 203, 196], // #80CBC4
        ],
        'lavanda' => [
            [111, 78, 161],  // #6F4EA1
            [170, 132, 198], // #AA84C6
            [230, 230, 250], // #E6E6FA
        ],
        'noite-profunda' => [
            [30, 60, 114],  // #1E3C72
            [42, 82, 152],  // #2A5298
        ],
        'grafite' => [
            [35, 37, 38],   // #232526
            [65, 67, 69],   // #414345
        ],
        'dourado' => [
            [66, 46, 5],    // #422E05
            [153, 101, 21], // #996515
            [255, 215, 0],  // #FFD700
        ],
        'aurora' => [
            [37, 117, 252], // #2575FC
            [106, 17, 203], // #6A11CB
        ],
        'default' => [
                [1, 40, 64],    // #012840
                [39, 79, 115],  // #274F73
                [14, 13, 64],   // #0E0D40
            ],
        ];

        $selectedTheme = $themes[$theme] ?? $themes['default'];

        return $this->gerarShareCustom($filme, $selectedTheme, true);
    }

    /**
     * Função principal: gera o pôster 9:16 com gradiente e elementos visuais.
     */
    private function gerarShareCustom($filme, array $colors, $ajax = false)
    {
        $width = 1080;
        $height = 1920;
        $img = imagecreatetruecolor($width, $height);
        imageantialias($img, true);

        // -----------------------
        // 🎨 Fundo gradiente dinâmico
        // -----------------------
        $colorCount = count($colors);
        for ($i = 0; $i < $height; $i++) {
            $ratio = $i / $height;
            $segment = $ratio * ($colorCount - 1);
            $index = floor($segment);
            $next = min($index + 1, $colorCount - 1);
            $localRatio = $segment - $index;

            $r = intval($colors[$index][0] * (1 - $localRatio) + $colors[$next][0] * $localRatio);
            $g = intval($colors[$index][1] * (1 - $localRatio) + $colors[$next][1] * $localRatio);
            $b = intval($colors[$index][2] * (1 - $localRatio) + $colors[$next][2] * $localRatio);

            $color = imagecolorallocate($img, $r, $g, $b);
            imageline($img, 0, $i, $width, $i, $color);
        }

        // -----------------------
        // 🎨 Cores principais
        // -----------------------
        $highlightColor = imagecolorallocate($img, 247, 231, 161); // Dourado suave
        $textColor = imagecolorallocate($img, 230, 230, 230); // Cinza claro para textos

        // -----------------------
        // 🎞️ Poster centralizado com sombra
        // -----------------------
        $horizontalPadding = intval($width * 0.1);
        $verticalPadding = intval($height * 0.15);
        $posterMaxWidth = $width - ($horizontalPadding * 2);
        $posterMaxHeight = $height - ($verticalPadding * 2.2);

        if ($filme->poster) {
            $posterData = @file_get_contents($filme->poster);
            if ($posterData) {
                $poster = @imagecreatefromstring($posterData);
                if ($poster) {
                    $posterRatio = imagesx($poster) / imagesy($poster);

                    if ($posterMaxWidth / $posterMaxHeight > $posterRatio) {
                        $posterHeight = $posterMaxHeight;
                        $posterWidth = intval($posterHeight * $posterRatio);
                    } else {
                        $posterWidth = $posterMaxWidth;
                        $posterHeight = intval($posterWidth / $posterRatio);
                    }

                    $x = intval(($width - $posterWidth) / 2);
                    $posterY = intval(($height - $posterHeight) / 2.5);

                    // 🌑 Sombra suave atrás do pôster
                    $shadow = imagecreatetruecolor($posterWidth + 60, $posterHeight + 60);
                    imagesavealpha($shadow, true);
                    $transparent = imagecolorallocatealpha($shadow, 0, 0, 0, 127);
                    imagefill($shadow, 0, 0, $transparent);
                    $shadowColor = imagecolorallocatealpha($shadow, 0, 0, 0, 110);
                    imagefilledrectangle($shadow, 30, 30, $posterWidth + 30, $posterHeight + 30, $shadowColor);
                    imagecopy($img, $shadow, $x - 30, $posterY - 30, 0, 0, $posterWidth + 60, $posterHeight + 60);
                    imagedestroy($shadow);

                    // 🧱 Aplicar cantos arredondados
                    $posterResized = imagecreatetruecolor($posterWidth, $posterHeight);
                    imagesavealpha($posterResized, true);
                    $transparent = imagecolorallocatealpha($posterResized, 0, 0, 0, 127);
                    imagefill($posterResized, 0, 0, $transparent);
                    imagecopyresampled($posterResized, $poster, 0, 0, 0, 0, $posterWidth, $posterHeight, imagesx($poster), imagesy($poster));
                    $posterRounded = $this->applyRoundedCorners($posterResized, 50);
                    imagecopy($img, $posterRounded, $x, $posterY, 0, 0, $posterWidth, $posterHeight);

                    imagedestroy($poster);
                    imagedestroy($posterResized);
                    imagedestroy($posterRounded);
                }
            }
        }

        // -----------------------
        // ✍️ Fontes
        // -----------------------
        $fontTitle = public_path('storage/fonts/PlayfairDisplay-Bold.ttf');
        $fontSans = public_path('storage/fonts/Lato-Bold.ttf');

        // -----------------------
        // 🏷️ Título
        // -----------------------
        $titulo = mb_strtoupper($filme->nome);
        $fontSizeTitulo = 34;
        $bboxTitulo = imagettfbbox($fontSizeTitulo, 0, $fontTitle, $titulo);
        $textWidth = $bboxTitulo[2] - $bboxTitulo[0];
        $xTitulo = intval(($width - $textWidth) / 2);
        $yTitulo = $posterY + $posterHeight + 80;
        imagettftext($img, $fontSizeTitulo, 0, $xTitulo, $yTitulo, $textColor, $fontTitle, $titulo);

        // -----------------------
        // ⭐ Nota
        // -----------------------
        if ($filme->nota) {
            $nota = number_format($filme->nota, 1, ',', '') . '/10';
            $fontSizeNota = 46;
            $bboxNota = imagettfbbox($fontSizeNota, 0, $fontSans, $nota);
            $notaWidth = $bboxNota[2] - $bboxNota[0];
            $xNota = intval(($width - $notaWidth) / 2);
            $yNota = $yTitulo + 90;

            $shadow = imagecolorallocatealpha($img, 0, 0, 0, 90);
            imagettftext($img, $fontSizeNota, 0, $xNota + 2, $yNota + 2, $shadow, $fontSans, $nota);
            imagettftext($img, $fontSizeNota, 0, $xNota, $yNota, $highlightColor, $fontSans, $nota);
        }

        // -----------------------
        // 🪶 Rodapé
        // -----------------------
        $footerText = "K-Filmes © 2025";
        $fontSizeFooter = 24;
        $footerColor = imagecolorallocate($img, 200, 200, 200);
        $bboxFooter = imagettfbbox($fontSizeFooter, 0, $fontSans, $footerText);
        $footerWidth = $bboxFooter[2] - $bboxFooter[0];
        $xFooter = intval(($width - $footerWidth) / 2);
        $yFooter = $height - 60;
        imagettftext($img, $fontSizeFooter, 0, $xFooter, $yFooter, $footerColor, $fontSans, $footerText);

        // -----------------------
        // 💾 Salvar imagem
        // -----------------------
        $filename = 'share_' . $filme->id . '_' . time() . '.jpg';
        $path = public_path('storage/shares/' . $filename);
        if (!file_exists(public_path('storage/shares'))) {
            mkdir(public_path('storage/shares'), 0755, true);
        }

        imagejpeg($img, $path, 90);
        imagedestroy($img);

        $url = asset('storage/shares/' . $filename);

        if ($ajax) {
            return response()->json(['url' => $url]);
        }

        return view('filmes.share', compact('url', 'filme'));
    }

    /**
     * Cria cantos arredondados no pôster.
     */
    private function applyRoundedCorners($image, $radius)
    {
        $w = imagesx($image);
        $h = imagesy($image);

        $mask = imagecreatetruecolor($w, $h);
        imagesavealpha($mask, true);
        $transparent = imagecolorallocatealpha($mask, 0, 0, 0, 127);
        imagefill($mask, 0, 0, $transparent);
        $opaque = imagecolorallocatealpha($mask, 0, 0, 0, 0);

        imagefilledrectangle($mask, $radius, 0, $w - $radius, $h, $opaque);
        imagefilledrectangle($mask, 0, $radius, $w, $h - $radius, $opaque);
        imagefilledellipse($mask, $radius, $radius, $radius * 2, $radius * 2, $opaque);
        imagefilledellipse($mask, $w - $radius, $radius, $radius * 2, $radius * 2, $opaque);
        imagefilledellipse($mask, $radius, $h - $radius, $radius * 2, $radius * 2, $opaque);
        imagefilledellipse($mask, $w - $radius, $h - $radius, $radius * 2, $radius * 2, $opaque);

        imagealphablending($image, false);
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $alpha = imagecolorat($mask, $x, $y) & 0x7F000000;
                $color = imagecolorat($image, $x, $y);
                imagesetpixel($image, $x, $y, ($color & 0x00FFFFFF) | $alpha);
            }
        }

        imagedestroy($mask);
        return $image;
    }

private function interpolateColor($color1, $color2, $t)
{
    $c1 = sscanf($color1, "#%02x%02x%02x");
    $c2 = sscanf($color2, "#%02x%02x%02x");

    $r = (int) ($c1[0] + ($c2[0] - $c1[0]) * $t);
    $g = (int) ($c1[1] + ($c2[1] - $c1[1]) * $t);
    $b = (int) ($c1[2] + ($c2[2] - $c1[2]) * $t);

    return sprintf("#%02x%02x%02x", $r, $g, $b);
}

}
