<?php

namespace Timedoor\MailLogger\Commands;

use Illuminate\Console\Command;
use Timedoor\MailLogger\Logger\MailLogger;

class ResendUnSentMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail-logger:resend-unsent-mail {--now}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resend all unsent mails';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info(MailLogger::resendUnsentMails($this->option('now')) . ' Mail(s) retried successfully.');
    }
}
