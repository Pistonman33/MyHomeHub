<?php

namespace App\Livewire\Frontend\Posts;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Post;

class PostsList extends Component
{
    use WithPagination;

    public $search = '';
    public $termId = null;

    protected $queryString = ['search', 'termId'];

    public function selectTerm($id)
    {
        $this->termId = $id;
        $this->resetPage(); 
    }

    public function render()
    {
        
        $query = Post::where('status', 'published')
                    ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhereHas('categories', function($q2) {
                      $q2->where('name', 'like', "%{$this->search}%");
                  })
                  ->orWhereHas('tags', function($q3) {
                      $q3->where('name', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->termId) {
            $query->whereHas('terms', function($q) {
                $q->where('terms.id', $this->termId);
            });
        }
        return view('livewire.frontend.posts.posts-list', [
            'posts' => $query->paginate(10)->withQueryString(),
        ]);
    }
}