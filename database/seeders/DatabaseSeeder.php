<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; 
use App\Models\Client;
use App\Models\Transaction;
// use Faker\Generator as Faker;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        //create admin user
        User::create([
        	"name" => "admin", 
        	"email" => "admin@admin.com",
        	"password" => bcrypt('password'),
            "api_token" => \Str::random(60)
        ]);

        //create 20 clients with 5 transactions each
        // $faker = \Faker::create();
        $faker = Faker::create();
        Client::factory(20)
        ->create()
        ->each(function($client) use($faker){
            //client transaction
            Transaction::factory(20)
                ->create([
                    'client' => $client->id
                ]);
 
        });
    }
}
