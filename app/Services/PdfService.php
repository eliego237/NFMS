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

        $logo = null;

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

            mkdir(
                public_path('temp'),
                0755,
                true
            );
        }

        $qrPath = public_path(
            'temp/qrcode-' .
            $payment->id .
            '.png'
        );

        file_put_contents(
            $qrPath,
            $result->getString()
        );

        /*
        |--------------------------------------------------------------------------
        | Génération du PDF
        |--------------------------------------------------------------------------
        */
 
        $html = view('pdf.receipt', [
    'payment' => $payment,
    'logo' => $logo,
    'qrcode' => $qrcode,
])->render();

dd($html);

      $html = view('pdf.receipt', [
    'payment' => $payment,
    'logo' => $logo,
    'qrcode' => $qrPath,
])->render();

$pdf = Pdf::loadHTML($html);

$pdf->setPaper('A5', 'landscape');

return $pdf->stream('test.pdf');
    }
}