<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">

<title>Vérification du reçu</title>

<style>

body{

    font-family:Arial,sans-serif;

    background:#f5f5f5;

    margin:0;

    padding:40px;

}

.container{

    max-width:750px;

    margin:auto;

    background:#fff;

    border-radius:10px;

    padding:30px;

    box-shadow:0 5px 20px rgba(0,0,0,.15);

}

h1{

    color:#198754;

    text-align:center;

    margin-bottom:30px;

}

table{

    width:100%;

    border-collapse:collapse;

}

td{

    padding:10px;

    border-bottom:1px solid #ddd;

}

.label{

    font-weight:bold;

    width:35%;

}

.valid{

    margin-top:30px;

    padding:15px;

    text-align:center;

    background:#198754;

    color:white;

    font-size:18px;

    border-radius:8px;

}

</style>

</head>

<body>

<div class="container">

<h1>

REÇU AUTHENTIQUE

</h1>

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

Étudiant

</td>

<td>

{{ $payment->enrollment->student->first_name }}
{{ $payment->enrollment->student->last_name }}

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

Montant payé

</td>

<td>

{{ number_format($payment->amount,0,',',' ') }}

FCFA

</td>

</tr>

<tr>

<td class="label">

Date

</td>

<td>

{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}

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

</table>

<div class="valid">

✓ Ce reçu est authentique et a été généré par NEW FASHION.

</div>

</div>

</body>

</html>