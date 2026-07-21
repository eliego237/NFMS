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
    font-size:11px;
    color:#222;
}

.wrapper{
    position:relative;
    width:100%;
    border:1px solid #d8d8d8;
    padding:8px;
    overflow:hidden;
}

/*==========================================
FILIGRANE
==========================================*/

.watermark{

    position:absolute;

    top:70px;
    left:0;

    width:100%;

    text-align:center;

    font-size:22px;

    line-height:30px;

    font-weight:bold;

    color:#0d6efd;

    opacity:.09;

    z-index:0;

}

/*==========================================
CACHET PAYÉ
==========================================*/

.paid{

    position:absolute;

    top:215px;

    right:20px;

    font-size:60px;

    font-weight:bold;

    color:#dc3545;

    opacity:.18;

    transform:rotate(-20deg);

    z-index:1;

}

/*==========================================
HEADER
==========================================*/

.header{

    position:relative;

    z-index:2;

    border-bottom:2px solid #0d6efd;

    padding-bottom:8px;

    margin-bottom:10px;

}

.header table{

    width:100%;

    border-collapse:collapse;

}

.logo{

    width:14%;

    text-align:center;

}

.logo img{

    width:70px;

}

.school{

    width:61%;

    text-align:center;

}

.school-name{

    font-size:28px;

    color:#0d6efd;

    font-weight:bold;

    letter-spacing:.5px;

}

.school-sub{

    font-size:13px;

    font-weight:bold;

    margin-top:2px;

}

.school-info{

    font-size:11px;

    color:#555;

    line-height:1.45;

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

    font-size:11px;

    font-weight:bold;

}

.receipt-number{

    text-align:center;

    color:#dc3545;

    font-size:18px;

    font-weight:bold;

    padding-top:10px;

}

.receipt-date{

    text-align:center;

    color:#666;

    padding:6px;

    font-size:14px;

}

/*==========================================
TABLES
==========================================*/

.table{

    width:100%;

    border-collapse:collapse;

    margin-top:8px;

    position:relative;

    z-index:2;

}

.table th{

    background:#0d6efd;

    color:#fff;

    padding:6px;

    font-size:11px;

    border:1px solid #0d6efd;

}

.table td{

    border:1px solid #d8d8d8;

    padding:6px;

    vertical-align:top;

}

.label{

    color:#0d6efd;

    font-weight:bold;

    font-size:11px;

    margin-bottom:2px;

}

.value{

    color:#222;

    font-size:11px;

    margin-bottom:6px;

}

.amount{

    color:#198754;

    font-size:13px;

    font-weight:bold;

}

.red{

    color:#dc3545;

    font-weight:bold;

}

.center{

    text-align:center;

}

.space{

    height:6px;

}

.footer-dev{

    margin-top:8px;

    border-top:1px solid #ddd;

    padding-top:5px;

    text-align:center;

    font-size:8px;

    color:#777;

}

</style>

</head>

<body>

<div class="wrapper">

<div class="watermark">

ELIE GO &nbsp;&nbsp; ELIE GO &nbsp;&nbsp; ELIE GO<br>

NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION<br>

ELIE GO &nbsp;&nbsp; ELIE GO &nbsp;&nbsp; ELIE GO<br>

NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION<br>

ELIE GO &nbsp;&nbsp; ELIE GO &nbsp;&nbsp; ELIE GO<br>

NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION

ELIE GO &nbsp;&nbsp; ELIE GO &nbsp;&nbsp; ELIE GO

NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION

ELIE GO &nbsp;&nbsp; ELIE GO &nbsp;&nbsp; ELIE GO

NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION

ELIE GO &nbsp;&nbsp; ELIE GO &nbsp;&nbsp; ELIE GO

NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION

ELIE GO &nbsp;&nbsp; ELIE GO &nbsp;&nbsp; ELIE GO

NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION

ELIE GO &nbsp;&nbsp; ELIE GO &nbsp;&nbsp; ELIE GO

NEW FASHION &nbsp;&nbsp; NEW FASHION &nbsp;&nbsp; NEW FASHION

ELIE GO &nbsp;&nbsp; ELIE GO &nbsp;&nbsp; ELIE GO
</div>

<div class="paid">

PAYÉ

</div>

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

