<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Quotidien du Support</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px 10px 0 0; text-align: center;">
        <h1 style="margin: 0; font-size: 28px;">Résumé du Support (24h)</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">{{ date('d/m/Y') }}</p>
    </div>
    
    <div style="background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;">
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
            <h2 style="margin: 0 0 20px 0; color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px;">Statistiques de la veille</h2>
            
            <ul style="list-style: none; padding: 0;">
                <li style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 600; color: #555;">Nouveaux tickets :</span>
                    <span style="background: #2563eb; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold;">{{ $stats['nouveaux'] }}</span>
                </li>
                <li style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 600; color: #555;">Tickets résolus :</span>
                    <span style="background: #10b981; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold;">{{ $stats['resolus'] }}</span>
                </li>
                <li style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 600; color: #555;">En attente totale :</span>
                    <span style="background: #f59e0b; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold;">{{ $stats['ouverts_total'] }}</span>
                </li>
            </ul>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ route('supervisor.stats') }}" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                Consulter le tableau de bord complet
            </a>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center; color: #6b7280; font-size: 12px;">
            <p>Cet email est généré automatiquement par le système de support.</p>
            <p>Pour ne plus recevoir ces emails, veuillez contacter l'administrateur système.</p>
        </div>
    </div>
</body>
</html>
