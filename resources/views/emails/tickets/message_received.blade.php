<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle réponse - Ticket {{ $ticket->reference }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .title {
            font-size: 20px;
            color: #666;
            margin: 0;
        }
        .content {
            margin-bottom: 30px;
        }
        .ticket-info {
            background: #f8f9fa;
            border-left: 4px solid #2563eb;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .ticket-ref {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .ticket-title {
            font-size: 16px;
            margin-bottom: 15px;
        }
        .message-box {
            background: #f3f4f6;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .message-header {
            font-weight: 600;
            color: #10b981;
            margin-bottom: 10px;
        }
        .message-content {
            white-space: pre-wrap;
            line-height: 1.6;
        }
        .cta-section {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 0 10px;
        }
        .btn:hover {
            background: #1d4ed8;
        }
        .btn-secondary {
            background: #6b7280;
        }
        .btn-secondary:hover {
            background: #4b5563;
        }
        .footer {
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">CJES Support</div>
            <h1 class="title">Nouvelle réponse sur votre ticket</h1>
        </div>

        <div class="content">
            <p>Bonjour {{ $ticket->user->prenom }},</p>
            
            <p>Une nouvelle réponse a été ajoutée à votre ticket de support.</p>

            <div class="ticket-info">
                <div class="ticket-ref">{{ $ticket->reference }}</div>
                <div class="ticket-title">{{ $ticket->titre }}</div>
                
                <div style="margin-top: 15px;">
                    <strong>Statut actuel:</strong> {{ $ticket->statut->nom }}<br>
                    <strong>Assigné à:</strong> {{ $ticket->technicien ? $ticket->technicien->nom . ' ' . $ticket->technicien->prenom : 'Non assigné' }}
                </div>
            </div>

            <div class="message-box">
                <div class="message-header">
                    Réponse de {{ $messageAuthor->nom }} {{ $messageAuthor->prenom }}
                    <small style="color: #666; font-weight: normal;">({{ $message->created_at->format('d/m/Y H:i') }})</small>
                </div>
                <div class="message-content">{{ $message->message }}</div>
            </div>

            <div class="cta-section">
                <a href="{{ url('/tickets/' . $ticket->id) }}" class="btn">Voir le ticket</a>
                <a href="{{ url('/my-tickets') }}" class="btn btn-secondary">Mes tickets</a>
            </div>
        </div>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement par le système de support CJES.</p>
            <p>Pour répondre directement à ce message, veuillez utiliser l'interface du ticket.</p>
        </div>
    </div>
</body>
</html>
