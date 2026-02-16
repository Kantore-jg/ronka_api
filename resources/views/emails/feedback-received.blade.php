<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #6b4423; color: white; padding: 15px; border-radius: 8px 8px 0 0; }
        .content { background: #faf8f5; padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 8px 8px; }
        .label { font-weight: bold; color: #6b4423; }
        .message { white-space: pre-wrap; background: white; padding: 15px; border-radius: 6px; margin-top: 10px; }
        .meta { font-size: 0.9em; color: #666; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0;">RONKA Event Multi Service</h2>
        <p style="margin:5px 0 0 0; opacity:0.9;">Nouveau {{ $feedback->type === 'suggestion' ? 'suggestion' : 'feedback' }} reçu</p>
    </div>
    <div class="content">
        @if($feedback->name)
        <p><span class="label">Nom :</span> {{ $feedback->name }}</p>
        @endif
        @if($feedback->contact)
        <p><span class="label">Contact :</span> {{ $feedback->contact }}</p>
        @endif
        @if($feedback->sender_email)
        <p><span class="label">Email expéditeur :</span> {{ $feedback->sender_email }}</p>
        @endif
        <p><span class="label">Message :</span></p>
        <div class="message">{{ $feedback->message }}</div>
        <p class="meta">Reçu le {{ $feedback->created_at->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>
