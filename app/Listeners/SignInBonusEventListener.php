<?php

namespace App\Listeners;

use App\Events\SignInBonus;
use App\Services\Bonus\BonusApiIntegrationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SignInBonusEventListener
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
     * @param SignInBonus $event
     * @return void
     * @throws \App\Exceptions\IdpException
     */
    public function handle(SignInBonus $event)
    {
        $data = $event->data;
        BonusApiIntegrationService::bonusRequest($data);
    }
}
