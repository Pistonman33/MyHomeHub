<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Post;
use App\Term;
use DB;
use Carbon\Carbon;

class ImportWordPressPosts extends Command
{
    protected $signature = 'import:wordpress';
    protected $description = 'Import WordPress posts, categories, and tags';

    public function handle()
    {
        
        // Clean database tables before import
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('post_term')->truncate(); 
        DB::table('posts')->truncate();
        DB::table('terms')->truncate();     
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info("Importation des articles WordPress...");

        // get all posts from wordpress database
        $wpPosts = DB::connection('wordpress')
                     ->table('rayufat_posts')
                     ->where('post_type', 'post')
                     ->whereIn('post_status', ['publish', 'draft'])
                     ->get();

        foreach ($wpPosts as $wpPost) {
            $status = match ($wpPost->post_status) {
                'publish' => 'published',
                'draft'   => 'draft',
                default   => 'draft',
            };

            // Create or update laravel posts based on slug
            $post = Post::updateOrCreate(
                ['slug' => $wpPost->post_name],
                [
                    'title' => $wpPost->post_title,
                    'content' => $wpPost->post_content,
                    'status' => $status,
                ]
            );
            $post->timestamps = false; // disable timestamps for this operation
            $post->created_at = Carbon::parse($wpPost->post_date);
            $post->updated_at = Carbon::parse($wpPost->post_modified);
            $post->save();

            // get categories and tags for the post
            $termIds = DB::connection('wordpress')
                        ->table('rayufat_term_relationships as tr')
                        ->join('rayufat_term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
                        ->join('rayufat_terms as t', 'tt.term_id', '=', 't.term_id')
                        ->where('tr.object_id', $wpPost->ID)
                        ->select('t.name', 't.slug', 'tt.taxonomy')
                        ->get();

            $termIdsLaravel = [];

            foreach ($termIds as $term) {
                // create term if not exists
                $termLaravel = Term::firstOrCreate(
                    ['slug' => $term->slug],
                    [
                        'name' => $term->name,
                        'type' => $term->taxonomy === 'category' ? 'category' : 'tag',
                    ]
                );
                $termIdsLaravel[] = $termLaravel->id;
            }

            // Sync terms with the post
            $post->terms()->sync($termIdsLaravel);

            $this->info("Importé : {$post->title}");
        }

        $this->info("Importation terminée !");
    }
}
