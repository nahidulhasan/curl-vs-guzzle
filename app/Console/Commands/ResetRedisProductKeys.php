<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class ResetRedisProductKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:reset-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Available Products Redis keys ';

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
        $pattern = Str::slug(env('REDIS_PREFIX', 'laravel'), '_') . '_database_';
        $keys = Redis::keys('available_products:*');
        $values = [];

        foreach ($keys as $key) {
            $values [] = str_replace($pattern, '', $key);
        }
        //Log::info(json_encode($values));
        if (!empty($values)) {
            Redis::del($values);
        }
    }
}
