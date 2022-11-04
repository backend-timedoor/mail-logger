<?php

namespace Timedoor\MailLogger\Commands;

use Illuminate\Console\Command;
use Timedoor\MailLogger\Logger\MailLogger;

class PruneOldMailables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail-logger:prune {--hours=72 : The number of hours to retain mails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete\'s old mailables';

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
        $this->info(MailLogger::pruneMails(now()->subHours($this->option('hours'))) . ' mails pruned.');
    }
}
