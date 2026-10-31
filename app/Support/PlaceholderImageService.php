<?php

namespace App\Support;

use App\Models\Vehicle;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PlaceholderImageService
{
    public const WIDTH = 960;
    public const HEIGHT = 640;

    public function generateForVehicle(Vehicle $vehicle, string $disk = 'public'): array
    {
        $titles = [
            'Front View', 'Side View', 'Interior', 'Engine', 'Rear View', 'Dashboard',
        ];

        $paths = [];

        foreach ($titles as $index => $title) {
            $filename = Str::slug($vehicle->displayName()).'-'.($index + 1).'.webp';
            $fullPath = 'vehicles/'.$vehicle->ulid.'/'.$filename;

            $image = $this->render($vehicle->displayName(), $title, $vehicle->price);

            $storage = Storage::disk($disk);
            $tmp = tempnam(sys_get_temp_dir(), 'wcimg').'.webp';
            imagewebp($image, $tmp, 82);
            $storage->put($fullPath, file_get_contents($tmp));

            $thumbPath = 'vehicles/'.$vehicle->ulid.'/thumbs/'.$filename;
            $thumb = $this->thumbnail($image);
            $tmp2 = tempnam(sys_get_temp_dir(), 'wcthumb').'.webp';
            imagewebp($thumb, $tmp2, 80);
            $storage->put($thumbPath, file_get_contents($tmp2));

            imagedestroy($image);
            imagedestroy($thumb);
            @unlink($tmp);
            @unlink($tmp2);

            $paths[] = [
                'path' => $fullPath,
                'thumbnail' => $thumbPath,
                'alt' => $vehicle->displayName().' - '.$title,
                'main' => $index === 0,
                'sort_order' => $index,
            ];
        }

        return $paths;
    }

    protected function render(string $vehicleName, string $viewTitle, $price): \GdImage
    {
        $w = self::WIDTH;
        $h = self::HEIGHT;
        $image = imagecreatetruecolor($w, $h);

        $top = [8, 12, 22]; // deep navy
        $bottom = [18, 20, 26]; // near black

        for ($y = 0; $y < $h; $y++) {
            $t = $y / $h;
            $r = (int) ($top[0] + ($bottom[0] - $top[0]) * $t);
            $g = (int) ($top[1] + ($bottom[1] - $top[1]) * $t);
            $b = (int) ($top[2] + ($bottom[2] - $top[2]) * $t);
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $y, $w, $y, $color);
        }

        $gold = imagecolorallocate($image, 212, 175, 55);
        $cream = imagecolorallocate($image, 244, 240, 228);

        for ($i = 0; $i < 5; $i++) {
            $alpha = (int) (14 - $i * 2);
            imagefilledellipse($image, (int) ($w * 0.18 + $i * 60), (int) ($h * 0.35), 160, 160, imagecolorallocatealpha($image, $gold >> 16, ($gold >> 8) & 0xFF, $gold & 0xFF, $alpha));
        }

        imagefilledrectangle($image, 60, (int) ($h * 0.62), 70, (int) ($h * 0.62) + 90, $gold);
        imagefilledrectangle($image, $w - 70, (int) ($h * 0.62), $w - 60, (int) ($h * 0.62) + 90, $gold);

        $font = $this->fontPath();

        if ($font) {
            imagettftext($image, 13, 0, 64, 420, $gold, $font, 'WORXCARS');
            imagettftext($image, 34, 0, 64, 480, $cream, $font, strtoupper($vehicleName));
            imagettftext($image, 18, 0, 64, 520, $gold, $font, $viewTitle);
            imagettftext($image, 24, 0, 64, 585, $cream, $font, 'R'.number_format((float) $price, 0, ',', ' '));
            imagettftext($image, 33, 0, 200, 120, $gold, $font, 'BUY YOUR CAR. GET PAID.');
        } else {
            imagestring($image, 5, 64, 460, strtoupper($vehicleName), $cream);
            imagestring($image, 4, 64, 500, 'R'.number_format((float) $price, 0, ',', ' '), $gold);
        }

        return $image;
    }

    protected function thumbnail(\GdImage $source): \GdImage
    {
        $tw = 480;
        $th = 320;
        $thumb = imagecreatetruecolor($tw, $th);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $tw, $th, self::WIDTH, self::HEIGHT);

        return $thumb;
    }

    protected function fontPath(): ?string
    {
        $candidates = [
            'C:\Windows\Fonts\seguisb.ttf',
            'C:\Windows\Fonts\arialbd.ttf',
            'C:\Windows\Fonts\arial.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
        ];

        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    public function generateLogo(string $disk = 'public'): ?string
    {
        $w = 512;
        $h = 512;
        $image = imagecreatetruecolor($w, $h);

        $dark = imagecolorallocate($image, 12, 15, 22);
        imagefill($image, 0, 0, $dark);

        $gold = imagecolorallocate($image, 212, 175, 55);
        $font = $this->fontPath();

        for ($i = 0; $i < 6; $i++) {
            imagefilledellipse($image, (int) ($w * (0.2 + $i * 0.12)), 240, 120, 120, imagecolorallocatealpha($image, 212, 175, 55, 16 - $i));
        }

        if ($font) {
            imagettftext($image, 64, 0, 56, 250, $gold, $font, 'WORX');
            imagettftext($image, 64, 0, 290, 250, imagecolorallocate($image, 244, 240, 228), $font, 'CARS');
        } else {
            imagestring($image, 5, 80, 240, 'WORXCARS', $gold);
        }

        $tmp = tempnam(sys_get_temp_dir(), 'wclogo').'.png';
        imagepng($image, $tmp);
        $storage = Storage::disk($disk);
        $storage->put('brand/worxcars-logo.png', file_get_contents($tmp));

        imagedestroy($image);
        @unlink($tmp);

        return 'brand/worxcars-logo.png';
    }

    public function generateHero(string $disk = 'public'): ?string
    {
        $storage = Storage::disk($disk);

        if ($storage->exists('brand/hero.webp')) {
            return 'brand/hero.webp';
        }

        $w = 1600;
        $h = 900;
        $image = imagecreatetruecolor($w, $h);

        $stops = [
            [10, 14, 22], [16, 22, 32], [26, 34, 48], [12, 16, 24], [8, 10, 15],
        ];

        $n = count($stops);
        for ($y = 0; $y < $h; $y++) {
            $seg = min($n - 1, (int) (($y / $h) * $n));
            $t = (($y / $h) * $n) - $seg;
            $from = $stops[$seg];
            $to = $stops[min($n - 1, $seg + 1)];
            $r = (int) ($from[0] + ($to[0] - $from[0]) * $t);
            $g = (int) ($from[1] + ($to[1] - $from[1]) * $t);
            $b = (int) ($from[2] + ($to[2] - $from[2]) * $t);
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $y, $w, $y, $color);
        }

        $gold = imagecolorallocate($image, 212, 175, 55);
        $cream = imagecolorallocate($image, 246, 242, 232);

        for ($i = 0; $i < 8; $i++) {
            imagefilledellipse($image, (int) ($w * (0.12 + $i * 0.11)), (int) ($h * 0.3), 90, 90, imagecolorallocatealpha($image, 212, 175, 55, 12 - ($i % 4)));
        }

        imagefilledrectangle($image, (int) ($w * 0.30), (int) ($h * 0.62), (int) ($w * 0.30) + 8, (int) ($h * 0.62) + 110, $gold);
        imagefilledrectangle($image, (int) ($w * 0.66), (int) ($h * 0.62), (int) ($w * 0.66) + 8, (int) ($h * 0.62) + 110, $gold);

        $font = $this->fontPath();

        if ($font) {
            imagettftext($image, 26, 0, (int) ($w * 0.30), (int) ($h * 0.46), $gold, $font, 'WORXCARS');
            imagettftext($image, 72, 0, (int) ($w * 0.30), (int) ($h * 0.55), $cream, $font, 'BUY YOUR CAR.');
            imagettftext($image, 72, 0, (int) ($w * 0.30), (int) ($h * 0.64), $gold, $font, 'GET PAID.');
            imagettftext($image, 22, 0, (int) ($w * 0.30), (int) ($h * 0.71), $cream, $font, 'Premium vehicles. Trusted sellers. Real buyer rewards.');
        }

        $tmp = tempnam(sys_get_temp_dir(), 'wchero').'.webp';
        imagewebp($image, $tmp, 85);
        $storage->put('brand/hero.webp', file_get_contents($tmp));

        imagedestroy($image);
        @unlink($tmp);

        return 'brand/hero.webp';
    }
}