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
            font-family: 'Helvetica', Arial, sans-serif !important;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            color: #111;
            line-height: 1.1;
            background: #fff;
            width: 100%;
        }

        .page-break {
            page-break-after: always;
        }

        /* --- TICKET DESIGN --- */
        .ticket-wrapper {
            width: 100%;
            height: 297mm;
            position: relative;
            overflow: hidden;
        }

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
            opacity: 0.8;
        }

        .header-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 30%, rgba(0, 0, 0, 0.9) 100%);
        }

        .header-text {
            position: absolute;
            bottom: 45px;
            left: 50px;
            right: 50px;
        }

        .category {
            color: #51A687;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        .sub-category {
            color: #999;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 5px;
        }

        .title {
            color: #fff;
            font-size: 55px;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: -2px;
            line-height: 0.9;
        }

        .main-content {
            width: 100%;
            border-collapse: collapse;
        }

        .col-left {
            width: 60%;
            padding: 50px 0 0 50px;
            vertical-align: top;
        }

        .col-right {
            width: 40%;
            padding: 50px 40px;
            border-left: 1px solid #eee;
            text-align: center;
            vertical-align: top;
        }

        .data-group {
            margin-bottom: 25px;
        }

        .label {
            font-size: 9px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 5px;
            display: block;
        }

        .value {
            font-size: 18px;
            font-weight: bold;
            color: #000;
        }

        .qr-box {
            padding: 10px;
            border: 1px solid #eee;
            display: inline-block;
            margin-top: 10px;
            margin-bottom: 10px;
            background: #fff;
        }

        .id-mono {
            font-family: monospace !important;
            font-size: 9px;
            color: #bbb;
            word-break: break-all;
        }

        .footer {
            position: absolute;
            bottom: 40px;
            left: 50px;
            font-size: 9px;
            color: #ccc;
        }

        /* --- CONTENT PAGE DESIGN (ANTI-OVERFLOW) --- */
        .info-page {
            padding: 15mm;
            /* On laisse de l'air autour du cadre */
            width: 210mm;
            /* Largeur fixe A4 */
        }

        .info-frame {
            border: 1px solid #ddd;
            padding: 20px;
            /* Ne surtout pas mettre width: 100% ici avec DomPDF si on a un border */
            display: block;
            width: calc(100% - 40px - 30mm);
            /* Largeur calculée (210mm - 30mm de padding info-page) */
        }

        .rich-text {
            font-size: 9px;
            line-height: 1.2;
            color: #333;
            width: 100%;
        }

        .rich-text h1 {
            font-size: 14px;
            margin: 0 0 6px 0;
            text-transform: uppercase;
            border-bottom: 1px solid #eee;
            padding-bottom: 3px;
        }

        .rich-text h2 {
            font-size: 11px;
            margin: 10px 0 4px 0;
            text-transform: uppercase;
        }

        .rich-text h3 {
            font-size: 10px;
            margin: 6px 0 2px 0;
            font-weight: bold;
        }

        .rich-text p {
            margin: 0 0 5px 0;
            text-align: justify;
        }

        .rich-text ul,
        .rich-text ol {
            margin: 0 0 5px 0;
            padding-left: 20px;
        }

        .rich-text li {
            margin-bottom: 2px;
        }

        .rich-text img {
            max-width: 100%;
            height: auto;
            margin: 5px 0;
            display: block;
        }

        .rich-text hr {
            border: none;
            border-top: 1px solid #eee;
            margin: 10px 0;
        }
    </style>
</head>

<body>

    @foreach ($tickets as $ticket)
        <div class="ticket-wrapper {{ !$loop->last || $customContent ? 'page-break' : '' }}">
            <div class="header">
                @if ($backgroundImage)
                    <img src="{{ $backgroundImage }}" class="header-img">
                @endif
                <div class="header-overlay"></div>
                <div class="header-text">
                    <h1 class="title">{{ $event->title }}</h1>
                </div>
            </div>

            <table class="main-content">
                <tr>
                    <td class="col-left">
                        <div class="data-group">
                            <span class="label">Acheteur</span>
                            <span class="value">{{ $ticket->checkout->customer_name }}</span>
                        </div>

                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px;">
                            <tr>
                                <td style="width: 50%; vertical-align: top;">
                                    <span class="label">Date</span>
                                    <span class="value">{{ $event->date->translatedFormat('d F Y') }}</span>
                                </td>
                                @if ($event->start_time)
                                    <td style="vertical-align: top;">
                                        <span class="label">Ouverture</span>
                                        <span class="value">
                                            {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                                            @if ($event->end_time)
                                                — {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}
                                            @endif
                                        </span>
                                    </td>
                                @endif
                            </tr>
                        </table>

                        @if ($event->address)
                            <div class="data-group">
                                <span class="label">Lieu</span>
                                <div class="value" style="font-size: 15px; font-weight: normal; line-height: 1.2;">
                                    {{ $event->address }}</div>
                            </div>
                        @endif

                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 50%; vertical-align: top;">
                                    <span class="label">Montant</span>
                                    <span class="value">{{ number_format($ticket->price_paid / 100, 2, ',', ' ') }}
                                        €</span>
                                </td>
                                @if ($event->minimum_age)
                                    <td style="vertical-align: top;">
                                        <span class="label">Restriction</span>
                                        <span class="value">{{ $event->minimum_age }} ans +</span>
                                    </td>
                                @endif
                            </tr>
                        </table>
                    </td>

                    <td class="col-right">
                        <div class="category">{{ $ticket->reservable->name }}</div>
                        @if ($ticket->ticketPrice)
                            <div class="sub-category">{{ $ticket->ticketPrice->name }}</div>
                        @endif
                        <div class="qr-box">
                            <img src="data:image/png;base64, {!! base64_encode(
                                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(180)->margin(0)->generate($ticket->public_id),
                            ) !!}"
                                style="width: 150px; height: 150px;">
                        </div>
                        <div class="id-mono" style="margin-bottom: 25px;">{{ $ticket->public_id }}</div>

                        <div style="text-align: left; background: #fafafa; padding: 12px; border: 1px solid #eee;">
                            <span class="label">Référence</span>
                            <div class="id-mono" style="color: #666;">{{ $ticket->checkout->uuid }}</div>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="footer">
                <strong style="color: #111;">Symbiosa</strong> &bull; Support: symbiosa.be &bull; Généré le
                {{ date('d/m/Y à H:i') }}
            </div>
        </div>
    @endforeach

    @if ($customContent)
        <div class="info-page">
            <div class="info-frame">
                <div class="rich-text">
                    {!! $customContent !!}
                </div>
            </div>
        </div>
    @endif

</body>

</html>
