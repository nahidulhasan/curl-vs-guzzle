<?php

namespace App\Repositories;

use App\Models\OtpConfig;

/**
 * Class AppVersionRepository
 * @package App\Repositories
 */
class OtpConfigRepository extends BaseRepository
{
    /**
     * @var OtpConfig
     */
    protected $model;


    /**
     * OtpConfigRepository constructor.
     * @param OtpConfig $model
     */
    public function __construct(OtpConfig $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve Otp config info
     *
     * @return mixed
     */
    public function getOtpConfig()
    {
        return $this->model->get();
    }
}
