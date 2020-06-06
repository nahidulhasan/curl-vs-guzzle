<?php

namespace App\Services;

use App\Enums\ApiCustomStatusCode;
use App\Enums\HttpStatusCode;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class CurrentBalanceService
 * @package App\Services
 */
class DummyApiService extends ApiBaseService
{

    public function getRedeemPriyojonPoints()
    {
        $data = [
            "priyojon_status" => "GOLD",
            "total_points" => 11500,
            "offers" => [
                [
                    'volume' => [
                        'amount' => 200,
                        'unit' => 'mb'
                    ],
                    'validity' => [
                        'amount' => 7,
                        'unit' => 'days'
                    ],
                    'redeem_points' => 100
                ],
                [
                    'volume' => [
                        'amount' => 300,
                        'unit' => 'mb'
                    ],
                    'minutes' => [
                        'amount' => 200,
                        'unit' => 'minutes'
                    ],

                    'validity' => [
                        'amount' => 11,
                        'unit' => 'days'
                    ],
                    'redeem_points' => 300
                ],
                [
                    'volume' => [
                        'amount' => 500,
                        'unit' => 'mb'
                    ],
                    'minutes' => [
                        'amount' => 200,
                        'unit' => 'minutes'
                    ],
                    'sms' => [
                        'amount' => 250,
                        'unit' => 'sms'
                    ],
                    'validity' => [
                        'amount' => 15,
                        'unit' => 'days'
                    ],
                    'redeem_points' => 700
                ],
            ]

        ];
        return $this->sendSuccessResponse($data, 'Redeem priyojon points');
    }


