<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">

<title>Reçu de paiement</title>

<style>

@page{
    size:A5 landscape;
    margin:8mm;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    font-family:DejaVu Sans,sans-serif;

    font-size:12px;

    color:#333;

}

.wrapper{

    position:relative;

    width:100%;

    border:1px solid #cfcfcf;

    padding:8px;

}

/*==================================================
FILIGRANE NEW FASHION
==================================================*/

.watermark{

    position:absolute;

    top:55px;

    left:0;

    width:100%;

    text-align:center;

    color:#0d6efd;

    font-size:24px;

    line-height:32px;

    font-weight:bold;

    opacity:.05;

}

/*==================================================
CACHET PAYE
==================================================*/

.paid{

    position:absolute;

    top:130px;

    left:700px;

    font-size:90px;

    color:#dc3545;

    font-weight:bold;

    opacity:.10;

}

/*==================================================
HEADER
==================================================*/

.header{

    width:100%;

    border-bottom:2px solid #0d6efd;

    padding-bottom:6px;

    margin-bottom:8px;

}

.header table{

    width:100%;

    border-collapse:collapse;

}

.logo{

    width:15%;

    text-align:center;

}

.logo img{

    width:60px;

}

.school{

    width:60%;

    text-align:center;

}

.school-name{

    color:#0d6efd;

    font-size:40px;

    font-weight:bold;

}

.school-sub{

    font-size:12px;

    font-weight:bold;

    margin-top:2px;

}

.school-info{

    font-size:9px;

    color:#666;

}

.receipt{

    width:25%;

}

.receipt-box{

    border:2px solid #198754;

}

.receipt-title{

    background:#198754;

    color:#fff;

    text-align:center;

    padding:5px;

    font-weight:bold;

    font-size:9px;

}

.receipt-number{

    text-align:center;

    color:#dc3545;

    font-size:16px;

    font-weight:bold;

    padding-top:8px;

}

.receipt-date{

    text-align:center;

    color:#777;

    padding:6px;

    font-size:12px;

}

/*==================================================
TABLEAUX
==================================================*/

.table{

    width:100%;

    border-collapse:collapse;

    margin-top:7px;

}

.table th{

    background:#0d6efd;

    color:white;

    padding:5px;

    border:1px solid #0d6efd;

    font-size:12px;

}

.table td{

    border:1px solid #d8d8d8;

    padding:6px;

    vertical-align:top;

}

.label{

    color:#0d6efd;

    font-weight:bold;

    font-size:12px;

}

.value{

    font-size:12px;

    color:#333;

}

.amount{

    color:#198754;

    font-size:12px;

    font-weight:bold;

}

.red{

    color:#dc3545;

    font-weight:bold;

}

.center{

    text-align:center;

}

.small{

    font-size:9px;

}

.space{

    height:5px;

}

/*==================================================
FOOTER
==================================================*/

.footer{

    margin-top:6px;

    border-top:1px solid #d8d8d8;

    padding-top:5px;

    font-size:9px;

    text-align:center;

    color:#555;

}

</style>

</head>

<body>

<div class="wrapper">

<!-- ==================================================
FILIGRANE NEW FASHION
================================================== -->

<div class="watermark">

NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION
NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION

</div>

<!-- ==================================================
CACHET PAYE
================================================== -->

<div class="paid">

PAYÉ

</div>

<!-- ==================================================
HEADER
================================================== -->

<div class="header">

<table>

<tr>

<td class="logo">

<img src="{{ $logo }}">

</td>

<td class="school">

<div class="school-name">

NEW FASHION

</div>

<div class="school-sub">

Institut de Beauté & Centre de Formation Professionnelle

</div>

<div class="school-info">

Douala - Ndogbong Face Socaver

</div>

<div class="school-info">

(+237) 676 26 68 65 • (+237) 655 36 19 46

</div>

<div class="school-info">

monkammarche@gmail.com

</div>

<div class="school-info">

www.newfashioncm.com

</div>

</td>

<td class="receipt">

<div class="receipt-box">

<div class="receipt-title">

REÇU DE PAIEMENT

</div>

<div class="receipt-number">

{{ $payment->receipt_number }}

</div>

<div class="receipt-date">

{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}

</div>

</div>

</td>

</tr>

</table>

</div>

<!-- ==================================================
ETUDIANT / FORMATION / PAIEMENT
================================================== -->

<table class="table">

<tr>

<th width="33%">

ÉTUDIANT

</th>

<th width="34%">

FORMATION

</th>

<th width="33%">

PAIEMENT

</th>

</tr>

<tr>

<td>

<div class="label">Matricule</div>

<div class="value">

{{ $payment->enrollment->student->matricule }}

</div>

<div class="space"></div>

<div class="label">

Nom complet

</div>

<div class="value">

{{ $payment->enrollment->student->first_name }}
{{ $payment->enrollment->student->last_name }}

</div>

<div class="space"></div>

<div class="label">

Téléphone

