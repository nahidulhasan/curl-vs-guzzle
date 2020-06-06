<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnContactBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contact_backup:drop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Customer Contact backups drop column';

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
        Schema::table('customer_contact_backups', function (Blueprint $table) {
            $table->dropColumn('contact_backup');
        });
    }
}
