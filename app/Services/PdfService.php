<?php

namespace App\Services;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    /**
     * Générer le reçu PDF.
     */
    public static function receipt(Payment $payment)
    {
        $payment->load([
            'enrollment.student',
            'enrollment.trainings',
            'paymentMethod',
            'receiver',
            'cashTransaction',
            'allocations.enrollment.student',
            'allocations.enrollment.trainings',
        ]);

        $logo = self::getLogo();

        $pdf = Pdf::loadView(
            'pdf.receipt',
            [
                'payment' => $payment,
                'logo' => $logo,
                'qrcode' => null,
            ]
        );

        $pdf->setPaper('A5', 'landscape');

        $pdf->getDomPDF()->getOptions()->set([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);

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