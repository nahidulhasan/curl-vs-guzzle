<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WelcomeInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $welcome = [
            [
                'guest_salutation' => "Good Morning",
                'user_salutation' =>"Good Morning Fariha",
                'guest_message' => "Get a new connection enjoy all day. Facebook browsing for just 25tk/day",
                'user_message' => "Looks like it will be sunny and warm outside. Take your shades with you",
                'icon' => "http://www.myiconfinder.com/uploads/iconsets/256-256-98e2a448aca3310dfd045e443106efba-weather.png"
            ]
        ];

        DB::table('welcome_info')->insert($welcome);


        factory(App\Models\WelcomeInfo::class, 10)->create();
    }
}



