<?php

namespace Tests\Feature\Frontend\Posts;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use App\Models\Post;
use App\Models\Term;

class PostsListTest extends TestCase
{
    use RefreshDatabase;
    
    /** Display published posts */
    public function test_displays_published_posts()
    {
        Post::factory()->create(['title' => 'Post publié', 'status' => 'published']);
        Post::factory()->create(['title' => 'Post brouillon', 'status' => 'draft']);

        Livewire::test('frontend.posts.posts-list')
            ->assertSee('Post publié')
            ->assertDontSee('Post brouillon');
    }
    
    /** Filter posts by search term */
    public function test_filters_posts_by_search_field()
    {
        Post::factory()->create(['title' => 'Laravel Testing', 'status' => 'published']);
        Post::factory()->create(['title' => 'Vue.js Guide', 'status' => 'published']);

        Livewire::test('frontend.posts.posts-list')
            ->set('search', 'Laravel')
            ->assertSee('Laravel Testing')
            ->assertDontSee('Vue.js Guide');
    }    
    /** Filter posts by search term in tags */
    public function test_filters_posts_by_search_by_tags()
    {
        $post1 = Post::factory()->create(['title' => 'Laravel Testing', 'status' => 'published']);
        $post2 = Post::factory()->create(['title' => 'Vue.js Guide', 'status' => 'published']);
        
        $tag1 = Term::factory()->create([
            'name' => 'PHP',
            'type' => 'tag',
        ]);

        $tag2 = Term::factory()->create([
            'name' => 'Framework',
            'type' => 'tag',
        ]);

        $tag3 = Term::factory()->create([
            'name' => 'Javascript',
            'type' => 'tag',
        ]);

        $post1->terms()->attach($tag1->id);
        $post1->terms()->attach($tag2->id);

        $post2->terms()->attach($tag2->id);
        $post2->terms()->attach($tag3->id);

        Livewire::test('frontend.posts.posts-list')
            ->set('search', 'PHP')
            ->assertSee('Laravel Testing')
            ->assertDontSee('Vue.js Guide');
        Livewire::test('frontend.posts.posts-list')
            ->set('search', 'Framework')
            ->assertViewHas('posts', function ($posts) {
                return $posts->count() === 2;
            });
    }    

    /** Filter posts by search term in categories */
    public function test_filters_posts_by_search_by_categories()
    {
        $post1 = Post::factory()->create(['title' => 'Laravel Testing', 'status' => 'published']);
        $post2 = Post::factory()->create(['title' => 'Vue.js Guide', 'status' => 'published']);
        
        $cat1 = Term::factory()->create([
            'name' => 'Développement Web',
            'type' => 'category',
        ]);

        $post1->terms()->attach($cat1->id);
        $post2->terms()->attach($cat1->id);

        Livewire::test('frontend.posts.posts-list')
            ->set('search', 'Développement')
            ->assertSee('Laravel Testing')
            ->assertSee('Vue.js Guide');
        Livewire::test('frontend.posts.posts-list')
            ->set('search', 'Web')
            ->assertViewHas('posts', function ($posts) {
                return $posts->count() === 2;
            });
    }  
}