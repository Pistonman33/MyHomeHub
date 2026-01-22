<?php

namespace App\Livewire\Posts;

use Livewire\Component;
use App\Models\Post;
use App\Models\Term;
use App\Livewire\Forms\PostForm;
use Illuminate\Support\Str;

class PostEdit extends Component
{
    public PostForm $form;

    /** @var \Illuminate\Support\Collection */
    public $categories;
    public $tags;

    public array $selectedCategories = [];
    public array $selectedTags = [];

    public string $tagSearch = '';
    public array $tagSuggestions = [];

    public ?string $newCategoryName = null;

    /**
     * Mount
     */
    public function mount(?int $postId = null)
    {
        // Charger catégories & tags
        $this->categories = Term::where('type', 'category')->orderBy('name')->get();
        $this->tags       = Term::where('type', 'tag')->orderBy('name')->get();

        if ($postId) {
            $post = Post::with('terms')->findOrFail($postId);

            // remplir le formulaire
            $this->form->fillFromModel($post);

            // catégories sélectionnées
            $this->selectedCategories = $post->terms
                ->where('type', 'category')
                ->pluck('id')
                ->toArray();

            // tags sélectionnés
            $this->selectedTags = $post->terms
                ->where('type', 'tag')
                ->pluck('id')
                ->toArray();
        }
    }

    /**
     * Save
     */
    public function save()
    {
        $post = $this->form->save();

        // sync catégories + tags
        $post->terms()->sync(
            array_merge($this->selectedCategories, $this->selectedTags)
        );

        session()->flash('success', 'Post saved successfully');

        return redirect()->route('admin.blog.posts');
    }

    /* =========================
     |  Categories
     ========================= */

    public function addCategory()
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:255',
        ]);

        $slug = Str::slug($this->newCategoryName);

        $termExists = Term::where('slug', $slug)
            ->whereIn('type', ['tag', 'category'])
            ->exists();

        if ($termExists) {
            $this->addError(
                'categorySearch',
                'A tag or category already exists with this name.'
            );
            return;
        }


        $category = Term::create([
            'name' => $this->newCategoryName,
            'slug' => $slug,
            'type' => 'category',
        ]);

        $this->categories->push($category);
        $this->selectedCategories[] = $category->id;

        $this->newCategoryName = '';
    }

    /* =========================
     |  Tags (style WordPress)
     ========================= */

    public function updatedTagSearch()
    {
        if (strlen($this->tagSearch) < 2) {
            $this->tagSuggestions = [];
            return;
        }

        $this->tagSuggestions = Term::where('type', 'tag')
            ->where('name', 'like', '%' . $this->tagSearch . '%')
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function addExistingTag(int $tagId)
    {
        if (!in_array($tagId, $this->selectedTags)) {
            $this->selectedTags[] = $tagId;
        }

        $this->reset(['tagSearch', 'tagSuggestions']);
    }

    public function addTagFromInput()
    {
        $this->tagSearch = trim($this->tagSearch);    
        if (!$this->tagSearch) {
            return;
        }

        $slug = Str::slug($this->tagSearch);

        $termExists = Term::where('slug', $slug)
            ->whereIn('type', ['tag', 'category'])
            ->exists();

        if ($termExists) {
            $this->addError(
                'tagSearch',
                'A tag or category already exists with this name.'
            );
            return;
        }

        $tag = Term::firstOrCreate(
            ['name' => $this->tagSearch, 'type' => 'tag'],
            ['slug' => $slug]
        );

        $this->tags       = Term::where('type', 'tag')->orderBy('name')->get();

        $this->addExistingTag($tag->id);
    }

    public function removeTag(int $tagId)
    {
        $this->selectedTags = array_values(
            array_filter($this->selectedTags, fn ($id) => $id !== $tagId)
        );
    }

    public function render()
    {
        return view('livewire.posts.post-edit');
    }
}
