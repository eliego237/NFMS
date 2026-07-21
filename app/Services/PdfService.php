<?php

namespace App\Services;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    /**
     * Générer le reçu PDF.
     */
    public static function receipt(
        Payment $payment
    ) {
        /*
        |--------------------------------------------------------------------------
        | Chargement des relations
        |--------------------------------------------------------------------------
        */

        $payment->loadMissing([

            'enrollment.student',

            'enrollment.training',

            'paymentMethod',

            'receiver',

            'cashTransaction',

        ]);

        /*
        |--------------------------------------------------------------------------
        | Logo
        |--------------------------------------------------------------------------
        */

        $logo = self::getLogo();

        /*
        |--------------------------------------------------------------------------
        | QR Code
        |--------------------------------------------------------------------------
        */

        $verificationUrl = route(

            'receipt.verify',

            $payment->receipt_number

        );

        $qrcode = QrCodeService::url($verificationUrl);

        /*
        |--------------------------------------------------------------------------
        | Génération du PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(

            'pdf.receipt',

            [

                'payment' => $payment,

                'logo' => $logo,

                'qrcode' => $qrcode,

            ]

        );

        /*
        |--------------------------------------------------------------------------
        | Configuration DomPDF
        |--------------------------------------------------------------------------
        */

        $pdf->setPaper(

            'A5',

            'landscape'

        );

        
        /*
        |--------------------------------------------------------------------------
        | Retour
        |--------------------------------------------------------------------------
        */

        return $pdf->stream(

            sprintf(

                'recu-%s.pdf',

                $payment->receipt_number

            )

        );
    }

    /**
     * Retourner le logo de l'école en Base64.
     */
    private static function getLogo(): ?string
    {
        $path = public_path(

            'images/logo-small.png'

        );

        if (! file_exists($path)) {

            return null;

        }

        return 'data:image/png;base64,' .

            base64_encode(

                file_get_contents($path)

            );
    }
}