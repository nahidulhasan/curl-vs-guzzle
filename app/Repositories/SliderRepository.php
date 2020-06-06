<?php

namespace App\Repositories;

use App\Models\Slider;

/**
 * Class SliderRepository
 * @package App\Repositories
 */
class SliderRepository extends BaseRepository
{
    /**
     * @var model
     */
    protected $model;


    /**
     * SliderRepository constructor.
     * @param Slider $model
     */
    public function __construct(Slider $model)
    {
        $this->model = $model;
    }


    /**
     * Retrieve Home Slider info
     *
     * @return mixed
     */
    public function getHomeSliderInfo()
    {
        $data = $this->model::whereHas('sliderImages', function ($q) {
            $q->where('is_active', 1);
        })->with(['sliderImages' => function ($q) {
            $q->where('is_active', 1);
        }])->where('platform', "App")
            ->where('component_id', 1)
            ->get();

        return $data;
    }


    /**
     * Retrieve  Dashboard Slider info
     *
     * @return mixed
     */
    public function getDashboardSliderInfo()
    {
        $data = $this->model::whereHas('sliderImages', function ($q) {
            $q->where('is_active', 1);
        })->with(['sliderImages' => function ($q) {
            $q->where('is_active', 1);
        }])->where('platform', "App")
            ->where('component_id', 2)
            ->get();

        return $data;
    }


    /**
     * Retrieve Internet Slider info
     *
     * @return mixed
     */
    public function getInternetSliderInfo()
    {
        $data = $this->model::whereHas('sliderImages', function ($q) {
            $q->where('is_active', 1);
        })->with(['sliderImages' => function ($q) {
            $q->where('is_active', 1);
        }])->where('platform', "App")
            ->where('component_id', 5)
            ->get();

        return $data;
    }


    /**
     * Retrieve Bundle Slider info
     *
     * @return mixed
     */
    public function getBundleSliderInfo()
    {
        $data = $this->model::whereHas('sliderImages', function ($q) {
            $q->where('is_active', 1);
        })->with(['sliderImages' => function ($q) {
            $q->where('is_active', 1);
        }])->where('platform', "App")
            ->where('component_id', 6)
            ->get();

        return $data;
    }

    /**
     * Retrieve History Slider info
     *
     * @return mixed
     */
    public function getHistorySliderInfo()
    {
        $data = $this->model::whereHas('sliderImages', function ($q) {
            $q->where('is_active', 1);
        })->with(['sliderImages' => function ($q) {
            $q->where('is_active', 1);
        }])->where('platform', "App")
            ->where('component_id', 7)
            ->get();

        return $data;
    }

    public function getMinuteSliderInfo()
    {
        $data = $this->model::whereHas('sliderImages', function ($q) {
            $q->where('is_active', 1);
        })->with(['sliderImages' => function ($q) {
            $q->where('is_active', 1);
        }])->where('platform', "App")
            ->where('component_id', 15)
            ->get();

        return $data;
    }

    public function getSMSSliderInfo()
    {
        $data = $this->model::whereHas('sliderImages', function ($q) {
            $q->where('is_active', 1);
        })->with(['sliderImages' => function ($q) {
            $q->where('is_active', 1);
        }])->where('platform', "App")
            ->where('component_id', 16)
            ->get();

        return $data;
    }

    public function getRechargeOfferSliderInfo()
    {
        $data = $this->model::whereHas('sliderImages', function ($q) {
            $q->where('is_active', 1);
        })->with(['sliderImages' => function ($q) {
            $q->where('is_active', 1);
        }])->where('platform', "App")
            ->where('component_id', 13)
            ->get();

        return $data;
    }

    public function getCallRateSliderInfo()
    {
        $data = $this->model::whereHas('sliderImages', function ($q) {
            $q->where('is_active', 1);
        })->with(['sliderImages' => function ($q) {
            $q->where('is_active', 1);
        }])->where('platform', "App")
            ->where('component_id', 14)
            ->get();

        return $data;
    }

    public function getAmarOfferSliderInfo()
    {
        $data = $this->model::whereHas('sliderImages', function ($q) {
            $q->where('is_active', 1);
        })->with(['sliderImages' => function ($q) {
            $q->where('is_active', 1);
        }])->where('platform', "App")
            ->where('component_id', 17)
            ->get();

        return $data;
    }
}
