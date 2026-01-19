<?php 

namespace App\Livewire\Posts;

use Livewire\Component;
use App\Post;
use App\Term;

class PostForm extends Component
{
    public $posts;
    public $categories;
    public $tags;
    public $selectedCategories = [];
    public $selectedTags = [];

    public $newCategoryName;
    public $newTagName;

    /**
     * Initialize component with optional post ID
     */
    public function mount($postId = null)
    {
        // get tags and category
        $this->categories = Term::where('type', 'category')->get();
        $this->tags = Term::where('type', 'tag')->get();

        if ($postId) {
            $this->posts = Post::findOrFail($postId);

            // Selected categories and tags for the post
            $this->selectedCategories = $this->posts->terms()
                                        ->where('type', 'category')
                                        ->pluck('id')
                                        ->toArray();

            $this->selectedTags = $this->posts->terms()
                                        ->where('type', 'tag')
                                        ->pluck('id')
                                        ->toArray();
        } else {
            $this->posts = new Post();
        }
    }

    /**
     * Save post 
     */
    public function save()
    {
        $this->validate([
            'posts.title' => 'required|string|max:255',
            'posts.content' => 'required',
            'posts.status' => 'required|in:draft,published',
            'selectedCategories' => 'required|array',
            'selectedTags' => 'nullable|array',
        ]);

        $this->posts->save();

        // Sync categories and tags by Term model
        $this->posts->terms()->sync(array_merge($this->selectedCategories, $this->selectedTags));

        session()->flash('success', 'Post saved successfully!');
        return redirect()->route('admin.blog.posts');
    }

    public function render()
    {
        return view('livewire.posts.post-form');
    }

    public function addCategory()
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:255',
        ]);

        $category = Term::create([
            'name' => $this->newCategoryName
        ]);

        $this->categories->push($category); 
        $this->selectedCategories[] = $category->id; 
        $this->newCategoryName = '';
    }

    public function addTag()
    {
        $this->validate([
            'newTagName' => 'required|string|max:255',
        ]);

        $tag = Term::create([
            'name' => $this->newTagName,
            'type' => 'tag',
        ]);

        $this->tags->push($tag);
        $this->selectedTags[] = $tag->id;
        $this->newTagName = '';
    }

}
