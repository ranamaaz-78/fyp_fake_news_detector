<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class TestMailCommand extends Command
{
    protected $signature = 'mail:test {email? : Address to send the test to}';

    protected $description = 'Send a test email using current MAIL_* settings and show any SMTP error';

    public function handle(): int
    {
        $to = $this->argument('email') ?: config('mail.from.address');

        $this->info('Mail configuration:');
        $this->line('  mailer   : '.config('mail.default'));
        $this->line('  host     : '.config('mail.mailers.smtp.host'));
        $this->line('  port     : '.config('mail.mailers.smtp.port'));
        $this->line('  scheme   : '.(config('mail.mailers.smtp.scheme') ?: 'null'));
        $this->line('  username : '.config('mail.mailers.smtp.username'));
        $this->line('  from     : '.config('mail.from.address'));
        $this->line('  app.url  : '.config('app.url'));
        $this->line('  to       : '.$to);
        $this->newLine();

        try {
            Mail::raw('VeriFact AI SMTP test at '.now()->toDateTimeString(), function ($message) use ($to) {
                $message->to($to)->subject('VeriFact AI — SMTP Test');
            });

            $this->info('SENT_OK — check inbox/spam for: '.$to);

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('FAIL: '.$e->getMessage());
            $this->line($e->getFile().':'.$e->getLine());

            return self::FAILURE;
        }
    }
}
