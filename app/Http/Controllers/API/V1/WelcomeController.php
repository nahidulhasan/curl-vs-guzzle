<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\WelcomeService;

/**
 * Class WelcomeController
 * @package App\Http\Controllers\API\V1
 */
class WelcomeController extends Controller
{

    /**
     * @var welcomeService
     */
    protected $welcomeService;


    /**
     * WelcomeController constructor.
     * @param WelcomeService $welcomeService
     */
    public function __construct(WelcomeService $welcomeService)
    {
        $this->welcomeService = $welcomeService;
    }

    /**
     * Retrieve guest welcome info
     *
     * @return mixed|string
     */
    public function getWelcomeInfo()
    {
        return $this->welcomeService->getWelcomeInfo();
    }

}
