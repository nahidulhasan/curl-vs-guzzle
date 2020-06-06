<?php

namespace App\Repositories;

use App\Models\WelcomeInfo;

/**
 * Class WelcomeInfoRepository
 * @package App\Repositories
 */
class WelcomeInfoRepository extends BaseRepository
{

    /**
     * @var WelcomeInfo
     */
    protected $model;


    /**
     * WelcomeInfoRepository constructor.
     * @param WelcomeInfo $model
     */
    public function __construct(WelcomeInfo $model)
    {
        $this->model = $model;
    }


    /**
     * Retrieve guest welcome info
     *
     * @return mixed
     */
    public function getGuestWelcomeInfo()
    {
        return $this->model::select('guest_salutation', 'guest_message', 'icon')->get();
    }


    /**
     * Retrieve user welcome info
     *
     * @return mixed
     */
    public function getUserWelcomeInfo()
    {
        return $this->model::select('user_salutation', 'user_message', 'icon')->get();
    }
}
