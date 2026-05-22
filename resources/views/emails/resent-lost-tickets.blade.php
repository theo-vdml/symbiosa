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
            border-top: 5px solid #51A687;
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
        .info-box {
            background-color: #f3fcf9;
            border-left: 4px solid #51A687;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
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
            <div class="title">Remplacement de billets</div>
        </div>
        <div class="content">
            <p>Bonjour {{ $checkout->customer_name }},</p>
            
            <p>Suite à votre demande sur notre site, nous vous renvoyons vos billets pour l'événement <strong>{{ $event->title }}</strong>.</p>

            <div class="info-box">
                Ceci est un email de support généré suite à une demande de récupération de billets perdus.
            </div>

            <p>Vous trouverez vos billets en pièce jointe de ce mail.</p>
            
            <p><strong>Référence de commande :</strong> {{ $checkout->uuid }}</p>
            
            <p>Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer ce message.</p>

            <p>L'équipe Symbiosa</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Symbiosa. Cet email a été envoyé via notre service de support.
        </div>
    </div>
</body>
</html>