</div>

<div class="value">

{{ $payment->enrollment->student->phone }}

</div>

<div class="space"></div>

<div class="label">

Email

</div>

<div class="value">

{{ $payment->enrollment->student->email ?? '-' }}

</div>

</td>

<td>

<div class="label">

Code Formation

</div>

<div class="value">

{{ $payment->enrollment->training->code }}

</div>

<div class="space"></div>

<div class="label">

Formation

</div>

<div class="value">

{{ $payment->enrollment->training->title }}

</div>

<div class="space"></div>

<div class="label">

Durée

</div>

<div class="value">

{{ $payment->enrollment->training->duration_months }} mois

</div>

<div class="space"></div>

<div class="label">

Catégorie

</div>

<div class="value">

{{ $payment->enrollment->training->category }}

</div>

</td>

<td>

<div class="label">

Référence

</div>

<div class="value">

{{ $payment->receipt_number }}

</div>

<div class="space"></div>

<div class="label">

Date

</div>

<div class="value">

{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}

</div>

<div class="space"></div>

<div class="label">

Montant payé

</div>

<div class="amount">

{{ number_format($payment->amount,0,',',' ') }} FCFA

</div>

<div class="space"></div>

<div class="label">

Mode de paiement

</div>

<div class="value">

{{ $payment->paymentMethod->name }}

</div>

<div class="space"></div>

<div class="label">

Caissier

</div>

<div class="value">

{{ $payment->receiver->name }}

</div>

</td>

</tr>

</table>

<!-- ==================================================
RÉCAPITULATIF FINANCIER
================================================== -->

<table class="table">

<tr>

<th colspan="2">

RÉCAPITULATIF FINANCIER

</th>

</tr>

<tr>

<td width="65%">

<div class="label">

Numéro d'inscription

</div>

<div class="value">

{{ $payment->enrollment->enrollment_number }}

</div>

<div class="space"></div>

<div class="label">

Année académique

</div>

<div class="value">

{{ $payment->enrollment->academic_year }}

</div>

<div class="space"></div>

<div class="label">

Statut

</div>

<div class="value">

{{ $payment->enrollment->formatted_status }}

</div>

<div class="space"></div>

<div class="label">

Progression du paiement

</div>

<div class="value">

{{ $payment->enrollment->payment_progress }} %

</div>

</td>

<td width="35%">

<table style="width:100%;border-collapse:collapse;">

<tr>

<td>Frais d'inscription</td>

<td align="right">

{{ $payment->enrollment->formatted_registration_fee }}

</td>

</tr>

<tr>

<td>Frais de formation</td>

<td align="right">

{{ $payment->enrollment->formatted_training_fee }}

</td>

</tr>

<tr>

<td>Remise</td>

<td align="right">

{{ $payment->enrollment->formatted_discount }}

</td>

</tr>

<tr>

<td><b>Total</b></td>

<td align="right">

{{ number_format($payment->enrollment->total_amount,0,',',' ') }} FCFA

</td>

</tr>

<tr>

<td><b>Déjà payé</b></td>

<td align="right">

{{ $payment->enrollment->formatted_amount_paid }}

</td>

</tr>

<tr>

<td>

<b>Reste à payer</b>

</td>

<td align="right" class="red">

{{ $payment->enrollment->formatted_balance }}

</td>

</tr>

</table>

</td>

</tr>

</table>

<!-- ==================================================
QR CODE / OBSERVATIONS / SIGNATURES
================================================== -->

<table class="table">

<tr>

<th width="25%">

VÉRIFICATION

</th>

<th width="45%">

OBSERVATIONS

</th>

<th width="30%">

SIGNATURES

</th>

</tr>

<tr>

<td class="center">

<img src="{{ $qrcode }}" width="85">

<div class="small">

Scanner ce QR Code pour vérifier
l'authenticité de ce reçu.

</div>

</td>

<td>

<div class="label">

Référence

</div>

<div class="value">

{{ $payment->receipt_number }}

</div>

<div class="space"></div>

<div class="label">

Lien de vérification

</div>

<div class="small">

{{ route('receipt.verify',$payment->receipt_number) }}

</div>

<div class="space"></div>

<div class="label">

Date d'impression

</div>

<div class="small">

{{ now()->format('d/m/Y H:i') }}

</div>

<div class="space"></div>

<div class="label">

Observation

</div>

<div class="small">

les frais de scolarité ne sont ni remboursable, ni transférables

Toute modification ou falsification rend ce document nul.

</div>

</td>

<td>

<div class="center">

Le Caissier

<br><br><br><br>

_____________________

</div>

<br>

<div class="center">

La Direction

<br><br><br><br>

_____________________

</div>

</td>

</tr>

</table>

<!-- ==================================================
FOOTER
================================================== -->

<div class="footer">

Document officiel NEW FASHION • Toute falsification est interdite •
Authentification par QR Code •
© {{ date('Y') }} NEW FASHION

</div>

</div>

</body>

</html>