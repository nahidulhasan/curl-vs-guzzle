<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $offers = [
            [
                'name' => "Nahidul Hasan",
                'email' => "nahidul.hasan@bs-23.net",
                'phone' => "0173949449",
                'password' => '123456',
           ]
        ];

        DB::table('users')->insert($offers);


        // create 10 users using the user factory
        factory(App\Models\User::class, 10)->create();
    }
}