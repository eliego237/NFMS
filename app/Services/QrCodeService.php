<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;

class QrCodeService
{
    /**
     * Génère un QR Code en Base64.
     */
    public static function make(
        string $data,
        int $size = 220,
        int $margin = 10
    ): string {

        $builder = new Builder();

        $result = $builder->build(
            data: $data,
            size: $size,
            margin: $margin
        );

        return 'data:image/png;base64,' .
            base64_encode($result->getString());
    }

    /**
     * Génère les données binaires du QR Code.
     */
    public static function raw(
        string $data,
        int $size = 220,
        int $margin = 10
    ): string {

        $builder = new Builder();

        return $builder->build(
            data: $data,
            size: $size,
            margin: $margin
        )->getString();
    }

    /**
     * Génère un QR Code à partir d'une URL.
     */
    public static function url(
        string $url,
        int $size = 220
    ): string {

        return self::make($url, $size);
    }

    /**
     * Génère un QR Code contenant du texte.
     */
    public static function text(
        string $text,
        int $size = 220
    ): string {

        return self::make($text, $size);
    }
}