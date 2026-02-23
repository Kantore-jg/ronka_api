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
        .credentials { background: white; border: 2px solid #D4AF37; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .credentials h3 { color: #6b4423; margin-top: 0; }
        .cred-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .cred-row:last-child { border-bottom: none; }
        .cred-label { font-weight: bold; color: #6b4423; }
        .cred-value { font-family: monospace; font-size: 1rem; color: #333; }
        .btn { display: inline-block; background: #D4AF37; color: #1a1a1a; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: bold; margin: 15px 0; }
        .footer { background: #f0f0f0; padding: 15px; border-radius: 0 0 8px 8px; text-align: center; font-size: 0.85rem; color: #666; border: 1px solid #ddd; border-top: none; }
        .warning { font-size: 0.85rem; color: #888; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RONKA Event Multi Service</h1>
        <p>Bienvenue dans notre équipe !</p>
    </div>
    <div class="content">
        <p>Bonjour <strong>{{ $member->name }}</strong>,</p>
        <p>
            Nous avons le plaisir de vous informer que vous êtes désormais 
            <strong>membre officiel</strong> de <strong>RONKA Event Multi Service</strong>.
        </p>
        <p>Voici vos identifiants de connexion à la plateforme :</p>

        <div class="credentials">
            <h3>Vos identifiants</h3>
            <div class="cred-row">
                <span class="cred-label">Code membre :</span>
                <span class="cred-value">{{ $member->username }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-label">Mot de passe :</span>
                <span class="cred-value">{{ $plainPassword }}</span>
            </div>
        </div>

        <p>Cliquez sur le lien ci-dessous pour accéder à la plateforme :</p>
        <p style="text-align: center;">
            <a href="{{ $loginUrl }}" class="btn">Accéder à la plateforme</a>
        </p>

        <p class="warning">
            Pour des raisons de sécurité, nous vous recommandons de ne pas partager vos identifiants.
        </p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} RONKA Event Multi Service. Tous droits réservés.</p>
    </div>
</body>
</html>