    public function getAmarOffer()
    {
        $data = [
            [
                'internet' => 999,
                'minutes' => 250,
                'sms' => 250,
                'validity' => 99,
                'price' => 999,
                'offer_code' => "MX999",
                'tag' => "Exclusive",
                'redeem_points' => 100
            ],

            [
                'internet' => 999,
                'minutes' => 0,
                'sms' => 0,
                'validity' => 99,
                'price' => 999,
                'offer_code' => "MX990",
                'tag' => "Hot Offer",
                'redeem_points' => 100
            ],

            [
                'internet' => 999,
                'minutes' => 300,
                'sms' => 300,
                'validity' => 99,
                'price' => 999,
                'offer_code' => "MX990",
                'tag' => "Hot Offer",
                'redeem_points' => 100
            ],
        ];

        try {
            return $this->sendSuccessResponse($data, 'Amar offer');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }


    public function getUsagesSummary()
    {
        $message = "User usage Summary.";

        $minutes = [
            'title' => 'Minutes',
            'total' => 300,
            'unit' => 'Min',
            'cost' => 100,
            'message' => 'Your minute usage in total'
        ];

        $internet = [
            'title' => 'Internet',
            'total' => 2024,
            'unit' => 'mb',
            'cost' => 50,
            'message' => 'Your data usage in total'
        ];

        $sms = [
            'title' => 'SMS',
            'total' => 120,
            'unit' => 'SMS',
            'cost' => 20,
            'message' => 'Your SMS usage in total'
        ];

        $roaming = [
            'title' => 'Roaming',
            'total' => 120,
            'unit' => 'TK',
            'cost' => 120,
            'message' => 'Your roaming usage in total'
        ];

        $recharge = [
            'title' => 'Recharge',
            'total' => 790,
            'unit' => 'TK',
            'cost' => 790,
            'message' => 'Your recharge amount in total'
        ];

        $vas = [
            'title' => 'Active VAS',
            'total' => 3,
            'unit' => '',
            'cost' => 120,
            'message' => 'Your VAS price in total'
        ];


        $data = [
            'total' => $minutes['cost'] + $internet['cost'] + $sms['cost'] + $roaming['cost'] + $recharge['cost'] + $vas ['cost'],
            'minutes' => $minutes,
            'sms' => $sms,
            'roaming' => $roaming,
            'recharge' => $recharge,
            'vas' => $vas,
            'internet' => $internet

        ];

        return $this->sendSuccessResponse($data, $message);
    }

    public function getRechargeHistory()
    {

        $data = [
            [
                'recharge_from' => "01927346182",
                'date' => "2019-09-12 08:09:09",
                'amount' => 50
            ],
            [
                'recharge_from' => "01927346182",
                'date' => "2019-09-13 08:09:09",
                'amount' => 100
            ],
            [
                'recharge_from' => "01927346182",
                'date' => "2019-09-13 20:09:09",
                'amount' => 300
            ],
            [
                'recharge_from' => "01927346182",
                'date' => "2019-09-14 08:09:09",
                'amount' => 200
            ],
            [
                'recharge_from' => "01927346182",
                'date' => "2019-09-15 08:09:09",
                'amount' => 100
            ],

        ];

        return $this->sendSuccessResponse($data, 'Recharge History');
    }

    public function getUsagesDetails($param, $roaming_type)
    {
        $data = [];
        $message = "No Data Available";
/*        if ($param == 'internet') {
            $message = "Internet Usage History";
            $data = [
                [
                    'duration' => [
                        'start' => Carbon::now('UTC')->subMinutes(20)->toDateTimeString(),
                        'end' => Carbon::now('UTC')->subMinutes(10)->toDateTimeString()
                    ],
                    'usage' => [
                        'total' => 200,
                        'unit' => 'mb'
                    ],
                    'cost' => 10,
                ],
                [
                    'duration' => [
                        'start' => Carbon::now('UTC')->subMinutes(80)->toDateTimeString(),
                        'end' => Carbon::now('UTC')->subMinutes(40)->toDateTimeString()
                    ],
                    'usage' => [
                        'total' => 700,
                        'unit' => 'mb'
                    ],
                    'cost' => 20,
                ],

                [
                    'duration' => [
                        'start' => Carbon::now('UTC')->subMinutes(40)->toDateTimeString(),
                        'end' => Carbon::now('UTC')->subMinutes(20)->toDateTimeString()
                    ],
                    'usage' => [
                        'total' => 300,
                        'unit' => 'mb'
                    ],
                    'cost' => 22,
                ],

                [
                    'date' => Carbon::now('UTC')->subDay(1)->toDateTimeString(),
                    'duration' => [
                        'start' => Carbon::now('UTC')->subMinutes(80)->toDateTimeString(),
                        'end' => Carbon::now('UTC')->subMinutes(30)->toDateTimeString()
                    ],
                    'usage' => [
                        'total' => 400,
                        'unit' => 'mb'
                    ],
                    'cost' => 28,
                ],
                [
                    'duration' => [
                        'start' => Carbon::now('UTC')->subMinutes(80)->toDateTimeString(),
                        'end' => Carbon::now('UTC')->subMinutes(30)->toDateTimeString()
                    ],
                    'usage' => [
                        'total' => 400,
                        'unit' => 'mb'
                    ],
                    'cost' => 28,
                ]
            ];
        }

        if ($param == 'minutes') {
            $message = "Minutes Usage History";
            $data = [
                [
                    'date' => Carbon::now('UTC')->subHours(5)->toDateTimeString(),
                    'number' => '0172121212',
                    'is_outgoing' => true,
                    'duration' => [
                        'total' => 10,
                        'unit' => 'mins'
                    ],
                    'cost' => 15
                ],
                [
                    'date' => Carbon::now('UTC')->subHours(8)->toDateTimeString(),
                    'number' => '0192121212',
                    'is_outgoing' => false,
                    'duration' => [
                        'total' => 3,
                        'unit' => 'Min'
                    ],
                    'cost' => 0
                ],
                [
                    'date' => Carbon::now('UTC')->subHours(12)->toDateTimeString(),
                    'number' => '0172121212',
                    'is_outgoing' => true,
                    'duration' => [
                        'total' => 20,
                        'unit' => 'Min'
                    ],
                    'cost' => 25
                ],
                [
                    'date' => Carbon::now('UTC')->subHours(15)->toDateTimeString(),
                    'number' => '0172121212',
                    'is_outgoing' => false,
                    'duration' => [
                        'total' => 2,
                        'unit' => 'Min'
                    ],
                    'cost' => 0
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(20)->toDateTimeString(),
                    'number' => '0182121216',
                    'is_outgoing' => true,
                    'duration' => [
                        'total' => 5,
                        'unit' => 'Min'
                    ],
                    'cost' => 8
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(24)->toDateTimeString(),
                    'number' => '0132121216',
                    'is_outgoing' => true,
                    'duration' => [
                        'total' => 9,
                        'unit' => 'Min'
                    ],
                    'cost' => 13
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(26)->toDateTimeString(),
                    'number' => '0132121216',
                    'is_outgoing' => true,
                    'duration' => [
                        'total' => 2,
                        'unit' => 'Min'
                    ],
                    'cost' => 4
                ],
            ];
        }

        if ($param == 'sms') {
            $message = "SMS Usage History";
            $data = [
                [
                    'date' => Carbon::now('UTC')->subHours(5)->toDateTimeString(),
                    'number' => '0172121212',
                    'is_outgoing' => true,
                    'usage' => [
                        'total' => 1,
                        'unit' => 'SMS'
                    ],
                    'cost' => 0.67
                ],
                [
                    'date' => Carbon::now('UTC')->subHours(8)->toDateTimeString(),
                    'number' => '0192121212',
                    'is_outgoing' => false,
                    'usage' => [
                        'total' => 1,
                        'unit' => 'SMS'
                    ],
                    'cost' => 0
                ],
                [
                    'date' => Carbon::now('UTC')->subHours(12)->toDateTimeString(),
                    'number' => '0172121212',
                    'is_outgoing' => true,
                    'usage' => [
                        'total' => 2,
                        'unit' => 'SMS'
                    ],
                    'cost' => 1.25
                ],
                [
                    'date' => Carbon::now('UTC')->subHours(15)->toDateTimeString(),
                    'number' => '0172121212',
                    'is_outgoing' => false,
                    'usage' => [
                        'total' => 2,
                        'unit' => 'SMS'
                    ],
                    'cost' => 0
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(20)->toDateTimeString(),
                    'number' => '0182121216',
                    'is_outgoing' => true,
                    'usage' => [
                        'total' => 2,
                        'unit' => 'SMS'
                    ],
                    'cost' => 1.25
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(24)->toDateTimeString(),
                    'number' => '0132121216',
                    'is_outgoing' => true,
                    'usage' => [
                        'total' => 1,
                        'unit' => 'SMS'
                    ],
                    'cost' => 0.67
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(26)->toDateTimeString(),
                    'number' => '0132121216',
                    'is_outgoing' => true,
                    'usage' => [
                        'total' => 2,
                        'unit' => 'SMS'
                    ],
                    'cost' => 1.67
                ],
            ];
        }

        if ($param == 'subscription') {
            $message = "Subscription Usage History";
            $data = [
                [
                    'service_name' => "Amar Tune",
                    'activated_date' => "2019-09-01",
                    'billing_date' => "2019-09-31",
                    'is_auto_renew' => true,
                    'price' => 15,
                ],
                [
                    'service_name' => "Contact Backup",
                    'activated_date' => "2019-09-10",
                    'billing_date' => "2019-10-09",
                    'is_auto_renew' => false,
                    'price' => 25,
                ],
                [
                    'service_name' => "Jokes Daily",
                    'activated_date' => "2019-09-10",
                    'billing_date' => "2019-10-09",
                    'is_auto_renew' => false,
                    'price' => 10,
                ],
                [
                    'service_name' => "Missed Call Alert",
                    'activated_date' => "2019-09-10",
                    'billing_date' => "2019-10-09",
                    'is_auto_renew' => true,
                    'price' => 30,
                ],
            ];
        }

        if ($param == 'recharge') {
            $message = "User Recharge History";
            $data = [
                [
                    'date' => Carbon::now('UTC')->subHours(5)->toDateTimeString(),
                    'recharge_from' => '0172121212',
                    'amount' => 30
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(25)->toDateTimeString(),
                    'recharge_from' => '0182121223',
                    'amount' => 100
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(50)->toDateTimeString(),
                    'recharge_from' => '0182121223',
                    'amount' => 100
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(150)->toDateTimeString(),
                    'recharge_from' => '0172121212',
                    'amount' => 200
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(200)->toDateTimeString(),
                    'recharge_from' => '0172121212',
                    'amount' => 50
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(250)->toDateTimeString(),
                    'recharge_from' => '0172121212',
                    'amount' => 100
                ],
            ];
        }*/

        if ($param == 'roaming' && !$roaming_type) { // roaming summary
            $data = [
                'internet' => 200,
                'minutes' => 65,
                'recharge' => 580,
                'sms' => 6.67
            ];
            $message = 'Roaming Usage Summary';
        }

        if ($param == 'roaming' && $roaming_type == 'internet') { // roaming summary
            $data = [
                [
                    'duration' => [
                        'start' => Carbon::now('UTC')->subMinutes(20)->toDateTimeString(),
                        'end' => Carbon::now('UTC')->subMinutes(10)->toDateTimeString()
                    ],
                    'usage' => [
                        'total' => 200,
                        'unit' => 'mb'
                    ],
                    'cost' => 10,
                ],
                [
                    'duration' => [
                        'start' => Carbon::now('UTC')->subMinutes(80)->toDateTimeString(),
                        'end' => Carbon::now('UTC')->subMinutes(40)->toDateTimeString()
                    ],
                    'usage' => [
                        'total' => 700,
                        'unit' => 'mb'
                    ],
                    'cost' => 110,
                ],

                [
                    'duration' => [
                        'start' => Carbon::now('UTC')->subMinutes(40)->toDateTimeString(),
                        'end' => Carbon::now('UTC')->subMinutes(20)->toDateTimeString()
                    ],
                    'usage' => [
                        'total' => 300,
                        'unit' => 'mb'
                    ],
                    'cost' => 20,
                ],

                [
                    'duration' => [
                        'start' => Carbon::now('UTC')->subMinutes(80)->toDateTimeString(),
                        'end' => Carbon::now('UTC')->subMinutes(30)->toDateTimeString()
                    ],
                    'usage' => [
                        'total' => 400,
                        'unit' => 'mb'
                    ],
                    'cost' => 30,
                ],
                [
                    'duration' => [
                        'start' => Carbon::now('UTC')->subMinutes(80)->toDateTimeString(),
                        'end' => Carbon::now('UTC')->subMinutes(30)->toDateTimeString()
                    ],
                    'usage' => [
                        'total' => 400,
                        'unit' => 'mb'
                    ],
                    'cost' => 30,
                ]
            ];
            $message = 'Roaming Internet Usage Summary';
        }

        if ($param == 'roaming' && $roaming_type == 'minutes') {
            $message = "Roaming Minutes Usage History";
            $data = [
                [
                    'date' => Carbon::now('UTC')->subHours(5)->toDateTimeString(),
                    'number' => '0172121212',
                    'is_outgoing' => true,
                    'duration' => [
                        'total' => 10,
                        'unit' => 'Min'
                    ],
                    'cost' => 15
                ],
                [
                    'date' => Carbon::now('UTC')->subHours(8)->toDateTimeString(),
                    'number' => '0192121212',
                    'is_outgoing' => false,
                    'duration' => [
                        'total' => 3,
                        'unit' => 'Min'
                    ],
                    'cost' => 0
                ],
                [
                    'date' => Carbon::now('UTC')->subHours(12)->toDateTimeString(),
                    'number' => '0172121212',
                    'is_outgoing' => true,
                    'duration' => [
                        'total' => 20,
                        'unit' => 'Min'
                    ],
                    'cost' => 25
                ],
                [
                    'date' => Carbon::now('UTC')->subHours(15)->toDateTimeString(),
                    'number' => '0172121212',
                    'is_outgoing' => false,
                    'duration' => [
                        'total' => 2,
                        'unit' => 'Min'
                    ],
                    'cost' => 0
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(20)->toDateTimeString(),
                    'number' => '0182121216',
                    'is_outgoing' => true,
                    'duration' => [
                        'total' => 5,
                        'unit' => 'Min'
                    ],
                    'cost' => 8
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(24)->toDateTimeString(),
                    'number' => '0132121216',
                    'is_outgoing' => true,
                    'duration' => [
                        'total' => 9,
                        'unit' => 'Min'
                    ],
                    'cost' => 13
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(26)->toDateTimeString(),
                    'number' => '0132121216',
                    'is_outgoing' => true,
                    'duration' => [
                        'total' => 2,
                        'unit' => 'Min'
                    ],
                    'cost' => 4
                ],
            ];
        }

        if ($param == 'roaming' && $roaming_type == 'sms') {
            $message = "Roaming SMS Usage History";
            $data = [
                [
                    'date' => Carbon::now('UTC')->subHours(5)->toDateTimeString(),
                    'number' => '0172121212',
                    'is_outgoing' => true,
                    'usage' => [
                        'total' => 1,
                        'unit' => 'SMS'
                    ],
                    'cost' => 0.67
                ],
                [
                    'date' => Carbon::now('UTC')->subHours(8)->toDateTimeString(),
                    'number' => '0192121212',
                    'is_outgoing' => false,
                    'usage' => [
                        'total' => 1,
                        'unit' => 'SMS'
                    ],
                    'cost' => 0
                ],
                [
                    'date' => Carbon::now('UTC')->subHours(12)->toDateTimeString(),
                    'number' => '0172121212',
                    'is_outgoing' => true,
                    'usage' => [
                        'total' => 2,
                        'unit' => 'SMS'
                    ],
                    'cost' => 1.25
                ],
                [
                    'date' => Carbon::now('UTC')->subHours(15)->toDateTimeString(),
                    'number' => '0172121212',
                    'is_outgoing' => false,
                    'usage' => [
                        'total' => 2,
                        'unit' => 'SMS'
                    ],
                    'cost' => 0
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(20)->toDateTimeString(),
                    'number' => '0182121216',
                    'is_outgoing' => true,
                    'usage' => [
                        'total' => 2,
                        'unit' => 'SMS'
                    ],
                    'cost' => 1.25
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(24)->toDateTimeString(),
                    'number' => '0132121216',
                    'is_outgoing' => true,
                    'usage' => [
                        'total' => 1,
                        'unit' => 'SMS'
                    ],
                    'cost' => 0.67
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(26)->toDateTimeString(),
                    'number' => '0132121216',
                    'is_outgoing' => true,
                    'usage' => [
                        'total' => 2,
                        'unit' => 'SMS'
                    ],
                    'cost' => 1.67
                ],
            ];
        }

        if ($param == 'roaming' && $roaming_type == 'recharge') {
            $message = "User Recharge History";
            $data = [
                [
                    'date' => Carbon::now('UTC')->subHours(5)->toDateTimeString(),
                    'recharge_from' => '0172121212',
                    'amount' => 30
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(25)->toDateTimeString(),
                    'recharge_from' => '0182121223',
                    'amount' => 100
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(50)->toDateTimeString(),
                    'recharge_from' => '0182121223',
                    'amount' => 100
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(150)->toDateTimeString(),
                    'recharge_from' => '0172121212',
                    'amount' => 200
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(200)->toDateTimeString(),
                    'recharge_from' => '0172121212',
                    'amount' => 50
                ],

                [
                    'date' => Carbon::now('UTC')->subHours(250)->toDateTimeString(),
                    'recharge_from' => '0172121212',
                    'amount' => 100
                ],
            ];
        }

        return $this->sendSuccessResponse($data, $message);
    }


    /**
     * USSD Code List
     *
     * @return JsonResponse
     */
    public function getUssdCode()
    {
        $data = [
            [
                'name' => "Amar Offer",
                'ussd_code' => "888#",
                'purpose' => "Amar Offer",
                'Provider' => 'Banglalink'
            ],

            [
                'name' => "Priyojon Offer",
                'ussd_code' => "*16*649#",
                'purpose' => "Amar Offer",
                'Provider' => 'Banglalink'
            ],

            [
                'name' => "Amar Offer",
                'ussd_code' => "884#",
                'purpose' => "Amar Offer",
                'Provider' => 'Banglalink'
            ],

            [
                'name' => "Priyojon Offer",
                'ussd_code' => "*16*647#",
                'purpose' => "Amar Offer",
                'Provider' => 'Banglalink'
            ],

        ];

        try {
            return $this->sendSuccessResponse($data, 'USSD Code List');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }


    public function getPriyojonTiers()
    {
        $message = "Priyojon Tiers Info";

        $data = [
            [
                'name' => 'SILVER',
                'slug' => 'SILVER',
                'point' => [
                    'lower' => 1000,
                    'upper' => 2999,
                ],
            ],
            [
                'name' => 'GOLD',
                'slug' => 'GOLD',
                'point' => [
                    'lower' => 3000,
                    'upper' => 5999,
                ],
            ],
            [
                'name' => 'PLATINUM',
                'slug' => 'PLATINUM',
                'point' => [
                    'lower' => 6000,
                    'upper' => null,
                ],
            ]
        ];

        return $this->sendSuccessResponse($data, $message, []);
    }

    /**
     * @param $msisdn
     * @return JsonResponse
     */
    public function getPriyojonStatus($msisdn)
    {
        $message = "Your Priyojon Status Info";

        $tiers = [
            [
                'name' => 'Silver',
                'slug' => 'silver',
                'point' => 1500,
            ],
            [
                'name' => 'Gold',
                'slug' => 'gold',
                'point' => 3000,
            ],
            [
                'name' => 'Platinum',
                'slug' => 'platinum',
                'point' => 7000,
            ]
        ];

        $random_points = rand(1000, 7000);
        if ($random_points > 1500 && $random_points < 3000) {
            $type = "Silver";
        } elseif ($random_points >= 3000 && $random_points < 7000) {
            $type = "Gold";
        } else {
            $type = "Platinum";
        }
        $data = [
            'tiers' => $tiers,
            'priyojon_status' => [
                'is_priyojon' => true,
                'priyojon_info' => [
                    'type' => $type,
                    'total_points' => $random_points
                ],
                'is_upgradable' => ($type != 'Platinum') ? true : false,
                'upgrade_info' => ($type != 'Platinum') ? [
                    'upgrade_to' => ($type === 'Silver') ? 'Gold' : 'Platinum',
                    'cost' => rand(1000, 2000),
                    'from' => Carbon::now('UTC')->addDays(2)->toDateString() . ' 00:00:00'
                ] : []
            ]
        ];

        return $this->sendSuccessResponse($data, $message, []);
    }


    public function getPriyojonOffers(Request $request)
    {

        if ($request->priyojon_status === "platinum") {
            $data = [
                [
                    'id' => uniqid(),
                    'title' => 'BOGO offer',
                    'expires_at' => 'expires in 10 days',
                    'type' => 'food',
                    'partner' => [
                        'code' => 'PRB1099',
                        'name' => 'Westin',
                        'rating' => 4.9,
                        'total_rating' => 100
                    ]
                ],
                [
                    'id' => uniqid(),
                    'title' => '20% discount',
                    'expires_at' => 'expires in 30 days',
                    'type' => 'travel',
                    'partner' => [
                        'code' => 'PRB1089',
                        'name' => 'Dusai Resort',
                        'rating' => 4.5,
                        'total_rating' => 80
                    ]
                ],
                [
                    'id' => uniqid(),
                    'title' => '15% discount',
                    'expires_at' => 'expires in 20 days',
                    'type' => 'life-style',
                    'partner' => [
                        'code' => 'PRB2008',
                        'name' => 'Exclusive Kaniz Hair & Beauty',
                        'rating' => 4.5,
                        'total_rating' => 50
                    ]
                ]
            ];
        } elseif ($request->priyojon_status === "gold") {
            $data = [
                [
                    'id' => uniqid(),
                    'title' => '5% discount',
                    'expires_at' => 'expires in 10 days',
                    'type' => 'food',
                    'partner' => [
                        'code' => 'PRB1012',
                        'name' => 'Fish & Co.',
                        'rating' => 4.5,
                        'total_rating' => 40
                    ]
                ],
                [
                    'id' => uniqid(),
                    'title' => '10% discount',
                    'expires_at' => 'expires in 30 days',
                    'type' => 'food',
                    'partner' => [
                        'code' => 'PRB1012',
                        'name' => 'North-End Cafe',
                        'rating' => 4.5,
                        'total_rating' => 20
                    ]
                ],
                [
                    'id' => uniqid(),
                    'title' => '5% discount',
                    'expires_at' => 'expires in 20 days',
                    'type' => 'travel',
                    'partner' => [
                        'code' => 'PRB1013',
                        'name' => 'Novo Air',
                        'rating' => 4.0,
                        'total_rating' => 50
                    ]
                ],
                [
                    'id' => uniqid(),
                    'title' => '15% discount',
                    'expires_at' => 'expires in 4 days',
                    'type' => 'life-style',
                    'partner' => [
                        'code' => 'PRB1012',
                        'name' => 'Artisan Bd.',
                        'rating' => 4.5,
                        'total_rating' => 30
                    ]
                ],
            ];
        } else {
            $data = [
                [
                    'id' => uniqid(),
                    'title' => '5% discount',
                    'expires_at' => 'expires in 10 days',
                    'type' => 'food',
                    'partner' => [
                        'code' => 'PRB3030',
                        'name' => 'Take Out',
                        'rating' => 4.1,
                        'total_rating' => 30
                    ]
                ],
                [
                    'id' => uniqid(),
                    'title' => '10% discount',
                    'expires_at' => 'expires in 30 days',
                    'type' => 'food',
                    'partner' => [
                        'code' => 'PRB4020',
                        'name' => 'Tarka',
                        'rating' => 4.5,
                        'total_rating' => 20
                    ]
                ],
                [
                    'id' => uniqid(),
                    'title' => '5% discount',
                    'expires_at' => 'expires in 4 days',
                    'type' => 'life-style',
                    'partner' => [
                        'code' => 'PRB5030',
                        'name' => 'Star Cineplex',
                        'rating' => 4.6,
                        'total_rating' => 30
                    ]
                ],
            ];
        }


        return $this->sendSuccessResponse($data, 'Priyojon ' . $request->priyojon_status . ' Rewards');
    }


    /**
     * @return JsonResponse
     */
    public function getBalanceSummary()
    {
        $message = "User Balance Summary";
        $balance = rand(50, 200) / 10;
        $data = [
            'number' => '01932212121',
            'balance' => [
                'amount' => $balance,
                'unit' => 'taka',
                'expires_in' => Carbon::now('UTC')->addDays(120)->toDateTimeString(),
                'loan' => [
                    'is_eligible' => ($balance < 10) ? true : false,
                    'amount' => ($balance < 10) ? rand(10, 30) : 0
                ]
            ],
            'internet' => [
                'total' => 3072,
                'remaining' => 1024,
                'unit' => 'mb',
            ],
            'minutes' => [
                'total' => 300,
                'remaining' => 100,
                'unit' => 'minutes',
            ],
            'sms' => [
                'total' => 500,
                'remaining' => 400,
                'unit' => 'sms',
            ],
        ];
        return $this->sendSuccessResponse($data, $message, []);
    }


    /**
     * @param $type
     * @return JsonResponse
     */
    public function getBalanceDetails($type)
    {
        $data = [];
        $message = '';
        if ($type === 'balance') {
            $message = "Balance Details Info";
            $data = [
                'number' => '01933333111',
                'remaining_balance' => [
                    'amount' => 200,
                    'currency' => 'taka',
                    'expires_in' => Carbon::now('UTC')->addDays(120)->toDateTimeString()
                ],
                'roaming_balance' => [
                    'amount' => 20,
                    'currency' => 'usd',
                    'expires_in' => Carbon::now('UTC')->addDays(90)->toDateTimeString()
                ],
            ];
        } elseif ($type === 'internet') {
            $message = "Active Internet Offers Info";
            $data = [
                [
                    'package_name' => 'InternetPACKAGE1',
                    'total' => 1024,
                    'remaining' => 400,
                    'expires_in' => Carbon::now('UTC')->addDays(3)->toDateTimeString(),
                    'auto_renew' => true,
                ],
                [
                    'package_name' => 'InternetPACKAGE2',
                    'total' => 2048,
                    'remaining' => 1024,
                    'expires_in' => Carbon::now("UTC")->addDays(1)->toDateTimeString(),
                    'auto_renew' => false,
                ]
            ];
        } elseif ($type === 'minutes') {
            $message = "Active Minutes Offers Info";
            $data = [
                [
                    'package_name' => 'MinutesPACKAGE1',
                    'total' => 250,
                    'remaining' => 100,
                    'expires_in' => Carbon::now("UTC")->addDays(6)->toDateTimeString(),
                    'auto_renew' => true,
                ],
                [
                    'package_name' => 'MinutesPACKAGE2',
                    'total' => 400,
                    'remaining' => 30,
                    'expires_in' => Carbon::now("UTC")->addDays(5)->toDateTimeString(),
                    'auto_renew' => false,
                ]
            ];
        } elseif ($type === 'sms') {
            $message = "Active SMS Offers Info";
            $data = [
                [
                    'package_name' => 'SmsPACKAGE1',
                    'total' => 500,
                    'remaining' => 250,
                    'expires_in' => Carbon::now("UTC")->addDays(4)->toDateTimeString(),
                    'auto_renew' => true,
                ],
                [
                    'package_name' => 'SmsPACKAGE2',
                    'total' => 400,
                    'remaining' => 30,
                    'expires_in' => Carbon::now("UTC")->addDays(3)->toDateTimeString(),
                    'auto_renew' => false,
                ]
            ];
        }

        return $this->sendSuccessResponse($data, $message, []);
    }

    public function getManageAccounts()
    {
        $message = "Added Accounts Info";
        $data = [
            'primary' => [
                'name' => 'Thwoi',
                'number' => '01936000000',
                'active_package' => 'package_one',
                'is_active' => true
            ],
            'additional' => [
                [
                    'name' => 'Charlie',
                    'number' => '01936000011',
                    'active_package' => 'package_one',
                    'is_active' => true
                ],
                [
                    'name' => 'Ching',
                    'number' => '01936000022',
                    'active_package' => 'package_two',
                    'is_active' => false
                ]
            ]
        ];

        return $this->sendSuccessResponse($data, $message, []);
    }

    public function requestAdvancedLoan(Request $request)
    {
        $message = "15 tk Advanced Loan added to your account";
        $data = [
            'added_loan' => 15,
            'current_balance ' => 18,
        ];
        return $this->sendSuccessResponse($data, $message, []);
    }

    public function getSubscribedServices(Request $request)
    {
        $message = "user Subscribed digital Services";
        $data = [
            [
                'name' => 'Misscall Alert',
                'activated_date' => '2019-08-01',
                'price' => 30,
                'next_billing_date' => '2019-09-31',
                'is_auto_renew' => true
            ],
            [
                'name' => 'Amar Tune',
                'activated_date' => '2019-09-01',
                'price' => 20,
                'next_billing_date' => '2019-09-31',
                'is_auto_renew' => false
            ],
            [
                'name' => 'BnaglaFlix',
                'activated_date' => '2019-09-01',
                'price' => 15,
                'next_billing_date' => '2019-09-31',
                'is_auto_renew' => true
            ],
            [
                'name' => 'Vive',
                'activated_date' => '2019-09-04',
                'price' => 10,
                'next_billing_date' => '2019-10-03',
                'is_auto_renew' => false
            ]
        ];

        return $this->sendSuccessResponse($data, $message);
    }


    public function getActiveServices(Request $request)
    {
        $message = "All Available Services";

        $data = [
            [
                'id' => uniqid(),
                'name' => 'Power 2 You',
                'component_identifier' => 'power_2_you',
                'icon' => 'https://image.flaticon.com/icons/svg/70/70159.svg'
            ],
            [
                'id' => uniqid(),
                'name' => 'Emergency Balance',
                'component_identifier' => 'emergency_balance',
                'icon' => 'https://image.flaticon.com/icons/svg/70/70159.svg'
            ],
            [
                'id' => uniqid(),
                'name' => 'Manage FNF',
                'component_identifier' => 'manage_fnf',
                'icon' => 'https://image.flaticon.com/icons/svg/70/70159.svg'
            ],
            [
                'id' => uniqid(),
                'name' => 'Contact Backup',
                'component_identifier' => 'contact_backup',
                'icon' => 'https://image.flaticon.com/icons/svg/70/70159.svg'
            ],
            [
                'id' => uniqid(),
                'name' => 'Migrate Plan',
                'component_identifier' => 'migrate_plan',
                'icon' => 'https://image.flaticon.com/icons/svg/70/70159.svg'
            ]
        ];

        return $this->sendSuccessResponse($data, $message);
    }

    public function getCustomerCares(Request $request)
    {
        $message = "All Available Customer Cares";

        $data = [
            [
                'id' => uniqid(),
                'name' => 'Chat With Mita',
                'component_identifier' => 'chat_mita',
                'icon' => 'https://image.flaticon.com/icons/svg/70/70159.svg'
            ],
            [
                'id' => uniqid(),
                'name' => 'Store Locator',
                'component_identifier' => 'store_locator',
                'icon' => 'https://image.flaticon.com/icons/svg/70/70159.svg'
            ],
            [
                'id' => uniqid(),
                'name' => 'Report Lost Sim',
                'component_identifier' => 'report_lost_sim',
                'icon' => 'https://image.flaticon.com/icons/svg/70/70159.svg'
            ],
            [
                'id' => uniqid(),
                'name' => 'View USSD Code',
                'component_identifier' => 'ussd_code',
                'icon' => 'https://image.flaticon.com/icons/svg/70/70159.svg'
            ],
            [
                'id' => uniqid(),
                'name' => 'Call Customer Care',
                'component_identifier' => 'call_customer_care',
                'icon' => 'https://image.flaticon.com/icons/svg/70/70159.svg'
            ]
        ];

        return $this->sendSuccessResponse($data, $message);
    }


    public function getInternetPacks(Request $request)
    {
        $message = "All Available Internet Packs";

        $data = [
            [
                'type' => 'power_pack',
                'title' => 'Power Pack',
                'packs' => [
                    [
                        'product_code' => 'PW01010',
                        'tag' => 'Exclusive',
                        'price' => 99,
                        'volume' => 1024,
                        'validity' => 5,
                        'ussd_code' => '*121*2*1',
                        'bonus' => [

                        ],

                    ],
                    [
                        'product_code' => 'PW01011',
                        'tag' => 'Exclusive',
                        'price' => 199,
                        'volume' => 1024 * 3,
                        'validity' => 7,
                        'ussd_code' => '*121*2*2',
                        'bonus' => [
                            [
                                'title' => 'Youtube Pack',
                                'volume' => 512
                            ]
                        ],

                    ],
                    [
                        'product_code' => 'PW01012',
                        'tag' => 'Exclusive',
                        'price' => 299,
                        'volume' => 1024 * 4,
                        'validity' => 15,
                        'ussd_code' => '*121*2*3',
                        'bonus' => [

                        ],

                    ],

                    [
                        'product_code' => 'PW01013',
                        'tag' => 'Popular',
                        'price' => 299,
                        'volume' => 1024 * 1.5,
                        'validity' => 30,
                        'ussd_code' => '*121*2*4',
                        'bonus' => [

                        ],

                    ],
                ],
            ],
            [
                'type' => 'weekly_pack',
                'title' => 'Weekly Pack',
                'packs' => [
                    [
                        'product_code' => 'WP01010',
                        'tag' => 'Hot',
                        'price' => 111,
                        'volume' => 1024 * 1.5,
                        'validity' => 7,
                        'ussd_code' => '*121*2*5',
                        'bonus' => [

                        ],

                    ],
                    [
                        'product_code' => 'WP01011',
                        'tag' => 'Exclusive',
                        'price' => 199,
                        'volume' => 1024 * 2,
                        'validity' => 7,
                        'ussd_code' => '*121*2*6',
                        'bonus' => [
                            [
                                'title' => 'Youtube Pack',
                                'volume' => 512
                            ]
                        ],

                    ],
                    [
                        'product_code' => 'WP01012',
                        'tag' => 'Popular',
                        'price' => 299,
                        'volume' => 1024 * 2.5,
                        'validity' => 7,
                        'ussd_code' => '*121*2*7',
                        'bonus' => [

                        ],

                    ],
                ],
            ],
            [
                'type' => 'monthly_pack',
                'title' => 'Monthly Pack',
                'packs' => [
                    [
                        'product_code' => 'MP01010',
                        'tag' => 'Popular',
                        'price' => 299,
                        'volume' => 1024 * 3.5,
                        'validity' => 30,
                        'ussd_code' => '*121*2*8',
                        'bonus' => [

                        ],

                    ],
                    [
                        'product_code' => 'MP01011',
                        'tag' => 'Exclusive',
                        'price' => 399,
                        'volume' => 1024 * 5.5,
                        'validity' => 30,
                        'ussd_code' => '*121*2*9',
                        'bonus' => [
                            [
                                'title' => 'Youtube Pack',
                                'volume' => 512
                            ]
                        ],

                    ]
                ],
            ],
            [
                'type' => 'social_pack',
                'title' => 'Social Pack',
                'packs' => [
                    [
                        'product_code' => 'SP01010',
                        'tag' => 'Hot',
                        'price' => 49,
                        'volume' => 1024 * 1.5,
                        'validity' => 7,
                        'ussd_code' => '*121*2*10',
                        'bonus' => [

                        ],

                    ],
                    [
                        'product_code' => 'SP01011',
                        'tag' => 'Popular',
                        'price' => 119,
                        'volume' => 1024 * 2,
                        'validity' => 15,
                        'ussd_code' => '*121*2*11',
                        'bonus' => [
                        ],

                    ]
                ],
            ]
        ];

        return $this->sendSuccessResponse($data, $message);
    }

    public function buyInternetPack(Request $request)
    {
        // just to randomize buy status.randomly choose a flag to return status according to that

        $is_success = (bool)random_int(0, 1);

        $pack_prices = [
            'PW01010' => 99,
            'PW01011' => 199,
            'PW01012' => 299,
            'PW01013' => 299,
            'WP01010' => 111,
            'WP01011' => 199,
            'WP01012' => 299,
            'MP01010' => 299,
            'MP01011' => 399,
            'SP01010' => 49,
            'SP01011' => 119,
        ];

        $cur_pack_price = isset($pack_prices[$request->product_code]) ? $pack_prices[$request->product_code] : 299;

        if (!$is_success) {
            return $this->sendSuccessResponse([
                'internet_pack_price' => $cur_pack_price,
                'current_balance' => abs($cur_pack_price - rand(10, 100)),
            ], 'Insufficient Balance to purchase the pack', null, HttpStatusCode::SUCCESS, ApiCustomStatusCode::INSUFFICIENT_BALANCE);
        }

        return $this->sendSuccessResponse([], 'You have successfully buy the internet pack');
    }
}
