<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ColorThemeService
{
    public function extract(string $imagePath): array
    {
        if (!file_exists($imagePath)) {
            return $this->fallback();
        }

        return Cache::remember(
            'color_theme_' . md5($imagePath),
            now()->addDays(30),
            fn () => $this->processImage($imagePath)
        );
    }

    private function fallback(): array
    {
        return [
            'primary'   => '34,34,34',
            'secondary' => '18,18,18',
        ];
    }

    private function findSecondary(array $palette, string $primary): string
    {
        foreach ($palette as $color) {
            if ($this->colorDistance($primary, $color) > 70) {
                return $color;
            }
        }

        return $primary;
    }

    private function colorDistance(string $a, string $b): float
        {
            [$r1, $g1, $b1] = array_map('intval', explode(',', $a));
            [$r2, $g2, $b2] = array_map('intval', explode(',', $b));

            return sqrt(
                ($r1 - $r2) ** 2 +
                ($g1 - $g2) ** 2 +
                ($b1 - $b2) ** 2
            );
        }


    private function processImage(string $path): array
    {
        $img = @imagecreatefromjpeg($path)
            ?: @imagecreatefrompng($path);

        if (!$img) {
            return $this->fallback();
        }

        // reduzir drasticamente a imagem
        $size = 40;
        $small = imagecreatetruecolor($size, $size);
        imagecopyresampled(
            $small, $img,
            0, 0, 0, 0,
            $size, $size,
            imagesx($img), imagesy($img)
        );

        $colors = [];

        for ($x = 0; $x < $size; $x++) {
            for ($y = 0; $y < $size; $y++) {
                $rgb = imagecolorat($small, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                // ignorar cores muito claras ou escuras
                if ($r + $g + $b < 60 || $r + $g + $b > 720) continue;

                $key = "$r,$g,$b";
                $colors[$key] = ($colors[$key] ?? 0) + 1;
            }
        }

        arsort($colors);
        $palette = array_keys($colors);

        $primary = $palette[0] ?? '34,34,34';
        $secondary = $this->findSecondary($palette, $primary);

        imagedestroy($img);
        imagedestroy($small);

        return compact('primary', 'secondary');
    }

}


