<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PdfController extends Controller implements HasMiddleware
{
    /**
     * Middlewares du contrôleur.
     */
    public static function middleware(): array
{
    return [];
}

    /**
     * Générer le reçu PDF.
     */
    public function receipt(Payment $payment)
    {
        $payment->load([

            'enrollment.student',

            'enrollment.training',

            'paymentMethod',

            'receiver',

        ]);

        return view('pdf.receipt', [
    'payment' => $payment,
    'logo' => public_path('images/logo-small.png'),
    'qrcode' => public_path('temp/qrcode-'.$payment->id.'.png'),
    ]);

        return $pdf->stream(
    'recu-'.$payment->receipt_number.'.pdf'
    );
    }

    /**
     * Vérification publique d'un reçu.
     */
    public function verifyReceipt(string $receipt)
    {
        abort(418, 'VERIFY');
    }
}