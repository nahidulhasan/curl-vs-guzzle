<?php

namespace App\Repositories;

use App\Models\UssdCode;

/**
 * Class UssdCodeRepository
 * @package App\Repositories
 */
class UssdCodeRepository extends BaseRepository
{
    /**
     * @var OtpConfig
     */
    protected $model;


    /**
     * UssdCodeRepository constructor.
     * @param UssdCode $model
     */
    public function __construct(UssdCode $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve ussd code list
     *
     * @return mixed
     */
    public function getUssdCode()
    {
        return $this->model->get()->sortBy('title');
    }
}
