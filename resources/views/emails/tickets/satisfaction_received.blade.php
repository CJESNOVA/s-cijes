<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satisfaction client - {{ $ticket->reference }}</title>
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
        .satisfaction-info {
            background: #d1fae5;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: center;
        }
        .stars {
            font-size: 32px;
            color: #fbbf24;
            margin: 15px 0;
        }
        .score {
            font-size: 24px;
            font-weight: bold;
            color: #10b981;
            margin: 10px 0;
        }
        .comment-box {
            background: #f3f4f6;
            border-left: 4px solid #6b7280;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .comment-header {
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 10px;
        }
        .comment-content {
            font-style: italic;
            color: #4b5563;
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
        .rating-excellent { color: #10b981; }
        .rating-good { color: #3b82f6; }
        .rating-average { color: #f59e0b; }
        .rating-poor { color: #ef4444; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">CJES Support</div>
            <h1 class="title">Satisfaction client enregistrée</h1>
        </div>

        <div class="content">
            <p>Bonjour,</p>
            
            <p>Le client a enregistré sa satisfaction pour la résolution du ticket suivant:</p>

            <div class="ticket-info">
                <div class="ticket-ref">{{ $ticket->reference }}</div>
                <div class="ticket-title">{{ $ticket->titre }}</div>
                
                <div style="margin-top: 15px;">
                    <strong>Client:</strong> {{ $ticket->user->nom }} {{ $ticket->user->prenom }}<br>
                    <strong>Email:</strong> {{ $ticket->user->email }}<br>
                    <strong>Résolu par:</strong> {{ $ticket->technicien ? $ticket->technicien->nom . ' ' . $ticket->technicien->prenom : 'Non spécifié' }}<br>
                    <strong>Date résolution:</strong> {{ $ticket->date_resolution->format('d/m/Y H:i') }}
                </div>
            </div>

            <div class="satisfaction-info">
                <div style="font-weight: 600; margin-bottom: 10px;">Note de satisfaction</div>
                <div class="stars">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $satisfaction->note)
                            ⭐
                        @else
                            ☆
                        @endif
                    @endfor
                </div>
                <div class="score {{ $satisfaction->note >= 4 ? 'rating-excellent' : ($satisfaction->note >= 3 ? 'rating-good' : ($satisfaction->note >= 2 ? 'rating-average' : 'rating-poor')) }}">
                    {{ $satisfaction->note }}/5
                </div>
            </div>

            @if($satisfaction->commentaire)
            <div class="comment-box">
                <div class="comment-header">Commentaire du client:</div>
                <div class="comment-content">{{ $satisfaction->commentaire }}</div>
            </div>
            @endif

            <div class="cta-section">
                <a href="{{ url('/tickets/' . $ticket->id) }}" class="btn">Voir le ticket</a>
                <a href="{{ url('/dashboard') }}" class="btn btn-secondary">Tableau de bord</a>
            </div>
        </div>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement par le système de support CJES.</p>
            <p>Date d'enregistrement: {{ $satisfaction->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
