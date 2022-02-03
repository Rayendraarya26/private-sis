<?php

use Intervention\Image\Image;

if (!function_exists('certMakeMultiline')) {
    function makeMultiline(string $string, int $maxLength = 50): string
    {
        $words        = explode(" ", $string);
        $stringResult = "";
        $lines        = [];
        foreach ($words as $word) {
            if ((strlen($stringResult) + 1 + strlen($word)) > $maxLength) {
                $lines[]      = $stringResult;
                $stringResult = $word;
            } else {
                $stringResult = $stringResult . " " . $word;
            }
        }

        $lines[] = $stringResult;

        $stringResult = implode("\n", $lines);

        return trim($stringResult);
    }
}

if (!function_exists('certRenderMultiline')) {
    function renderMultiline(Image $img, $config, string $longText, int $maxLength = 40)
    {
        $xAxis    = $config['xAxis'];
        $yAxis    = $config['yAxis'];
        $fontType = $config['fontType'];
        $fontSize = $config['size'];
        $color    = $config['color'];
        $align    = $config['align'];
        $valign   = $config['valign'];

        $data      = makeMultiline($longText, $maxLength);
        $dataArray = explode("\n", $data);
        $loop      = 0;
        foreach ($dataArray as $text) {
            if ($loop > 0) {
                $img->text($text, $xAxis, $yAxis + ($fontSize * $loop), function ($font) use ($fontType, $color, $fontSize, $align, $valign) {
                    $font->file($fontType);
                    $font->size($fontSize);
                    $font->color($color);
                    $font->align($align);
                    $font->valign($valign);
                });
            } else {
                $img->text($text, $xAxis, $yAxis, function ($font) use ($fontType, $color, $fontSize, $align, $valign) {
                    $font->file($fontType);
                    $font->size($fontSize);
                    $font->color($color);
                    $font->align($align);
                    $font->valign($valign);
                });
            }
            $loop++;
        }
    }
}

if (!function_exists('certMergeImage')) {
    /**
     * @throws ImagickException
     */
    function certMergeImage(string $pdfName, array $imageList, bool $deleteAfterMerge = true): string
    {
        $imagic = new Imagick($imageList);
        $imagic->setImageFormat('pdf');
        $imagic->writeImages($pdfName, true);

        if ($deleteAfterMerge) {
            foreach ($imageList as $p) {
                @unlink($p);
            }
        }

        return public_path($pdfName);
    }
}
