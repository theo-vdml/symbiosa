<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.6;
            background-color: #f9f9f9;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            color: #51A687;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .content {
            margin-bottom: 30px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">Merci pour votre commande !</div>
        </div>
        <div class="content">
            <p>Bonjour {{ $checkout->customer_name }},</p>
            <p>Votre paiement a été validé avec succès pour l'événement <strong>{{ $event->title }}</strong>.</p>
            <p>Vous trouverez en pièces jointes vos billets et options réservés.</p>
            <p><strong>Référence de commande :</strong> {{ $checkout->uuid }}</p>
            <p>À très vite !</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Symbiosa. Tous droits réservés.
        </div>
    </div>
</body>
</html>
