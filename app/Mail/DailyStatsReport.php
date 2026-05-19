<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyStatsReport extends Mailable
{
    use Queueable, SerializesModels;

    public $stats;

    public function __construct($stats)
    {
        $this->stats = $stats;
    }

    public function build()
    {
        return $this->subject('Rapport Quotidien du Support - ' . date('d/m/Y'))
                    ->view('emails.support.daily-report');
    }
}
