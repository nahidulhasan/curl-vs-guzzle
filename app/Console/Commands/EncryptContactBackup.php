<?php

namespace App\Console\Commands;

use App\Models\CustomerContactBackup;
use Illuminate\Console\Command;

class EncryptContactBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encrypt:contact_backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt existing contact backup';

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
        $backups = CustomerContactBackup::all();

        $this->info('.......Contact Backup Encryption Start ......');
        $bar = $this->output->createProgressBar(count($backups));
        $bar->start();
        foreach ($backups as $backup) {
            $insert_data = encrypt($backup->contact_backup);

            $backup->update([
                'contacts' => $insert_data
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->info('.......Contact Backup Encryption Start ......');
    }
}
