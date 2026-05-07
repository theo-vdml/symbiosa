<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $ticket->is_attendee ? 'Billet' : 'Option' }} - {{ $ticket->name }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .ticket-container {
            width: 100%;
            height: 100%;
            background-color: #fff;
            padding: 40px;
            box-sizing: border-box;
        }
        .header {
            border-bottom: 2px solid #51A687;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .event-title {
            font-size: 28px;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .ticket-type {
            font-size: 18px;
            color: #51A687;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .details {
            display: table;
            width: 100%;
            margin-bottom: 40px;
        }
        .details-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .label {
            font-size: 10px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .value {
            font-size: 16px;
            color: #333;
            margin-bottom: 15px;
        }
        .qr-section {
            text-align: center;
            margin-top: 50px;
            padding: 30px;
            border: 1px dashed #ccc;
            border-radius: 10px;
        }
        .qr-code {
            margin-bottom: 15px;
        }
        .token {
            font-family: monospace;
            font-size: 12px;
            color: #666;
        }
        .footer {
            position: absolute;
            bottom: 40px;
            left: 40px;
            right: 40px;
            font-size: 10px;
            color: #999;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="header">
            <div class="event-title">{{ $event->title }}</div>
            <div class="ticket-type">{{ $ticket->name }}</div>
        </div>

        <div class="details">
            <div class="details-col">
                <div class="label">Date de l'événement</div>
                <div class="value">{{ $event->date->format('d/m/Y') }}</div>

                <div class="label">Lieu</div>
                <div class="value">{{ $event->city }}, {{ $event->country }}</div>
            </div>
            <div class="details-col">
                <div class="label">Client</div>
                <div class="value">{{ $checkout->customer_name ?? 'Client' }}</div>

                <div class="label">Référence Commande</div>
                <div class="value">{{ $checkout->uuid }}</div>
            </div>
        </div>

        <div class="qr-section">
            <div class="qr-code">
                <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(200)->margin(0)->generate($ticket->qr_code_token)) !!} ">
            </div>
            <div class="label">Scannez ce code à l'entrée</div>
            <div class="token">{{ $ticket->qr_code_token }}</div>
        </div>

        <div class="footer">
            Billet généré par Symbiosa. Ce billet est unique et ne peut être utilisé qu'une seule fois.
            Toute reproduction est interdite.
        </div>
    </div>
</body>
</html>
