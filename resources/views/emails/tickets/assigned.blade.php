<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau ticket assigné - {{ $ticket->reference }}</title>
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
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: 600;
            color: #666;
        }
        .info-value {
            color: #333;
        }
        .priority-urgent {
            background: #fee2e2;
            border-left-color: #dc2626;
        }
        .priority-high {
            background: #fed7aa;
            border-left-color: #ea580c;
        }
        .priority-normal {
            background: #fef3c7;
            border-left-color: #d97706;
        }
        .priority-low {
            background: #dbeafe;
            border-left-color: #2563eb;
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
            <h1 class="title">Nouveau ticket assigné</h1>
        </div>

        <div class="content">
            <p>Bonjour,</p>
            
            <p>Un nouveau ticket vous a été assigné et requiert votre attention.</p>

            <div class="ticket-info {{ $ticket->priorite->niveau >= 3 ? 'priority-urgent' : ($ticket->priorite->niveau >= 2 ? 'priority-high' : ($ticket->priorite->niveau >= 1 ? 'priority-normal' : 'priority-low')) }}">
                <div class="ticket-ref">{{ $ticket->reference }}</div>
                <div class="ticket-title">{{ $ticket->titre }}</div>
                
                <div class="info-row">
                    <span class="info-label">Demandeur:</span>
                    <span class="info-value">{{ $ticket->user->nom }} {{ $ticket->user->prenom }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $ticket->user->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Plateforme:</span>
                    <span class="info-value">{{ $ticket->plateforme->nom }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Module:</span>
                    <span class="info-value">{{ $ticket->module->nom }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Priorité:</span>
                    <span class="info-value">{{ $ticket->priorite->nom }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date d'ouverture:</span>
                    <span class="info-value">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <div style="background: #f3f4f6; padding: 15px; border-radius: 4px; margin: 20px 0;">
                <strong>Description:</strong><br>
                {{ $ticket->description }}
            </div>

            <div class="cta-section">
                <a href="{{ url('/tickets/' . $ticket->id) }}" class="btn">Voir le ticket</a>
                <a href="{{ url('/dashboard') }}" class="btn btn-secondary">Tableau de bord</a>
            </div>
        </div>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement par le système de support CJES.</p>
            <p>Ticket assigné par: {{ $assignedBy }}</p>
        </div>
    </div>
</body>
</html>
