<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Post;
use App\Models\Term;
use DB;

class ImportWordPressPosts extends Command
{
    protected $signature = 'import:wordpress';
    protected $description = 'Import WordPress posts, categories, and tags';

    public function handle()
    {
        $this->info("Importation des articles WordPress...");

        // Récupérer tous les posts WordPress
        $wpPosts = DB::connection('mysql')
                     ->table('wp_posts')
                     ->where('post_type', 'post')
                     ->whereIn('post_status', ['publish', 'draft'])
                     ->get();

        foreach ($wpPosts as $wpPost) {

            // Créer ou mettre à jour le post Laravel
            $post = Post::updateOrCreate(
                ['slug' => $wpPost->post_name],
                [
                    'title' => $wpPost->post_title,
                    'content' => $wpPost->post_content,
                    'status' => $wpPost->post_status,
                    'created_at' => $wpPost->post_date,
                    'updated_at' => $wpPost->post_modified
                ]
            );

            // Récupérer les catégories/tags pour ce post
            $termIds = DB::connection('mysql_wordpress')
                        ->table('wp_term_relationships as tr')
                        ->join('wp_term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
                        ->join('wp_terms as t', 'tt.term_id', '=', 't.term_id')
                        ->where('tr.object_id', $wpPost->ID)
                        ->select('t.name', 't.slug', 'tt.taxonomy')
                        ->get();

            $termIdsLaravel = [];

            foreach ($termIds as $term) {
                // Créer le term si pas existant
                $termLaravel = Term::firstOrCreate(
                    ['slug' => $term->slug],
                    [
                        'name' => $term->name,
                        'type' => $term->taxonomy // 'category' ou 'post_tag'
                    ]
                );
                $termIdsLaravel[] = $termLaravel->id;
            }

            // Synchroniser avec le post
            $post->terms()->sync($termIdsLaravel);

            $this->info("Importé : {$post->title}");
        }

        $this->info("Importation terminée !");
    }
}
