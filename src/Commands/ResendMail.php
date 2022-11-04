<?php

namespace Timedoor\MailLogger\Commands;

use Illuminate\Console\Command;
use Timedoor\MailLogger\Logger\MailLogger;

class ResendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail-logger:resend-mail {id} {--now}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resend mail by id';

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
        MailLogger::resendMailById($this->argument('id'), $this->option('now'));
        $this->info('Mail resent successfully.');
    }
}
