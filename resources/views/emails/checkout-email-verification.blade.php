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
        .code {
            display: block;
            width: fit-content;
            margin: 30px auto;
            padding: 15px 30px;
            background-color: #f3fcf9;
            border: 2px dashed #51A687;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #06402B;
        }
        .button {
            display: block;
            width: fit-content;
            margin: 30px auto;
            padding: 15px 30px;
            background-color: #51A687;
            color: #fff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #999;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">Vérification de votre email</div>
        </div>
        <div class="content">
            <p>Bonjour,</p>
            <p>Pour finaliser votre commande chez Symbiosa, veuillez vérifier votre adresse email.</p>
            
            <p>Utilisez le code suivant sur la page de paiement :</p>
            <div class="code">{{ $code }}</div>

            <p>Ou cliquez simplement sur le bouton ci-dessous pour vérifier automatiquement votre email :</p>
            <a href="{{ $verificationUrl }}" class="button">Vérifier mon email</a>

            <p>Ce code expirera en même temps que votre session de réservation.</p>
            <p>Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Symbiosa.
        </div>
    </div>
</body>
</html>
