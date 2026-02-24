<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #6b4423; color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
        .header h1 { margin: 0; font-size: 1.5rem; }
        .header p { margin: 5px 0 0; opacity: 0.9; font-size: 0.9rem; }
        .content { background: #faf8f5; padding: 25px; border: 1px solid #ddd; border-top: none; }
        .event-card { background: white; border: 2px solid #D4AF37; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .event-card h3 { color: #6b4423; margin-top: 0; }
        .event-row { padding: 8px 0; border-bottom: 1px solid #eee; }
        .event-row:last-child { border-bottom: none; }
        .event-label { font-weight: bold; color: #6b4423; }
        .buttons { text-align: center; margin: 25px 0; }
        .btn { display: inline-block; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: bold; margin: 0 8px; }
        .btn-confirm { background: #28a745; color: white; }
        .btn-decline { background: #dc3545; color: white; }
        .footer { background: #f0f0f0; padding: 15px; border-radius: 0 0 8px 8px; text-align: center; font-size: 0.85rem; color: #666; border: 1px solid #ddd; border-top: none; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RONKA Event Multi Service</h1>
        <p>Notification d'assignation</p>
    </div>
    <div class="content">
        <p>Bonjour <strong>{{ $member->name }}</strong>,</p>
        <p>
            Vous avez été assigné(e) à un événement. Veuillez prendre connaissance des détails
            ci-dessous et confirmer votre présence.
        </p>

        <div class="event-card">
            <h3>{{ $event->title }}</h3>
            <div class="event-row">
                <span class="event-label">Date :</span>
                {{ $event->date->format('d/m/Y') }}
            </div>
            @if($event->lieu)
            <div class="event-row">
                <span class="event-label">Lieu :</span>
                {{ $event->lieu }}
            </div>
            @endif
            @if($event->description)
            <div class="event-row">
                <span class="event-label">Description :</span>
                {{ $event->description }}
            </div>
            @endif
        </div>

        <p style="text-align: center;">Merci de confirmer votre présence :</p>

        <div class="buttons">
            <a href="{{ $confirmUrl }}" class="btn btn-confirm">Je confirme ma présence</a>
            <a href="{{ $declineUrl }}" class="btn btn-decline">Je ne pourrai pas</a>
        </div>

        <p style="font-size: 0.85rem; color: #888; text-align: center;">
            Vous pouvez aussi confirmer directement depuis votre espace membre sur la plateforme.
        </p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} RONKA Event Multi Service. Tous droits réservés.</p>
    </div>
</body>
</html>
