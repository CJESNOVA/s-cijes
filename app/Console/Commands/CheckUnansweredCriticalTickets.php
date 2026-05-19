<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use App\Notifications\CriticalTicketReminder;
use Illuminate\Support\Facades\Notification;

class CheckUnansweredCriticalTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:check-unanswered';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les tickets critiques sans réponse après 2h';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // On cherche les tickets :
        // 1. Priorité critique (ex: ID 4)
        // 2. Statut "Ouvert" (pas encore affecté ou traité)
        // 3. Créés il y a plus de 2 heures
        // 4. Sans aucun message associé (ou sans message d'un technicien)
        
        $tickets = Ticket::where('priorite_id', 4)
            ->where('statut_id', 1) 
            ->where('created_at', '<=', now()->subHours(2))
            ->whereDoesntHave('messages') // Aucun message du tout
            ->get();

        foreach ($tickets as $ticket) {
            // On notifie le canal Slack via un objet "AnonymousNotifiable"
            Notification::route('slack', config('services.slack.webhook_url'))
                ->notify(new CriticalTicketReminder($ticket));
        }

        if ($tickets->count() > 0) {
            $this->info("{$tickets->count()} ticket(s) critique(s) sans réponse trouvé(s) et notifié(s).");
        } else {
            $this->info('Aucun ticket critique sans réponse trouvé.');
        }

        return Command::SUCCESS;
    }
}
