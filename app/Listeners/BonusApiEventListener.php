<?php

namespace App\Listeners;

use App\Events\BonusApiEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BonusApiEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  BonusApiEvent  $event
     * @return void
     */
    public function handle(BonusApiEvent $event)
    {
        //
    }
}
