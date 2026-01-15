<?php

use Illuminate\Database\Seeder;

class SerieTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('series')->delete();
        DB::table('series')->insert([
            'title' => 'Alias',
            'fk_id_support' => 1,            
            "created_at" =>  \Carbon\Carbon::now(),
            "updated_at" => \Carbon\Carbon::now(),
        ]);
        DB::table('series')->insert([
            'title' => 'Body Of Proof',
            'fk_id_support' => 1,            
            "created_at" =>  \Carbon\Carbon::now(),
            "updated_at" => \Carbon\Carbon::now(),
        ]);
        DB::table('series')->insert([
            'title' => 'Desperate Housewives',
            'fk_id_support' => 1,            
            "created_at" =>  \Carbon\Carbon::now(),
            "updated_at" => \Carbon\Carbon::now(),
        ]);
        DB::table('series')->insert([
            'title' => 'Grey\'s Anatomy',
            'fk_id_support' => 1,            
            "created_at" =>  \Carbon\Carbon::now(),
            "updated_at" => \Carbon\Carbon::now(),
        ]);
        DB::table('series')->insert([
            'title' => 'X-Files',
            'fk_id_support' => 1,            
            "created_at" =>  \Carbon\Carbon::now(),
            "updated_at" => \Carbon\Carbon::now(),
        ]);
    }
}
