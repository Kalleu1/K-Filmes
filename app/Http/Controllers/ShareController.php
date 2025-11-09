<?php

namespace App\Http\Controllers;

use App\Models\Filme;
use Illuminate\Http\Request;

class ShareController extends Controller
{
public function gerarShareImage($id)
{
    $filme = Filme::findOrFail($id);

    $width = 1080;
    $height = 1920;
    $img = imagecreatetruecolor($width, $height);
    imageantialias($img, true);

    // -------------------
    // 🎨 Fundo gradiente com contraste mais forte
    // -------------------
$topColor = [1, 40, 64];    // #012840  
$middleColor = [39, 79, 115]; // #274F73  
$bottomColor = [14, 13, 64];  // #0E0D40
  // #000428

 
    for ($i = 0; $i < $height; $i++) {
        $ratio = $i / $height;
        $r = intval($topColor[0] * (1 - $ratio) + $bottomColor[0] * $ratio);
        $g = intval($topColor[1] * (1 - $ratio) + $bottomColor[1] * $ratio);
        $b = intval($topColor[2] * (1 - $ratio) + $bottomColor[2] * $ratio);
        $color = imagecolorallocate($img, $r, $g, $b);
        imageline($img, 0, $i, $width, $i, $color);
    }

    // -------------------
    // 🎨 Cores principais
    // -------------------
    $highlightColor = imagecolorallocate($img, 247, 231, 161); // Dourado suave
    $textColor = imagecolorallocate($img, 230, 230, 230); // Cinza claro para textos

    // -------------------
    // 🎞️ Poster centralizado com padding e sombra suave
    // -------------------
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

                // Criar sombra suave atrás do pôster 🎥
                $shadow = imagecreatetruecolor($posterWidth + 60, $posterHeight + 60);
                imagesavealpha($shadow, true);
                $transparent = imagecolorallocatealpha($shadow, 0, 0, 0, 127);
                imagefill($shadow, 0, 0, $transparent);

                // Cor e transparência da sombra
                $shadowColor = imagecolorallocatealpha($shadow, 0, 0, 0, 110);
                imagefilledrectangle($shadow, 30, 30, $posterWidth + 30, $posterHeight + 30, $shadowColor);

                // Aplicar sombra na imagem base
                imagecopy($img, $shadow, $x - 30, $posterY - 30, 0, 0, $posterWidth + 60, $posterHeight + 60);
                imagedestroy($shadow);

                // Redimensionar pôster e aplicar cantos arredondados
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

    // -------------------
    // ✍️ Fontes
    // -------------------
    $fontTitle = public_path('storage/fonts/PlayfairDisplay-Bold.ttf');
    $fontSans = public_path('storage/fonts/Lato-Bold.ttf');

    // -------------------
    // 🏷️ Título
    // -------------------
    $titulo = mb_strtoupper($filme->nome);
    $fontSizeTitulo = 34;
    $bboxTitulo = imagettfbbox($fontSizeTitulo, 0, $fontTitle, $titulo);
    $textWidth = $bboxTitulo[2] - $bboxTitulo[0];
    $xTitulo = intval(($width - $textWidth) / 2);
    $yTitulo = $posterY + $posterHeight + 80;

    imagettftext($img, $fontSizeTitulo, 0, $xTitulo, $yTitulo, $textColor, $fontTitle, $titulo);

    // -------------------
    // ⭐ Nota (sem círculo)
    // -------------------
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

    // -------------------
    // 🪶 Rodapé — centralizado
    // -------------------
    $footerText = "K-Filmes © 2025";
    $fontSizeFooter = 24;

    // 🖌️ Cor do rodapé — altere aqui se quiser mudar depois
    $footerColor = imagecolorallocate($img, 200, 200, 200); // cinza claro

    $bboxFooter = imagettfbbox($fontSizeFooter, 0, $fontSans, $footerText);
    $footerWidth = $bboxFooter[2] - $bboxFooter[0];
    $xFooter = intval(($width - $footerWidth) / 2); // centralizado
    $yFooter = $height - 60;
    imagettftext($img, $fontSizeFooter, 0, $xFooter, $yFooter, $footerColor, $fontSans, $footerText);

    // -------------------
    // 💾 Salvar imagem
    // -------------------
    $filename = 'share_' . $filme->id . '_' . time() . '.jpg';
    $path = storage_path('app/public/shares/' . $filename);

    if (!file_exists(storage_path('app/public/shares'))) {
        mkdir(storage_path('app/public/shares'), 0755, true);
    }

    imagejpeg($img, $path, 90);
    imagedestroy($img);

    $url = asset('storage/shares/' . $filename);
    return view('filmes.share', compact('url', 'filme'));
}

    // FUNÇÕES AUXILIARES

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

    
    public function gerarShareAjax(Request $request, $id)
{
    $filme = Filme::findOrFail($id);
    $cor = $request->input('cor', 'azul'); // valor padrão

    // === Mapeamento de gradientes ===
    $gradientes = [
        'azul' => [
            [0, 4, 40],     // #000428
            [0, 78, 146],   // #004e92
        ],
        'grafite' => [
            [35, 37, 38],   // #232526
            [65, 67, 69],   // #414345
        ],
        'preto' => [
            [0, 0, 0],
            [67, 67, 67],
        ],
        'roxo' => [
            [14, 13, 64],   // #0E0D40
            [39, 79, 115],  // #274F73
            [1, 40, 64],    // #012840 (opcional 3º tom)
        ],
    ];

    $cores = $gradientes[$cor] ?? $gradientes['azul'];

    // === Cria imagem base ===
    $width = 1080;
    $height = 1920;
    $img = imagecreatetruecolor($width, $height);

    // === Aplica gradiente escolhido ===
    if (count($cores) === 2) {
        [$topColor, $bottomColor] = $cores;
        for ($i = 0; $i < $height; $i++) {
            $ratio = $i / $height;
            $r = intval($topColor[0] * (1 - $ratio) + $bottomColor[0] * $ratio);
            $g = intval($topColor[1] * (1 - $ratio) + $bottomColor[1] * $ratio);
            $b = intval($topColor[2] * (1 - $ratio) + $bottomColor[2] * $ratio);
            $color = imagecolorallocate($img, $r, $g, $b);
            imageline($img, 0, $i, $width, $i, $color);
        }
    }

    // Gera o resto da imagem (poster, texto, etc)
    // ...

    // Salva imagem
    $outputPath = storage_path("app/public/share/share_{$filme->id}.jpg");
    imagejpeg($img, $outputPath, 90);
    imagedestroy($img);

    return response()->json([
        'success' => true,
        'image_url' => asset("storage/share/share_{$filme->id}.jpg"),
    ]);
}

}
