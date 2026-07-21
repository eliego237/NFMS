<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Vérification du reçu</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    font-family:Arial,Helvetica,sans-serif;

    background:#f5f7fa;

    color:#333;

    padding:40px;

}

.container{

    max-width:800px;

    margin:auto;

    background:#fff;

    border-radius:12px;

    box-shadow:0 8px 25px rgba(0,0,0,.12);

    overflow:hidden;

}

.header{

    background:#0d6efd;

    color:white;

    padding:25px;

    text-align:center;

}

.logo{

    width:80px;

    margin-bottom:10px;

}

.school{

    font-size:30px;

    font-weight:bold;

}

.subtitle{

    font-size:14px;

    opacity:.95;

    margin-top:5px;

}

.content{

    padding:30px;

}

.valid{

    background:#198754;

    color:white;

    text-align:center;

    padding:15px;

    border-radius:8px;

    font-size:22px;

    font-weight:bold;

    margin-bottom:25px;

}

.invalid{

    background:#dc3545;

    color:white;

    text-align:center;

    padding:15px;

    border-radius:8px;

    font-size:22px;

    font-weight:bold;

    margin-bottom:25px;

}

table{

    width:100%;

    border-collapse:collapse;

}

td{

    padding:12px;

    border-bottom:1px solid #ececec;

}

.label{

    width:35%;

    font-weight:bold;

    color:#0d6efd;

}

.footer{

    margin-top:35px;

    border-top:1px solid #ddd;

    padding-top:20px;

    text-align:center;

    color:#666;

    font-size:13px;

}

.badge{

    display:inline-block;

    margin-top:10px;

    background:#0d6efd;

    color:white;

    padding:8px 18px;

    border-radius:30px;

    font-weight:bold;

}

.notice{

    margin-top:20px;

    background:#eef7ff;

    border-left:5px solid #0d6efd;

    padding:15px;

    color:#555;

    line-height:1.6;

}

</style>

</head>

<body>

<div class="container">

<div class="header">

@if(file_exists(public_path('images/logo-small.png')))

<img
src="{{ asset('images/logo-small.png') }}"
class="logo">

@endif

<div class="school">

NEW FASHION

</div>

<div class="subtitle">

Institut de Beauté & Centre de Formation Professionnelle

</div>

</div>

<div class="content">

@if($payment)

<div class="valid">

✓ REÇU AUTHENTIQUE

</div>

<table>

<tr>

<td class="label">

Référence

</td>

<td>

{{ $payment->receipt_number }}

</td>

</tr>

<tr>

<td class="label">

N° Inscription

</td>

<td>

{{ $payment->enrollment->enrollment_number }}

</td>

</tr>

<tr>

<td class="label">

Étudiant

</td>

<td>

{{ $payment->enrollment->student->first_name }}
{{ $payment->enrollment->student->last_name }}

</td>

</tr>

<tr>

<td class="label">

Matricule

</td>

<td>

{{ $payment->enrollment->student->matricule }}

</td>

</tr>

<tr>

<td class="label">

Formation

</td>

<td>

{{ $payment->enrollment->training->title }}

</td>

</tr>

<tr>

<td class="label">

Année académique

</td>

<td>

{{ $payment->enrollment->academic_year }}

</td>

</tr>

<tr>

<td class="label">

Montant payé

</td>

<td>

{{ number_format($payment->amount,0,',',' ') }} FCFA

</td>

</tr>

<tr>

<td class="label">

Date du paiement

</td>

<td>

{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}

</td>

</tr>

<tr>

<td class="label">

Mode de paiement

</td>

<td>

{{ $payment->paymentMethod->name }}

</td>

</tr>

<tr>

<td class="label">

Caissier

</td>

<td>

{{ $payment->receiver->name }}

</td>

</tr>

<tr>

<td class="label">

Statut de l'inscription

</td>

<td>

{{ $payment->enrollment->formatted_status }}

</td>

</tr>

</table>

<div class="notice">

Ce reçu est enregistré dans la base de données officielle de <strong>NEW FASHION</strong>.

Toutes les informations affichées correspondent exactement au paiement effectué.

Toute modification du document original le rend automatiquement invalide.

</div>

@else

<div class="invalid">

✖ REÇU INVALIDE

</div>

<div class="notice">

Le numéro de reçu demandé n'existe pas dans notre base de données.

Il peut s'agir d'un faux document ou d'une erreur de saisie.

Si vous pensez qu'il s'agit d'une erreur, veuillez contacter l'administration de <strong>NEW FASHION</strong>.

</div>

@endif

<div class="footer">

<strong>NFMS v1.0</strong><br>

(New Fashion Management System Elie go)

<br><br>

© {{ date('Y') }} NEW FASHION

<div class="badge">

Document vérifié électroniquement

</div>

</div>

</div>

</div>

</body>

</html>