(+237) 676 26 68 65 • 655 36 19 46

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

<!-- ==========================================================
    INFORMATIONS PRINCIPALES
=========================================================== -->

<table class="table">

    <tr>

        <th width="35%">ÉTUDIANT</th>

        <th width="30%">FORMATION</th>

        <th width="35%">PAIEMENT</th>

    </tr>

    <tr>

        <!-- ================= ETUDIANT ================= -->

        <td>

            <div class="label">Matricule</div>
            <div class="value">
                {{ $payment->enrollment->student->matricule }}
            </div>

            <div class="label">Nom complet</div>
            <div class="value">
                {{ $payment->enrollment->student->first_name }}
                {{ $payment->enrollment->student->last_name }}
            </div>

            <div class="label">Téléphone</div>
            <div class="value">
                {{ $payment->enrollment->student->phone }}
            </div>

        </td>

        <!-- ================= FORMATION ================= -->

        <td>

            <div class="label">Code Formation</div>
            <div class="value">
                {{ $payment->enrollment->training->code }}
            </div>

            <div class="label">Catégorie</div>
            <div class="value">
                {{ $payment->enrollment->training->category }}
            </div>

            <div class="label">Durée</div>
            <div class="value">
                {{ $payment->enrollment->training->duration_months }} mois
            </div>

        </td>

        <!-- ================= PAIEMENT ================= -->

        <td>

            <div class="label">Montant payé</div>
            <div class="amount">
                {{ number_format($payment->amount,0,',',' ') }} FCFA
            </div>

            <div class="label">Mode de paiement</div>
            <div class="value">
                {{ $payment->paymentMethod->name }}
            </div>

            <div class="label">Caissier</div>
            <div class="value">
                {{ $payment->receiver->name }}
            </div>

        </td>

    </tr>

</table>


<!-- ==========================================================
    RÉCAPITULATIF FINANCIER
=========================================================== -->

<table class="table">

    <tr>

        <th colspan="7">

            RÉCAPITULATIF FINANCIER

        </th>

    </tr>

    <tr>

        <td width="18%" class="center">

            <div class="label">N° Inscription</div>

            <div class="value">
                {{ $payment->enrollment->enrollment_number }}
            </div>

        </td>

        <td width="14%" class="center">

            <div class="label">Année</div>

            <div class="value">
                {{ $payment->enrollment->academic_year }}
            </div>

        </td>

        <td width="14%" class="center">

            <div class="label">Total</div>

            <div class="value">
                {{ number_format($payment->enrollment->total_amount,0,',',' ') }} FCFA
            </div>

        </td>

        <td width="12%" class="center">

            <div class="label">Remise</div>

            <div class="value" style="color:#ff9800;font-weight:bold;">

                {{ $payment->enrollment->formatted_discount }}

            </div>

        </td>

        <td width="15%" class="center">

            <div class="label">Net à payer</div>

            <div class="value">

                {{ number_format($payment->enrollment->total_amount - $payment->enrollment->discount_amount,0,',',' ') }} FCFA

            </div>

        </td>

        <td width="13%" class="center">

            <div class="label">Déjà payé</div>

            <div class="value" style="color:#198754;font-weight:bold;">

                {{ $payment->enrollment->formatted_amount_paid }}

            </div>

        </td>

        <td width="14%" class="center">

            <div class="label red">Reste</div>

            <div class="red">

                {{ $payment->enrollment->formatted_balance }}

            </div>

        </td>

    </tr>

</table>

<!-- ==========================================================
    OBSERVATIONS & SIGNATURE
=========================================================== -->

<table class="table">

    <tr>

        <th width="70%">
            OBSERVATIONS
        </th>

        <th width="30%">
            SIGNATURE
        </th>

    </tr>

    <tr>

            <div class="value">

                Les frais de scolarité ne sont ni remboursables
                ni transférables.

            </div>

            <div class="space"></div>

            <div class="value">

                Toute modification ou falsification rend
                ce reçu invalide.

            </div>

        </td>

        <!-- ===========================
             SIGNATURE
        ============================ -->

        <td class="center">

            <div style="margin-top:5px;">

                Le Caissier

            </div>

            <br>

            ________________________

        </td>

    </tr>
    
</table>

</div>

</body>

</html>