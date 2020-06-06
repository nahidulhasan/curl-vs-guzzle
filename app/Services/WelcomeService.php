<?php

namespace App\Services;

use App\Models\WelcomeInfo;

class WelcomeService extends ApiBaseService
{


    /**
     * Retrieve guest welcome info
     *
     * @return mixed|string
     */
    public function getWelcomeInfo()
    {
        $welcome_info = WelcomeInfo::first();
        $data = [];
        if ($welcome_info) {
            $data ['message_en']         = $welcome_info->message_en;
            $data ['message_bn']         = $welcome_info->message_bn;
            $data ['login_button_title'] = $welcome_info->login_button_title;
            $data ['image']              = ($welcome_info->image) ?
                env('IMAGE_HOST') . '/storage/' . $welcome_info->image : null;
        }

        return  $this->sendSuccessResponse($data, 'Welcome Info Message');
    }
}
