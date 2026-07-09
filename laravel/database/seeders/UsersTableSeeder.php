<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('users')->delete();
      DB::table('users')->insert([
          'name'     => env('ADMIN_NAME'),
          'username' => env('ADMIN_USERNAME'),
          'email'    => env('ADMIN_EMAIL'),
          'password' => Hash::make(env('ADMIN_PASSWORD')),
          'api_token' => Str::random(60),
          "created_at" =>  \Carbon\Carbon::now(),
          "updated_at" => \Carbon\Carbon::now(),
      ]);
    }
}
