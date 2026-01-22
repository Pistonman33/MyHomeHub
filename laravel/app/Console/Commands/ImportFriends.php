<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Friend;
use App\Models\FriendGroup;
use DB;

class ImportFriends extends Command
{
    protected $signature = 'import:friends';
    protected $description = 'Import friends and friend groups from old Laravel database';

    /**
     * Execute the console command.
     */
    public function handle()
    {    

        // Clean database tables before import
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('friend_groups')->truncate(); 
        DB::table('friends')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $this->info("Importation des groupes d'amis...");
        
        // Get all friend groups from old laravel database
        $friend_groups = DB::connection('mysql')
                     ->table('groupe_amis')
                     ->get();

        foreach ($friend_groups as $friend_group) {
            // Create or Update Laravel row
            $friend = FriendGroup::updateOrCreate(
                [
                    'name' => $friend_group->nom,
                ]
            );

            $this->info("Importé : {$friend_group->nom}");
        }


        $this->info("Importation des amis...");
        
        // Get all friends from old laravel database
        $amis = DB::connection('mysql')
                     ->table('amis')
                     ->get();

        foreach ($amis as $ami) {
            // Create or Update Laravel row
            $friend = Friend::updateOrCreate(
                [
                    'lastname' => $ami->lastname,
                    'firstname' => $ami->firstname,
                    'birthdate' => $ami->birthdate,
                    'fk_id_friend_group' => $ami->fk_id_groupe_ami
                ]
            );

            $this->info("Importé : {$friend->firstname} {$friend->lastname}");
        }
        

    }
}
