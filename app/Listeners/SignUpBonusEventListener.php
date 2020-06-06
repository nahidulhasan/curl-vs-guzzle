<?php

namespace App\Listeners;

use App\Events\SignUpBonus;
use App\Services\Bonus\BonusApiIntegrationService;
use App\Services\Bonus\IDMIntegrationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SignUpBonusEventListener
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
     * @param SignUpBonus $event
     * @return void
     * @throws \App\Exceptions\IdpException
     */
    public function handle(SignUpBonus $event)
    {
        $data = $event->data;
        BonusApiIntegrationService::bonusRequest($data);

    }
}
