<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PdfService;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function receipt(Payment $payment)
{
    $payment->load([
        'enrollment.student',
        'enrollment.training',
        'paymentMethod',
        'receiver',
    ]);

    return view('pdf.receipt', compact('payment'))
        ->with('logo', public_path('images/logo.png'))
        ->with('qrcode', public_path('temp/qrcode-'.$payment->id.'.png'));
}
    public function verifyReceipt(string $receipt)
    {
        abort(418, 'VERIFY');
    }
}