<?php

namespace App\Services;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class PdfService
{
    /**
     * Générer le reçu PDF.
     */
    public static function receipt(Payment $payment)
    {
        $payment->load([
            'enrollment.student',
            'enrollment.training',
            'paymentMethod',
            'receiver',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Logo
        |--------------------------------------------------------------------------
        */

        $logo = 'data:image/png;base64,' . base64_encode(
    file_get_contents(public_path('images/logo-small.png'))
);

        /*
        |--------------------------------------------------------------------------
        | URL de vérification
        |--------------------------------------------------------------------------
        */

        $verificationUrl = route(
            'receipt.verify',
            $payment->receipt_number
        );

        /*
        |--------------------------------------------------------------------------
        | Génération du QR Code
        |--------------------------------------------------------------------------
        */

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($verificationUrl)
            ->size(220)
            ->margin(10)
            ->build();

        /*
        |--------------------------------------------------------------------------
        | Sauvegarde temporaire
        |--------------------------------------------------------------------------
        */

        if (!file_exists(public_path('temp'))) {
            mkdir(public_path('temp'), 0755, true);
        }

        $qrPath = public_path(
            'temp/qrcode-' . $payment->id . '.png'
        );

        file_put_contents(
            $qrPath,
            $result->getString()
        );

        /*
        |--------------------------------------------------------------------------
        | QR Code en Base64
        |--------------------------------------------------------------------------
        */

        $qrcode = 'data:image/png;base64,' . base64_encode(
            file_get_contents($qrPath)
        );

        /*
        |--------------------------------------------------------------------------
        | Génération du PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'pdf.receipt',
            [
                'payment' => $payment,
                'logo'    => $logo,
                'qrcode'  => $qrcode,
            ]
        );

        $pdf->setPaper('A5', 'landscape');

        return $pdf->stream(
            'REC-' . $payment->receipt_number . '.pdf'
        );
    }
}