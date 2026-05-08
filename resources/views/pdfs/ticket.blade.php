<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
            size: a4 portrait;
        }

        * {
            font-family: 'Helvetica', sans-serif !important;
        }

        body {
            margin: 0;
            padding: 0;
            color: #111;
            line-height: 1.1;
        }

        .page {
            page-break-after: always;
        }

        /* Header avec overlay dégradé */
        .header {
            width: 100%;
            height: 450px;
            background-color: #000;
            position: relative;
            overflow: hidden;
        }

        .header-img {
            width: 100%;
            height: auto;
            min-height: 100%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translateY(-50%) translateX(-50%);
            /* Centre l'image */
            opacity: 0.75;
        }

        .header-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.8) 100%);
        }

        .header-text {
            position: absolute;
            bottom: 40px;
            left: 50px;
            right: 50px;
        }

        .event-title {
            color: #fff;
            font-size: 65px;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: -3px;
            line-height: 0.9;
        }

        .ticket-category {
            background: #51A687;
            /* Vert émeraude moderne */
            color: #fff;
            display: inline-block;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
        }

        /* Main Layout */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .col-left {
            width: 60%;
            padding: 50px 0 50px 50px;
            vertical-align: top;
        }

        .col-right {
            width: 40%;
            padding: 50px 40px;
            border-left: 2px dashed #e0e0e0;
            text-align: center;
            vertical-align: top;
        }

        /* Blocks */
        .section-row {
            margin-bottom: 35px;
            padding-bottom: 15px;
        }

        .label {
            font-size: 9px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
            display: block;
        }

        .value {
            font-size: 20px;
            font-weight: bold;
            color: #000;
            text-transform: capitalize;
        }

        .sub-value {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
            font-weight: normal;
        }

        /* Stub elements */
        .qr-box {
            background: #fff;
            padding: 10px;
            display: inline-block;
            border: 1px solid #eee;
            margin-bottom: 15px;
        }

        .id-text {
            font-family: 'Courier', monospace;
            font-size: 10px;
            color: #aaa;
            word-break: break-all;
        }

        .order-box {
            margin-top: 60px;
            text-align: left;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
        }

        .footer {
            position: absolute;
            bottom: 40px;
            left: 50px;
            right: 40%;
            font-size: 9px;
            color: #ccc;
            line-height: 1.4;
        }
    </style>
</head>

<body>

    @foreach ($tickets as $ticket)
        <div class="header">
            @if ($backgroundImage)
                <img src="{{ $backgroundImage }}" class="header-img">
            @endif
            <div class="header-overlay"></div>
            <div class="header-text">
                <div class="ticket-category">{{ $ticket->reservable->name }}</div>
                <h1 class="event-title">{{ $event->title }}</h1>
            </div>
        </div>

        <table class="main-table">
            <tr>
                <td class="col-left">
                    <div class="section-row">
                        <span class="label">Acheteur</span>
                        <span class="value">{{ $ticket->checkout->customer_name }}</span>
                    </div>

                    <div class="section-row">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 55%;">
                                    <span class="label">Date de l'événement</span>
                                    <span class="value">{{ $event->date->translatedFormat('d F Y') }}</span>
                                </td>
                                <td>
                                    <span class="label">Ouverture</span>
                                    <span class="value">
                                        {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                                        @if ($event->end_time)
                                            — {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="section-row">
                        <span class="label">Lieu</span>
                        <div class="value">{{ $event->address }}</div>
                    </div>

                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 55%;">
                                <span class="label">Montant payé</span>
                                <span class="value">{{ number_format($ticket->price_paid / 100, 2, ',', ' ') }}
                                    €</span>
                            </td>
                            <td>
                                @if ($event->minimum_age)
                                    <span class="label">Contrôle d'âge</span>
                                    <span class="value">{{ $event->minimum_age }} ans +</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>

                <td class="col-right">
                    <div class="qr-box">
                        <img src="data:image/png;base64, {!! base64_encode(
                            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(180)->margin(0)->generate($ticket->public_id),
                        ) !!}" style="width: 160px; height: 160px;">
                    </div>
                    <div class="id-text">{{ $ticket->public_id }}</div>

                    <div class="order-box">
                        <span class="label">Référence commande</span>
                        <div class="id-text">{{ $ticket->checkout->uuid }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer">
            <div style="color: #000; font-weight: bold; margin-bottom: 5px; font-size: 11px;">Powered by Symbiosa</div>
            Ce document est votre titre d'accès officiel. Ne le partagez avec personne.
            L'organisateur se réserve le droit d'entrée.
            Généré le {{ date('d/m/Y à H:i') }}.
        </div>
    @endforeach

</body>

</html>
